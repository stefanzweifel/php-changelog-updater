<?php

declare(strict_types=1);

namespace App;

use App\Actions\ExtractPermalinkFragmentFromHeadingAction;
use App\Support\GitHubActionsOutput;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Inline\Text;

class CreateNewReleaseHeadingWithCompareUrl
{
    public function __construct(
        private GenerateCompareUrl $generateCompareUrl,
        private GitHubActionsOutput $gitHubActionsOutput,
        private ExtractPermalinkFragmentFromHeadingAction $extractPermalinkFragmentFromHeading
    ) {
    }

    public function create(string $repositoryUrl, string $previousVersion, string $latestVersion, string $releaseDate): Heading
    {
        $url = $this->generateCompareUrl->generate($repositoryUrl, $previousVersion, $latestVersion);

        $this->gitHubActionsOutput->add('RELEASE_COMPARE_URL', $url);

        return tap(new Heading(2), function (Heading $heading) use ($url, $latestVersion, $releaseDate) {
            $heading->appendChild($this->createLinkNode($latestVersion, $url));
            $heading->appendChild($this->createDateNode($releaseDate));

            $this->extractPermalinkFragmentFromHeading->execute($heading);
        });
    }

    protected function createLinkNode(string $text, string $url): Link
    {
        return tap(new Link($url), function (Link $link) use ($text) {
            $linkText = new Text($text);
            $link->appendChild($linkText);
        });
    }

    protected function createDateNode(string $releaseDate): Text
    {
        return new Text(" - {$releaseDate}");
    }
}
