<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\ReleaseAlreadyExistsInChangelogException;
use App\Queries\FindSecondLevelHeadingWithText;
use App\Queries\FindUnreleasedHeading;
use App\Support\Markdown;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContentInterface;
use Throwable;

class AddReleaseNotesToChangelogAction
{
    public function __construct(
        private Markdown                                      $markdown,
        private FindUnreleasedHeading                         $findUnreleasedHeading,
        private FindSecondLevelHeadingWithText                $findSecondLevelHeadingWithText,
        private PlaceReleaseNotesBelowUnreleasedHeadingAction $addNewReleaseNotesWithUnreleasedHeadingToChangelog,
        private PlaceReleaseNotesAtTheTopAction               $addNewReleaseToChangelog
    ) {
    }

    /**
     * @throws Throwable
     */
    public function execute(string $originalChangelog, string $latestVersion, string $headingText, ?string $releaseNotes, string $releaseDate, string $compareUrlTargetRevision): RenderedContentInterface
    {
        $changelog = $this->markdown->parse($originalChangelog);

        $this->checkIfVersionAlreadyExistsInChangelog($changelog, $latestVersion);

        $unreleasedHeading = $this->findUnreleasedHeading->find($changelog);

        if ($unreleasedHeading !== null) {
            $changelog = $this->addNewReleaseNotesWithUnreleasedHeadingToChangelog->execute(
                unreleasedHeading: $unreleasedHeading,
                latestVersion: $latestVersion,
                headingText: $headingText,
                releaseDate: $releaseDate,
                releaseNotes: $releaseNotes,
                changelog: $changelog,
                compareUrlTargetRevision: $compareUrlTargetRevision
            );
        } else {
            $changelog = $this->addNewReleaseToChangelog->execute(
                changelog: $changelog,
                latestVersion: $latestVersion,
                headingText: $headingText,
                releaseDate: $releaseDate,
                releaseNotes: $releaseNotes
            );
        }

        return $this->markdown->render($changelog);
    }

    /**
     * Check if a second-level heading for the latestVersion already exists in the document.
     * @throws ReleaseAlreadyExistsInChangelogException|Throwable
     */
    private function checkIfVersionAlreadyExistsInChangelog(Document $changelog, string $latestVersion): void
    {
        $result = $this->findSecondLevelHeadingWithText->find($changelog, $latestVersion);

        throw_unless(is_null($result), new ReleaseAlreadyExistsInChangelogException($latestVersion));
    }
}
