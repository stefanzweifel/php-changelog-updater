<?php

declare(strict_types=1);

namespace App\Actions;

use App\CreateNewReleaseHeading;
use App\Exceptions\ReleaseNotesCanNotBeplacedException;
use App\Exceptions\ReleaseNotesNotProvidedException;
use App\Queries\FindFirstSecondLevelHeading;
use League\CommonMark\Node\Block\Document;
use Throwable;

class PasteReleaseNotesAtTheTopAction
{
    public function __construct(
        private FindFirstSecondLevelHeading       $findFirstSecondLevelHeading,
        private CreateNewReleaseHeading           $createNewReleaseHeading,
        private PrepareReleaseNotesAction $prepareReleaseNotesAction,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function execute(Document $changelog, string $latestVersion, string $releaseDate, ?string $releaseNotes): Document
    {
        throw_if(empty($releaseNotes), ReleaseNotesNotProvidedException::class);

        $newReleaseHeading = $this->createNewReleaseHeading->create($latestVersion, $releaseDate);

        $parsedReleaseNotes = $this->prepareReleaseNotesAction->execute($releaseNotes, $newReleaseHeading);

        // Find the Heading of the previous Version
        $previousVersionHeading = $this->findFirstSecondLevelHeading->find($changelog);

        if ($previousVersionHeading !== null) {
            // Insert the newest Release Notes before the previous Release Heading
            $previousVersionHeading->insertBefore($parsedReleaseNotes);
        } elseif ($changelog->lastChild() !== null) {
            $changelog->lastChild()->insertAfter($parsedReleaseNotes);
        } else {
            throw new ReleaseNotesCanNotBeplacedException();
        }

        return $changelog;
    }
}
