<?php

declare(strict_types=1);

namespace App;

use App\Actions\ExtractNewReleaseHeadingFragmentAction;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Inline\Text;

class CreateNewReleaseHeading
{
    private ExtractNewReleaseHeadingFragmentAction $extractNewReleaseHeadingFragmentAction;

    public function __construct(ExtractNewReleaseHeadingFragmentAction $extractNewReleaseHeadingFragmentAction)
    {
        $this->extractNewReleaseHeadingFragmentAction = $extractNewReleaseHeadingFragmentAction;
    }

    public function create(string $latestVersion, string $releaseDate): Heading
    {
        return tap(new Heading(2), function (Heading $heading) use ($latestVersion, $releaseDate) {
            $heading->appendChild(new Text($latestVersion));
            $heading->appendChild(new Text(" - {$releaseDate}"));

            $this->extractNewReleaseHeadingFragmentAction->execute($heading);
        });
    }
}
