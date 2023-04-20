<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\AddReleaseNotesToChangelogAction;
use App\Exceptions\ReleaseAlreadyExistsInChangelogException;
use App\Exceptions\ReleaseNotesCanNotBeplacedException;
use App\Exceptions\ReleaseNotesNotProvidedException;
use App\Support\GitHubActionsOutput;
use LaravelZero\Framework\Commands\Command;
use League\CommonMark\Output\RenderedContentInterface;
use Throwable;
use Webmozart\Assert\Assert;

class UpdateCommand extends Command
{
    protected $signature = 'update
        {--release-notes= : Markdown Release Notes to be added to the CHANGELOG.}
        {--latest-version= : The version the CHANGELOG should be updated too.}
        {--heading-text= : Text used in the new release heading. Defaults to the value from --latest-version.}
        {--release-date= : Date when latest version has been released. Defaults to today.}
        {--path-to-changelog=CHANGELOG.md : Path to changelog markdown file to be updated.}
        {--compare-url-target-revision=HEAD : Target revision used in the compare URL of possible "Unreleased" heading.}
        {--github-actions-output : Display GitHub Actions related output}
        {-w\--write : Write changes to file}
    ';

    protected $description = 'Update Changelog with the given release notes.';

    /**
     * @throws Throwable
     */
    public function handle(AddReleaseNotesToChangelogAction $addReleaseNotesToChangelog, GitHubActionsOutput $gitHubActionsOutput)
    {
        $latestVersion = $this->option('latest-version') ?: $this->ask('What version should the CHANGELOG should be updated too?');
        $releaseNotes = $this->getReleaseNotes();
        $releaseDate = $this->option('release-date');
        $pathToChangelog = $this->option('path-to-changelog');
        $compareUrlTargetRevision = $this->option('compare-url-target-revision');
        $headingText = $this->option('heading-text');

        Assert::stringNotEmpty($latestVersion, 'No latest-version option provided. Abort.');
        Assert::fileExists($pathToChangelog, 'CHANGELOG file not found. Abort.');

        if (empty($releaseDate)) {
            $releaseDate = now()->format('Y-m-d');
        }

        if (empty($headingText)) {
            $headingText = $latestVersion;
        }

        $changelog = $this->getChangelogContent($pathToChangelog);

        try {
            $updatedChangelog = $addReleaseNotesToChangelog->execute(
                originalChangelog: $changelog,
                latestVersion: $latestVersion,
                headingText: $headingText,
                releaseNotes: $releaseNotes,
                releaseDate: $releaseDate,
                compareUrlTargetRevision: $compareUrlTargetRevision
            );
            $this->info($updatedChangelog->getContent());

            $this->writeChangelogToFile($pathToChangelog, $updatedChangelog);

            return self::SUCCESS;
        } catch (ReleaseAlreadyExistsInChangelogException $exception) {
            $this->warn($exception->getMessage());

            return self::SUCCESS;
        } catch (ReleaseNotesNotProvidedException|ReleaseNotesCanNotBeplacedException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            if ($this->option('github-actions-output')) {
                $gitHubActionsOutput->render($this->getOutput());
            }
        }
    }

    protected function getReleaseNotes(): null | string
    {
        if ( $this->hasOption('release-notes') ) {
            return $this->option('release-notes') ?: $this->ask('What markdown Release Notes should be added to the CHANGELOG?');
        }

        return null;
    }

    protected function getChangelogContent(string $pathToChangelog): bool | string
    {
        return file_get_contents($pathToChangelog);
    }

    protected function writeChangelogToFile(string $pathToChangelog, RenderedContentInterface $updatedMarkdown): void
    {
        if ($this->option('write')) {
            file_put_contents($pathToChangelog, $updatedMarkdown->getContent());
        }
    }
}
