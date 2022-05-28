<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\ReleaseNotesCanNotBeplacedException;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;

class InsertReleaseNotesInChangelogAction
{
    /**
     * @throws ReleaseNotesCanNotBeplacedException
     */
    public function execute(Document $changelog, Document $parsedReleaseNotes, ?Heading $previousVersionHeading): Document
    {
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
