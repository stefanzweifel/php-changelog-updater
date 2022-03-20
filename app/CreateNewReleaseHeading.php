<?php

declare(strict_types=1);

namespace App;

use App\Actions\ExtractPermalinkFragmentFromHeading;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Inline\Text;

class CreateNewReleaseHeading
{
    private ExtractPermalinkFragmentFromHeading $extractPermalinkFragmentFromHeading;

    public function __construct(ExtractPermalinkFragmentFromHeading $extractPermalinkFragmentFromHeading)
    {
        $this->extractPermalinkFragmentFromHeading = $extractPermalinkFragmentFromHeading;
    }

    public function create(string $latestVersion, string $releaseDate): Heading
    {
        return tap(new Heading(2), function (Heading $heading) use ($latestVersion, $releaseDate) {
            $heading->appendChild(new Text($latestVersion));
            $heading->appendChild(new Text(" - {$releaseDate}"));

            $this->extractPermalinkFragmentFromHeading->execute($heading);
        });
    }
}
