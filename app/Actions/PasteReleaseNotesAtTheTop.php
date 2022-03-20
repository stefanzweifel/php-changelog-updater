<?php

declare(strict_types=1);

namespace App\Actions;

use App\CreateNewReleaseHeading;
use App\Exceptions\ReleaseNotesNotProvidedException;
use App\MarkdownParser;
use App\Queries\FindFirstSecondLevelHeading;
use League\CommonMark\Node\Block\Document;
use Throwable;

class PasteReleaseNotesAtTheTop
{
    private FindFirstSecondLevelHeading $findFirstSecondLevelHeading;
    private MarkdownParser $parser;
    private CreateNewReleaseHeading $createNewReleaseHeading;

    public function __construct(FindFirstSecondLevelHeading $findFirstSecondLevelHeading, MarkdownParser $markdownParser, CreateNewReleaseHeading $createNewReleaseHeading)
    {
        $this->findFirstSecondLevelHeading = $findFirstSecondLevelHeading;
        $this->parser = $markdownParser;
        $this->createNewReleaseHeading = $createNewReleaseHeading;
    }

    /**
     * @throws Throwable
     */
    public function execute(string $latestVersion, ?string $releaseNotes, string $releaseDate, Document $changelog): Document
    {
        throw_if(empty($releaseNotes), ReleaseNotesNotProvidedException::class);

        $newReleaseHeading = $this->createNewReleaseHeading->create($latestVersion, $releaseDate);

        // Prepend the new Release Heading to the Release Notes
        $parsedReleaseNotes = $this->parser->parse($releaseNotes);
        $parsedReleaseNotes->prependChild($newReleaseHeading);

        // Find the Heading of the previous Version
        $previousVersionHeading = $this->findFirstSecondLevelHeading->find($changelog);

        if ($previousVersionHeading !== null) {
            // Insert the newest Release Notes before the previous Release Heading
            $previousVersionHeading->insertBefore($parsedReleaseNotes);
        } else {
            $changelog->lastChild()?->insertAfter($parsedReleaseNotes);
        }

        return $changelog;
    }
}
