<?php

namespace App;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Inline\Text;

class CreateNewReleaseHeading
{
    public function create(string $latestVersion, string $releaseDate): Heading
    {
        return tap(new Heading(2), function (Heading $heading) use ($latestVersion, $releaseDate) {
            $heading->appendChild(new Text($latestVersion));
            $heading->appendChild(new Text(" - {$releaseDate}"));
        });
    }
}
