<?php

declare(strict_types=1);

namespace App\Actions;

use App\Support\GitHubActionsOutput;
use DOMDocument;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkProcessor;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Renderer\HtmlRenderer;

class ExtractPermalinkFragmentFromHeading
{
    private GitHubActionsOutput $gitHubActionsOutput;

    public function __construct(GitHubActionsOutput $gitHubActionsOutput)
    {
        $this->gitHubActionsOutput = $gitHubActionsOutput;
    }

    public function execute(Heading $releaseHeading): string
    {
        $releaseHeading = clone $releaseHeading;

        $renderedHtml = $this->attachPermalinkAndRenderAsHtml($releaseHeading);
        $linkFragment = $this->extractLinkFragmentFromRenderedHtml($renderedHtml);

        return tap($linkFragment, function (string $linkFragment) {
            $this->gitHubActionsOutput->add('RELEASE_URL_FRAGMENT', $linkFragment);
        });
    }

    protected function attachPermalinkAndRenderAsHtml(Heading $releaseHeading): string
    {
        $environment = $this->prepareCommonmarkEnvironment();

        $document = $this->attachPermalinkToHeading($releaseHeading, $environment);

        $renderer = new HtmlRenderer($environment);

        return $renderer->renderDocument($document)->getContent();
    }

    protected function prepareCommonmarkEnvironment(): Environment
    {
        $config = [
            'heading_permalink' => [
                'html_class' => '',
                'id_prefix' => '',
                'fragment_prefix' => '',
                'insert' => 'before',
                'min_heading_level' => 1,
                'max_heading_level' => 6,
                'title' => '',
                'symbol' => HeadingPermalinkRenderer::DEFAULT_SYMBOL,
                'aria_hidden' => false,
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        return $environment;
    }

    /**
     * Attach Heading Permalink to given ReleaseHeading using
     * the Commonmark Heading Permalink Extension.
     * @param Heading $releaseHeading
     * @param Environment $environment
     * @return Document
     */
    protected function attachPermalinkToHeading(Heading $releaseHeading, Environment $environment): Document
    {
        $document = new Document();
        $document->appendChild($releaseHeading);

        $documentParsedEvent = new DocumentParsedEvent($document);

        $processor = (new HeadingPermalinkProcessor());
        $processor->setEnvironment($environment);
        $processor->__invoke($documentParsedEvent);

        return $document;
    }

    /**
     * Parse the rendered HTML as a DOM Document and extract the
     * href attribute from the generated a-tag.
     * @param string $html
     * @return string|null
     */
    protected function extractLinkFragmentFromRenderedHtml(string $html): ?string
    {
        $domDocument = new DOMDocument();
        $domDocument->loadHTML($html);

        return $domDocument
            ->getElementsByTagName('a')
            ->item(0)
            ->attributes
            ->getNamedItem('href')
            ->value;
    }
}
