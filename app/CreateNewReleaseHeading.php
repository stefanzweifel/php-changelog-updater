<?php

declare(strict_types=1);

namespace App;

use App\Actions\ExtractPermalinkFragmentFromHeading;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Inline\Text;

class CreateNewReleaseHeading
{
    public function __construct(private ExtractPermalinkFragmentFromHeading $extractPermalinkFragmentFromHeading)
    {
    }

    public function create(string $text, string $releaseDate): Heading
    {
        return tap(new Heading(2), function (Heading $heading) use ($text, $releaseDate) {
            $heading->appendChild(new Text($text));
            $heading->appendChild(new Text(" - {$releaseDate}"));

            $this->extractPermalinkFragmentFromHeading->execute($heading);
        });
    }
}
