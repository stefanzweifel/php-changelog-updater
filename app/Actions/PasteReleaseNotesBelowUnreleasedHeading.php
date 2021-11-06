<?php

namespace App\Actions;

use App\CreateNewReleaseHeading;
use App\GenerateCompareUrl;
use App\MarkdownParser;
use App\Queries\FindPreviousVersionHeading;
use Illuminate\Support\Str;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Block\Document;
use LogicException;
use Throwable;

class PasteReleaseNotesBelowUnreleasedHeading
{
    private MarkdownParser $parser;
    private GenerateCompareUrl $generateCompareUrl;
    private FindPreviousVersionHeading $findPreviousVersionHeading;
    private CreateNewReleaseHeading $createNewReleaseHeading;

    public function __construct(
        MarkdownParser $markdownParser,
        GenerateCompareUrl $generateCompareUrl,
        FindPreviousVersionHeading $findPreviousVersionHeading,
        CreateNewReleaseHeading $createNewReleaseHeading
    ) {
        $this->parser = $markdownParser;
        $this->generateCompareUrl = $generateCompareUrl;
        $this->findPreviousVersionHeading = $findPreviousVersionHeading;
        $this->createNewReleaseHeading = $createNewReleaseHeading;
    }

    /**
     * @param Heading $unreleasedHeading
     * @param string $latestVersion
     * @param string $releaseDate
     * @param string $releaseNotes
     * @param Document $changelog
     * @return Document
     * @throws Throwable
     */
    public function execute(Heading $unreleasedHeading, string $latestVersion, string $releaseDate, string $releaseNotes, Document $changelog): Document
    {
        $previousVersion = $this->getPreviousVersionFromUnreleasedHeading($unreleasedHeading);
        $repositoryUrl = $this->getRepositoryUrlFromUnreleasedHeading($unreleasedHeading);
        $updatedUrl = $this->generateCompareUrl->generate($repositoryUrl, $latestVersion, 'HEAD');

        $link = $this->getLinkNodeFromHeading($unreleasedHeading);
        $link->setUrl($updatedUrl);

        // Create new Heading containing the new version number
        $newReleaseHeading = $this->createNewReleaseHeading->create($repositoryUrl, $previousVersion, $latestVersion, $releaseDate);

        // Prepend the new Release Heading to the Release Notes
        $parsedReleaseNotes = $this->parser->parse($releaseNotes);
        $parsedReleaseNotes->prependChild($newReleaseHeading);

        // Find the Heading of the previous Version
        $previousVersionHeading = $this->findPreviousVersionHeading->find($changelog, $previousVersion);

        // Insert the newest Release Notes before the previous Release Heading
        $previousVersionHeading?->insertBefore($parsedReleaseNotes);

        return $changelog;
    }

    /**
     * @param Heading $unreleasedHeading
     * @return string
     * @throws Throwable
     */
    private function getPreviousVersionFromUnreleasedHeading(Heading $unreleasedHeading): string
    {
        $linkNode = $this->getLinkNodeFromHeading($unreleasedHeading);

        return Str::of($linkNode->getUrl())
            ->afterLast('/')
            ->explode('...')
            ->first();
    }

    /**
     * @param Heading $unreleasedHeading
     * @return string
     * @throws Throwable
     */
    private function getRepositoryUrlFromUnreleasedHeading(Heading $unreleasedHeading): string
    {
        $linkNode = $this->getLinkNodeFromHeading($unreleasedHeading);

        return Str::of($linkNode->getUrl())
            ->before('/compare')
            ->__toString();
    }

    /**
     * @param Heading $unreleasedHeading
     * @return Link
     * @throws Throwable
     */
    private function getLinkNodeFromHeading(Heading $unreleasedHeading): Link
    {
        /** @var Link $linkNode */
        $linkNode = $unreleasedHeading->firstChild();

        throw_if($linkNode === null, new LogicException("Can not find link node in unreleased heading."));

        return $linkNode;
    }
}
