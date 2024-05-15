<?php

declare(strict_types=1);

namespace App\Actions;

use App\CreateNewReleaseHeading;
use App\CreateNewReleaseHeadingWithCompareUrl;
use App\Exceptions\ReleaseNotesNotProvidedException;
use App\GenerateCompareUrl;
use App\Queries\FindFirstSecondLevelHeading;
use App\Support\GitHubActionsOutput;
use Illuminate\Support\Str;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Block\Document;
use Throwable;

class PlaceReleaseNotesAtTheTopAction
{
    public function __construct(
        private readonly FindFirstSecondLevelHeading $findFirstSecondLevelHeading,
        private readonly CreateNewReleaseHeading $createNewReleaseHeading,
        private readonly InsertReleaseNotesInChangelogAction $insertReleaseNotesInChangelogAction,
        private readonly CreateNewReleaseHeadingWithCompareUrl $createNewReleaseHeadingWithCompareUrl,
        private readonly GenerateCompareUrl $generateCompareUrl,
        private readonly GitHubActionsOutput $gitHubActionsOutput,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function execute(Document $changelog, string $headingText, string $releaseDate, ?string $releaseNotes, bool $hideDate = false): Document
    {
        throw_if(empty($releaseNotes), ReleaseNotesNotProvidedException::class);

        // Find the Heading of the previous Version
        $previousVersionHeading = $this->findFirstSecondLevelHeading->find($changelog);

        // If the previous version heading contains a compare URL, we should use that to generate the new release heading
        // If the previous version does not contain a URL, don't add a URL to the new release heading
        if ($previousVersionHeading && $this->headingContainsLink($previousVersionHeading)) {

            $previousVersion = $this->getPreviousVersionFromUnreleasedHeading($previousVersionHeading);
            $repositoryUrl = $this->getRepositoryUrlFromUnreleasedHeading($previousVersionHeading);
            $updatedUrl = $this->generateCompareUrl->generate($repositoryUrl, $headingText, $compareUrlTargetRevision = 'HEAD');

            $link = clone $this->getLinkNodeFromHeading($previousVersionHeading);
            $link->setUrl($updatedUrl);
            $this->gitHubActionsOutput->add('UNRELEASED_COMPARE_URL', $updatedUrl);

            // Create new Heading containing the new version number
            $newReleaseHeading = $this->createNewReleaseHeadingWithCompareUrl->create($repositoryUrl, $previousVersion, $headingText, $headingText, $releaseDate, $hideDate);


            // Update Compare URL in Previous Version Heading to use `$headingText` as the target revision in the compare URL
            $repositoryUrl = $this->getRepositoryUrlFromUnreleasedHeading($previousVersionHeading);
            $updatedUrl = $this->generateCompareUrl->generate($repositoryUrl, $previousVersion, $headingText);

            $link = $this->getLinkNodeFromHeading($previousVersionHeading);
            $link->setUrl($updatedUrl);
        } else {
            $newReleaseHeading = $this->createNewReleaseHeading->create($headingText, $releaseDate, $hideDate);
        }

        return $this->insertReleaseNotesInChangelogAction->execute(
            changelog: $changelog,
            releaseNotes: $releaseNotes,
            newReleaseHeading: $newReleaseHeading,
            previousVersionHeading: $previousVersionHeading
        );
    }

    private function headingContainsLink(Heading $previousVersionHeading): bool
    {
        $child = $previousVersionHeading->firstChild();

        return $child instanceof Link;
    }

    /**
     * @throws Throwable
     */
    private function getPreviousVersionFromUnreleasedHeading(Heading $unreleasedHeading): ?string
    {
        $linkNode = $this->getLinkNodeFromHeading($unreleasedHeading);

        return Str::of($linkNode->getUrl())
            ->afterLast('/')
            ->explode('...')
            ->first();
    }

    /**
     * @throws Throwable
     */
    private function getRepositoryUrlFromUnreleasedHeading(Heading $unreleasedHeading): string
    {
        $linkNode = $this->getLinkNodeFromHeading($unreleasedHeading);

        return Str::of($linkNode->getUrl())
            ->before('/compare')
            ->__toString();
    }

    private function getLinkNodeFromHeading(Heading $unreleasedHeading): Link
    {
        /** @var Link $linkNode */
        $linkNode = $unreleasedHeading->firstChild();

        return $linkNode;
    }
}
