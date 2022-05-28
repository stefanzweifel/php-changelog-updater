<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\ReleaseAlreadyExistsInChangelogException;
use App\MarkdownParser;
use App\MarkdownRenderer;
use App\Queries\FindSecondLevelHeadingWithText;
use App\Queries\FindUnreleasedHeading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContentInterface;
use Throwable;

class AddReleaseNotesToChangelog
{
    public function __construct(
        private MarkdownParser                          $markdownParser,
        private MarkdownRenderer                        $markdownRenderer,
        private FindUnreleasedHeading                   $findUnreleasedHeading,
        private FindSecondLevelHeadingWithText          $findSecondLevelHeadingWithText,
        private PasteReleaseNotesBelowUnreleasedHeading $pasteReleaseNotesBelowUnreleasedHeading,
        private PasteReleaseNotesAtTheTop $pasteReleaseNotesAtTheTop
    ) {
    }

    /**
     * @throws Throwable
     */
    public function execute(string $originalChangelog, ?string $releaseNotes, string $latestVersion, string $releaseDate, string $compareUrlTargetRevision): RenderedContentInterface
    {
        $changelog = $this->markdownParser->parse($originalChangelog);

        $this->checkIfVersionAlreadyExistsInChangelog($changelog, $latestVersion);

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
