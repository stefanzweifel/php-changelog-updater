<?php

declare(strict_types=1);

namespace App\Actions;

use App\Support\GitHubActionsOutput;
use DOMDocument;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkProcessor;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\HtmlRenderer;

class ExtractNewReleaseHeadingFragmentAction
{
    private GitHubActionsOutput $gitHubActionsOutput;

    public function __construct(GitHubActionsOutput $gitHubActionsOutput)
    {
        $this->gitHubActionsOutput = $gitHubActionsOutput;
    }

    public function execute(Node $releaseHeading): string
    {
        $releaseHeading = clone $releaseHeading;

        $renderedHtml = $this->attachHeadingPermalinkAndParseAsHtml($releaseHeading);
        $linkFragment = $this->extractLinkFragmentFromRenderedHtml($renderedHtml);

        return tap($linkFragment, function (string $linkFragment) {
            $this->gitHubActionsOutput->add('RELEASE_URL_FRAGMENT', $linkFragment);
        });
    }

    protected function attachHeadingPermalinkAndParseAsHtml(Node $releaseHeading): string
    {
        $environment = $this->prepareCommonmarkEnvironment();

        $document = $this->attachHeadingPermalink($releaseHeading, $environment);

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

    protected function attachHeadingPermalink(Node $releaseHeading, Environment $environment): Document
    {
        $document = new Document();
        $document->appendChild($releaseHeading);

        $documentParsedEvent = new DocumentParsedEvent($document);

        $processor = (new HeadingPermalinkProcessor());
        $processor->setEnvironment($environment);
        $processor->__invoke($documentParsedEvent);

        return $document;
    }

    protected function extractLinkFragmentFromRenderedHtml(string $result): ?string
    {
        $domDocument = new DOMDocument();
        $domDocument->loadHTML($result);

        return $domDocument
            ->getElementsByTagName('a')
            ->item(0)
            ->attributes
            ->getNamedItem('href')
            ->value;
    }
}
