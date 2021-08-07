<?php

declare(strict_types=1);

namespace App;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Inline\Text;

class CreateNewReleaseHeading
{
    private GenerateCompareUrl $generateCompareUrl;

    public function __construct(GenerateCompareUrl $generateCompareUrl)
    {
        $this->generateCompareUrl = $generateCompareUrl;
    }

    public function create(string $repositoryUrl, string $previousVersion, string $latestVersion, string $releaseDate): Heading
    {
        $url = $this->generateCompareUrl->generate($repositoryUrl, $previousVersion, $latestVersion);

        return tap(new Heading(2), function ($heading) use ($url, $latestVersion, $releaseDate) {
            $heading->appendChild($this->createLinkNode($latestVersion, $url));
            $heading->appendChild($this->createDateNode($releaseDate));
        });
    }

    protected function createLinkNode(string $latestVersion, string $url): Link
    {
        return tap(new Link($url), function ($link) use ($latestVersion) {
            $linkText = new Text($latestVersion);
            $link->appendChild($linkText);
        });
    }

    protected function createDateNode(string $releaseDate): Text
    {
        return new Text(" - {$releaseDate}");
    }
}
