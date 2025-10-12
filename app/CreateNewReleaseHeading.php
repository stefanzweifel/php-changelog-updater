<?php

declare(strict_types=1);

namespace App;

use App\Actions\ExtractPermalinkFragmentFromHeadingAction;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Inline\Text;

class CreateNewReleaseHeading
{
    public function __construct(private readonly ExtractPermalinkFragmentFromHeadingAction $extractPermalinkFragmentFromHeading) {}

    public function create(string $text, string $releaseDate, bool $hideDate = false): Heading
    {
        return tap(new Heading(2), function (Heading $heading) use ($hideDate, $text, $releaseDate) {
            $heading->appendChild(new Text($text));

            if ($hideDate === false) {
                $heading->appendChild(new Text(' - '.$releaseDate));
            }

            $this->extractPermalinkFragmentFromHeading->execute($heading);
        });
    }
}
