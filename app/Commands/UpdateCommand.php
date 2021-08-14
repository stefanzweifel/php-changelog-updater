<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\AddReleaseNotesToChangelog;
use LaravelZero\Framework\Commands\Command;
use League\CommonMark\Output\RenderedContentInterface;
use Throwable;

class UpdateCommand extends Command
{
    protected $signature = 'update
        {--release-notes= : Markdown Release Notes to be added to the CHANGELOG}
        {--latest-version= : The version the CHANGELOG should be updated too}
        {--release-date= : Date when latest version has been released}
        {--path-to-changelog=CHANGELOG.md : Path to changelog markdown file to be updated}
        {-w\--write : Write changes to file}
    ';

    protected $description = 'Update Changelog with the given release notes.';

    /**
     * @throws Throwable
     */
    public function handle(AddReleaseNotesToChangelog $addReleaseNotesToChangelog)
    {
        $releaseNotes = $this->option('release-notes');
        $latestVersion = $this->option('latest-version');
        $releaseDate = $this->option('release-date');
        $pathToChangelog = $this->option('path-to-changelog');
        $shouldWriteToFile = $this->option('write');

        if (empty($releaseDate)) {
            $releaseDate = now()->format('Y-m-d');
        }

        $changelog = $this->getChangelogContent($pathToChangelog);

        $updatedChangelog = $addReleaseNotesToChangelog->execute(
            originalChangelog: $changelog,
            releaseNotes: $releaseNotes,
            latestVersion: $latestVersion,
            releaseDate: $releaseDate
        );

        $this->info($updatedChangelog->getContent());

        $this->writeChangelogToFile($shouldWriteToFile, $pathToChangelog, $updatedChangelog);
    }

    protected function getChangelogContent(string $pathToChangelog): bool | string
    {
        return file_get_contents($pathToChangelog);
    }

    protected function writeChangelogToFile(bool $shouldWriteToFile, string $pathToChangelog, RenderedContentInterface $updatedMarkdown): void
    {
        if ($shouldWriteToFile) {
            file_put_contents($pathToChangelog, $updatedMarkdown->getContent());
        }
    }
}
