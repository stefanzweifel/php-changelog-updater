<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\AddReleaseNotesToChangelog;
use LaravelZero\Framework\Commands\Command;

class UpdateCommand extends Command
{
    protected $signature = 'update
        {--release-notes= : Markdown Release Notes to be added to the CHANGELOG}
        {--repository= : Web URL pointing to the repository}
        {--latest-version= : The version the CHANGELOG should be updated too}
        {--release-date= : Date when latest version has been released}
        {--path-to-changelog=CHANGELOG.md : Path to changelog markdown file to be updated}
        {-w\--write : Write changes to file}
    ';

    protected $description = 'Update Changelog with the given release notes';

    public function handle(AddReleaseNotesToChangelog $addReleaseNotesToChangelog)
    {
        $releaseNotes = $this->option('release-notes');
        $repositoryUrl = $this->option('repository');
        $latestVersion = $this->option('latest-version');
        $releaseDate = $this->option('release-date');
        $pathToChangelog = $this->option('path-to-changelog');
        $shouldWriteToFile = $this->option('write');

        $changelog = $this->getChangelogContent($pathToChangelog);

        $updatedMarkdown = $addReleaseNotesToChangelog->execute(
            changelog: $changelog,
            releaseNotes: $releaseNotes,
            latestVersion: $latestVersion,
            releaseDate: $releaseDate,
            repositoryUrl: $repositoryUrl
        );

        $this->info($updatedMarkdown->getContent());

        if ($shouldWriteToFile) {
            file_put_contents($pathToChangelog, $updatedMarkdown->getContent());
        }
    }

    protected function getChangelogContent(string $pathToChangelog): bool|string
    {
        return file_get_contents($pathToChangelog);
    }
}
