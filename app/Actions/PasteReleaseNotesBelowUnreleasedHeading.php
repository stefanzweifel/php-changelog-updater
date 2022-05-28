<?php

declare(strict_types=1);

namespace App\Actions;

use App\CreateNewReleaseHeadingWithCompareUrl;
use App\GenerateCompareUrl;
use App\Queries\FindSecondLevelHeadingWithText;
use App\Support\GitHubActionsOutput;
use App\Support\Markdown;
use Illuminate\Support\Str;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Block\Document;
use LogicException;
use Throwable;

class PasteReleaseNotesBelowUnreleasedHeading
{
    private GenerateCompareUrl $generateCompareUrl;
    private FindSecondLevelHeadingWithText $findPreviousVersionHeading;
    private CreateNewReleaseHeadingWithCompareUrl $createNewReleaseHeading;
    private GitHubActionsOutput $gitHubActionsOutput;
    private ShiftHeadingLevelInDocument $shiftHeadingLevelInDocument;
    private Markdown $markdown;

    public function __construct(
        GenerateCompareUrl                    $generateCompareUrl,
        FindSecondLevelHeadingWithText        $findPreviousVersionHeading,
        CreateNewReleaseHeadingWithCompareUrl $createNewReleaseHeading,
        GitHubActionsOutput                   $gitHubActionsOutput,
        ShiftHeadingLevelInDocument $shiftHeadingLevelInDocument,
        Markdown $markdown
    ) {
        $this->generateCompareUrl = $generateCompareUrl;
        $this->findPreviousVersionHeading = $findPreviousVersionHeading;
        $this->createNewReleaseHeading = $createNewReleaseHeading;
        $this->gitHubActionsOutput = $gitHubActionsOutput;
        $this->shiftHeadingLevelInDocument = $shiftHeadingLevelInDocument;
        $this->markdown = $markdown;
    }

    /**
     * @throws Throwable
     */
    public function execute(Heading $unreleasedHeading, string $latestVersion, string $releaseDate, ?string $releaseNotes, Document $changelog, string $compareUrlTargetRevision): Document
    {
        $previousVersion = $this->getPreviousVersionFromUnreleasedHeading($unreleasedHeading);
        $repositoryUrl = $this->getRepositoryUrlFromUnreleasedHeading($unreleasedHeading);
        $updatedUrl = $this->generateCompareUrl->generate($repositoryUrl, $latestVersion, $compareUrlTargetRevision);

        $link = $this->getLinkNodeFromHeading($unreleasedHeading);
        $link->setUrl($updatedUrl);
        $this->gitHubActionsOutput->add('UNRELEASED_COMPARE_URL', $updatedUrl);

        // Create new Heading containing the new version number
        $newReleaseHeading = $this->createNewReleaseHeading->create($repositoryUrl, $previousVersion, $latestVersion, $releaseDate);

        if (empty($releaseNotes)) {
            // If no Release Notes have been passed, add the new Release Heading below the updated Unreleased Heading.
            // We assume that the user already added their release notes under the Unreleased Heading.
            $unreleasedHeading->insertAfter($newReleaseHeading);
        } else {

            // Prepend the new Release Heading to the Release Notes
            $parsedReleaseNotes = $this->markdown->parse($releaseNotes);
            $parsedReleaseNotes = $this->shiftHeadingLevelInDocument->execute(
                document: $parsedReleaseNotes,
                baseHeadingLevel: 3
            );

            $parsedReleaseNotes->prependChild($newReleaseHeading);

            // Find the Heading of the previous Version
            $previousVersionHeading = $this->findPreviousVersionHeading->find($changelog, $previousVersion);

            if ($previousVersionHeading !== null) {
                // Insert the newest Release Notes before the previous Release Heading
                $previousVersionHeading->insertBefore($parsedReleaseNotes);
            } else {
                $changelog->lastChild()?->insertAfter($parsedReleaseNotes);
            }
        }

        return $changelog;
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

    /**
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
