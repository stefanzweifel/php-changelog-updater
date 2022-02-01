<?php

declare(strict_types=1);

namespace App\Actions;

use App\MarkdownParser;
use App\MarkdownRenderer;
use App\Queries\FindUnreleasedHeading;
use League\CommonMark\Output\RenderedContentInterface;
use Throwable;

class AddReleaseNotesToChangelog
{
    private MarkdownParser $markdownParser;
    private MarkdownRenderer $markdownRenderer;
    private FindUnreleasedHeading $findUnreleasedHeading;
    private PasteReleaseNotesBelowUnreleasedHeading $pasteReleaseNotesBelowUnreleasedHeading;
    private PasteReleaseNotesAtTheTop $pasteReleaseNotesAtTheTop;

    public function __construct(
        MarkdownParser $markdownParser,
        MarkdownRenderer $markdownRenderer,
        FindUnreleasedHeading $findUnreleasedHeading,
        PasteReleaseNotesBelowUnreleasedHeading $pasteReleaseNotesBelowUnreleasedHeading,
        PasteReleaseNotesAtTheTop $pasteReleaseNotesAtTheTop
    ) {
        $this->markdownParser = $markdownParser;
        $this->markdownRenderer = $markdownRenderer;
        $this->findUnreleasedHeading = $findUnreleasedHeading;
        $this->pasteReleaseNotesBelowUnreleasedHeading = $pasteReleaseNotesBelowUnreleasedHeading;
        $this->pasteReleaseNotesAtTheTop = $pasteReleaseNotesAtTheTop;
    }

    /**
     * @throws Throwable
     */
    public function execute(string $originalChangelog, string $releaseNotes, string $latestVersion, string $releaseDate, string $compareUrlTargetRevision): RenderedContentInterface
    {
        $changelog = $this->markdownParser->parse($originalChangelog);

        $unreleasedHeading = $this->findUnreleasedHeading->find($changelog);

        if ($unreleasedHeading !== null) {
            $changelog = $this->pasteReleaseNotesBelowUnreleasedHeading->execute(
                unreleasedHeading: $unreleasedHeading,
                latestVersion: $latestVersion,
                releaseDate: $releaseDate,
                releaseNotes: $releaseNotes,
                changelog: $changelog,
                compareUrlTargetRevision: $compareUrlTargetRevision
            );
        } else {
            $changelog = $this->pasteReleaseNotesAtTheTop->execute($latestVersion, $releaseNotes, $releaseDate, $changelog);
        }

        return $this->markdownRenderer->render($changelog);
    }
}
