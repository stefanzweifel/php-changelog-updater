<?php

declare(strict_types=1);

namespace App\Actions;

use App\Support\Markdown;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;

class PrepareReleaseNotesAction
{
    public function __construct(private Markdown $markdown, private ShiftHeadingLevelInDocumentAction $shiftHeadingLevelInDocument)
    {
    }

    public function execute(string $releaseNotes, Heading $newReleaseHeading): Document
    {
        // Turn Release Notes into Markdown AST
        $parsedReleaseNotes = $this->markdown->parse($releaseNotes);

        // Shift Headings in Release Notes to be all atleast level 3
        $parsedReleaseNotes = $this->shiftHeadingLevelInDocument->execute(
            document: $parsedReleaseNotes,
            baseHeadingLevel: 3
        );

        // Add new release heading
        $parsedReleaseNotes->prependChild($newReleaseHeading);

        return $parsedReleaseNotes;
    }
}
