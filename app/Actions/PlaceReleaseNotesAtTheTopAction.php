<?php

declare(strict_types=1);

namespace App\Actions;

use App\CreateNewReleaseHeading;
use App\Exceptions\ReleaseNotesNotProvidedException;
use App\Queries\FindFirstSecondLevelHeading;
use League\CommonMark\Node\Block\Document;
use Throwable;

class PlaceReleaseNotesAtTheTopAction
{
    public function __construct(
        private FindFirstSecondLevelHeading       $findFirstSecondLevelHeading,
        private CreateNewReleaseHeading           $createNewReleaseHeading,
        private InsertReleaseNotesInChangelogAction $insertReleaseNotesInChangelogAction
    ) {
    }

    /**
     * @throws Throwable
     */
    public function execute(Document $changelog, string $headingText, string $releaseDate, ?string $releaseNotes): Document
    {
        throw_if(empty($releaseNotes), ReleaseNotesNotProvidedException::class);

        $newReleaseHeading = $this->createNewReleaseHeading->create($headingText, $releaseDate);

        // Find the Heading of the previous Version
        $previousVersionHeading = $this->findFirstSecondLevelHeading->find($changelog);

        return $this->insertReleaseNotesInChangelogAction->execute(
            changelog: $changelog,
            releaseNotes: $releaseNotes,
            newReleaseHeading: $newReleaseHeading,
            previousVersionHeading: $previousVersionHeading
        );
    }
}
