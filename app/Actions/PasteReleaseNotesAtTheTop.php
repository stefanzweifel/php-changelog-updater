<?php

declare(strict_types=1);

namespace App\Actions;

use App\MarkdownParser;
use App\Queries\FindFirstSecondLevelHeading;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Inline\Text;

class PasteReleaseNotesAtTheTop
{
    private FindFirstSecondLevelHeading $findFirstSecondLevelHeading;
    private MarkdownParser $parser;

    public function __construct(FindFirstSecondLevelHeading $findFirstSecondLevelHeading, MarkdownParser $markdownParser)
    {
        $this->findFirstSecondLevelHeading = $findFirstSecondLevelHeading;
        $this->parser = $markdownParser;
    }

    public function execute(string $latestVersion, string $releaseNotes, string $releaseDate, Document $changelog): Document
    {
        // Create new Heading containing the new version and date
        $newReleaseHeading = $this->createNewReleaseHeading($latestVersion, $releaseDate);

        // Prepend the new Release Heading to the Release Notes
        $parsedReleaseNotes = $this->parser->parse($releaseNotes);
        $parsedReleaseNotes->prependChild($newReleaseHeading);

        // Find the Heading of the previous Version
        $previousVersionHeading = $this->findFirstSecondLevelHeading->find($changelog);

        if ($previousVersionHeading === null) {
            $changelog->lastChild()?->insertAfter($parsedReleaseNotes);
        } else {
            // Insert the newest Release Notes before the previous Release Heading
            $previousVersionHeading?->insertBefore($parsedReleaseNotes);
        }

        return $changelog;
    }

    protected function createNewReleaseHeading(string $latestVersion, string $releaseDate): Heading
    {
        return tap(new Heading(2), function (Heading $heading) use ($latestVersion, $releaseDate) {
            $heading->appendChild(new Text($latestVersion));
            $heading->appendChild(new Text(" - {$releaseDate}"));
        });
    }
}
