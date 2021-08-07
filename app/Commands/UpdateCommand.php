<?php

declare(strict_types=1);

namespace App\Commands;

use App\CreateNewReleaseHeading;
use App\FindUnreleasedHeading;
use App\GenerateCompareUrl;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\Query;
use League\CommonMark\Parser\MarkdownParser;
use Wnx\CommonmarkMarkdownRenderer\MarkdownRendererExtension;
use Wnx\CommonmarkMarkdownRenderer\Renderer\MarkdownRenderer;

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

    public function handle(CreateNewReleaseHeading $createNewReleaseHeading, FindUnreleasedHeading $findUnreleasedHeading, GenerateCompareUrl $generateCompareUrl)
    {
        $releaseNotes = $this->option('release-notes');
        $repositoryUrl = $this->option('repository');
        $latestVersion = $this->option('latest-version');
        $releaseDate = $this->option('release-date');
        $pathToChangelog = $this->option('path-to-changelog');
        $shouldWriteToFile = $this->option('write');

        $changelog = $this->getChangelogContent($pathToChangelog);

        $environment = new Environment();
        $environment->addExtension(new MarkdownRendererExtension());

        $parser = new MarkdownParser($environment);
        $markdownRenderer = new MarkdownRenderer($environment);

        $originalChangelog = $parser->parse($changelog);
        $parsedReleaseNotes = $parser->parse($releaseNotes);


        $unreleasedHeading = $findUnreleasedHeading->find($originalChangelog);

        $previousVersion = $this->getPreviousVersionFromUnreleasedHeading($unreleasedHeading);

        $unreleasedUrl = $generateCompareUrl->generate($repositoryUrl, $latestVersion, 'HEAD');

        $this->updateUrlOnUnreleasedHeading($unreleasedHeading, $unreleasedUrl);

        /** @var HtmlBlock $htmlContent */
        $htmlContent = (new Query())
            ->where(Query::type(HtmlBlock::class))
            ->findOne($originalChangelog);


        $newReleaseHeading = $createNewReleaseHeading->create($repositoryUrl, $previousVersion, $latestVersion, $releaseDate);

        $parsedReleaseNotes->prependChild($newReleaseHeading);

        $htmlContent->insertAfter($parsedReleaseNotes);

        $updatedMarkdown = $markdownRenderer->renderDocument($originalChangelog);

        $this->info($updatedMarkdown->getContent());

        if ($shouldWriteToFile) {
            file_put_contents($pathToChangelog, $updatedMarkdown->getContent());
        }
    }

    /**
     * @param Node|null $matchingNodes
     */
    private function updateUrlOnUnreleasedHeading(?Node $matchingNodes, string $url): void
    {
        /** @var Link $link */
        $link = $matchingNodes->firstChild();
        $link->setUrl($url);
    }

    /**
     * @param string $pathToChangelog
     * @return false|string
     */
    protected function getChangelogContent(string $pathToChangelog)
    {
        return file_get_contents($pathToChangelog);
    }

    /**
     * @param Heading $unreleasedHeading
     * @return string
     * @throws \Throwable
     */
    private function getPreviousVersionFromUnreleasedHeading(Heading $unreleasedHeading): string
    {
        /** @var Link $linkNode */
        $linkNode = $unreleasedHeading->firstChild();

        throw_if($linkNode === null, new \LogicException("Can not find link node in unreleased heading."));

        return Str::of($linkNode->getUrl())
            ->afterLast('/')
            ->explode('...')
            ->first();
    }
}
