<?php

declare(strict_types=1);

namespace App\Actions;

use App\CreateNewReleaseHeading;
use App\FindPreviousVersionHeading;
use App\FindUnreleasedHeading;
use App\GenerateCompareUrl;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Node;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Parser\MarkdownParser;
use Wnx\CommonmarkMarkdownRenderer\MarkdownRendererExtension;
use Wnx\CommonmarkMarkdownRenderer\Renderer\MarkdownRenderer;

class AddReleaseNotesToChangelog
{
    private MarkdownParser $parser;

    private MarkdownRenderer $renderer;
    private FindUnreleasedHeading $findUnreleasedHeading;
    private GenerateCompareUrl $generateCompareUrl;
    private FindPreviousVersionHeading $findPreviousVersionHeading;
    private CreateNewReleaseHeading $createNewReleaseHeading;

    public function __construct(Environment $environment, FindUnreleasedHeading $findUnreleasedHeading, GenerateCompareUrl $generateCompareUrl, FindPreviousVersionHeading $findPreviousVersionHeading, CreateNewReleaseHeading $createNewReleaseHeading)
    {
        $environment->addExtension(new MarkdownRendererExtension());

        $this->parser = new MarkdownParser($environment);
        $this->renderer = new MarkdownRenderer($environment);
        $this->findUnreleasedHeading = $findUnreleasedHeading;
        $this->generateCompareUrl = $generateCompareUrl;
        $this->findPreviousVersionHeading = $findPreviousVersionHeading;
        $this->createNewReleaseHeading = $createNewReleaseHeading;
    }

    /**
     * @param string $originalChangelog
     * @param string $releaseNotes
     * @param string $latestVersion
     * @param string $releaseDate
     * @return RenderedContentInterface
     * @throws \Throwable
     */
    public function execute(string $originalChangelog, string $releaseNotes, string $latestVersion, string $releaseDate): RenderedContentInterface
    {
        $changelog = $this->parser->parse($originalChangelog);

        $unreleasedHeading = $this->findUnreleasedHeading->find($changelog);

        $previousVersion = $this->getPreviousVersionFromUnreleasedHeading($unreleasedHeading);

        $repositoryUrl = $this->getRepositoryUrlFromUnreleasedHeading($unreleasedHeading);

        $updatedUrl = $this->generateCompareUrl->generate($repositoryUrl, $latestVersion, 'HEAD');

        $this->updateUrlOnUnreleasedHeading($unreleasedHeading, $updatedUrl);


        // Create new Heading containing the new version number
        $newReleaseHeading = $this->createNewReleaseHeading->create($repositoryUrl, $previousVersion, $latestVersion, $releaseDate);

        // Prepend the new Release Heading to the Release Notes
        $parsedReleaseNotes = $this->parser->parse($releaseNotes);
        $parsedReleaseNotes->prependChild($newReleaseHeading);


        // Find the Heading of the previous Version
        $previousVersionHeading = $this->findPreviousVersionHeading->find($changelog, $previousVersion);

        // Insert the newest Release Notes before the previous Release Heading
        $previousVersionHeading?->insertBefore($parsedReleaseNotes);

        return $this->renderer->renderDocument($changelog);
    }

    private function updateUrlOnUnreleasedHeading(Node $matchingNodes, string $url): void
    {
        /** @var Link $link */
        $link = $matchingNodes->firstChild();
        $link->setUrl($url);
    }

    /**
     * @param Heading $unreleasedHeading
     * @return string
     * @throws \Throwable
     */
    private function getPreviousVersionFromUnreleasedHeading(Heading $unreleasedHeading): string
    {
        /** @var Link $linkNode */
        $linkNode = $unreleasedHeading->firstChild();

        throw_if($linkNode === null, new \LogicException("Can not find link node in unreleased heading."));

        return Str::of($linkNode->getUrl())
            ->afterLast('/')
            ->explode('...')
            ->first();
    }

    private function getRepositoryUrlFromUnreleasedHeading(Heading $unreleasedHeading): string
    {
        /** @var Link $linkNode */
        $linkNode = $unreleasedHeading->firstChild();

        throw_if($linkNode === null, new \LogicException("Can not find link node in unreleased heading."));

        return (string) Str::of($linkNode->getUrl())
            ->before('/compare');
    }
}
