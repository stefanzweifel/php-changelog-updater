<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\AddReleaseNotesToChangelog;
use App\Exceptions\ReleaseAlreadyExistsInChangelogException;
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
        {--release-date= : Date when latest version has been released. Defaults to today.}
        {--path-to-changelog=CHANGELOG.md : Path to changelog markdown file to be updated.}
        {--compare-url-target-revision=HEAD : Target revision used in the compare URL of possible "Unreleased" heading.}
        {--github-actions-output=false: Display GitHub Actions related output}
        {-w\--write : Write changes to file}
    ';

    protected $description = 'Update Changelog with the given release notes.';

    /**
     * @throws Throwable
     */
    public function handle(AddReleaseNotesToChangelog $addReleaseNotesToChangelog, GitHubActionsOutput $gitHubActionsOutput)
    {
        $this->validateOptions();

        $releaseNotes = $this->option('release-notes');
        $latestVersion = $this->option('latest-version');
        $releaseDate = $this->option('release-date');
        $pathToChangelog = $this->option('path-to-changelog');
        $compareUrlTargetRevision = $this->option('compare-url-target-revision');

        if (empty($releaseDate)) {
            $releaseDate = now()->format('Y-m-d');
        }

        $changelog = $this->getChangelogContent($pathToChangelog);

        try {
            $updatedChangelog = $addReleaseNotesToChangelog->execute(
                originalChangelog: $changelog,
                releaseNotes: $releaseNotes,
                latestVersion: $latestVersion,
                releaseDate: $releaseDate,
                compareUrlTargetRevision: $compareUrlTargetRevision
            );
            $this->info($updatedChangelog->getContent());
            $this->writeChangelogToFile($pathToChangelog, $updatedChangelog);

            return self::SUCCESS;
        } catch (ReleaseAlreadyExistsInChangelogException $exception) {
            $this->warn($exception->getMessage());

            return self::SUCCESS;
        } catch (ReleaseNotesNotProvidedException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            if ($this->option('github-actions-output')) {
                $gitHubActionsOutput->render($this->getOutput());
            }
        }
    }

    private function validateOptions(): void
    {
        Assert::stringNotEmpty($this->option('latest-version'), 'No latest-version option provided. Abort.');
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
