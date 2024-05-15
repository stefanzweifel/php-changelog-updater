<?php

declare(strict_types=1);

use App\CreateNewReleaseHeadingWithCompareUrl;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Inline\Text;
use Wnx\CommonmarkMarkdownRenderer\MarkdownRendererExtension;
use Wnx\CommonmarkMarkdownRenderer\Renderer\MarkdownRenderer;

test('creates new release heading ast', function () {
    $repositoryUrl = 'https://github.com/org/repo';
    $previousVersion = 'v0.1.0';
    $latestVersion = 'v1.0.0';
    $headingText = $latestVersion;
    $releaseDate = '2021-02-01';

    $environment = new Environment();
    $environment->addExtension(new MarkdownRendererExtension());

    $markdownRenderer = new MarkdownRenderer($environment);

    /** @var Document $result */
    $result = app(CreateNewReleaseHeadingWithCompareUrl::class)->create(
        $repositoryUrl,
        $previousVersion,
        $latestVersion,
        $headingText,
        $releaseDate,
        hideDate: false,
    );

    $document = new Document();
    $document->appendChild($result);

    $this->assertInstanceOf(Heading::class, $result);
    $this->assertInstanceOf(Link::class, $result->firstChild());
    $this->assertInstanceOf(Text::class, $result->firstChild()->firstChild());
    $this->assertInstanceOf(Text::class, $result->firstChild()->firstChild());

    $renderedMarkdown = $markdownRenderer->renderDocument($document);
    $this->assertEquals('## [v1.0.0](https://github.com/org/repo/compare/v0.1.0...v1.0.0) - 2021-02-01', trim($renderedMarkdown->getContent()));

});

it('does not add date to release heading if hideDate is false', function () {
    $repositoryUrl = 'https://github.com/org/repo';
    $previousVersion = 'v0.1.0';
    $latestVersion = 'v1.0.0';
    $headingText = $latestVersion;
    $releaseDate = '2021-02-01';

    $environment = new Environment();
    $environment->addExtension(new MarkdownRendererExtension());

    $markdownRenderer = new MarkdownRenderer($environment);

    /** @var Document $result */
    $result = app(CreateNewReleaseHeadingWithCompareUrl::class)->create(
        $repositoryUrl,
        $previousVersion,
        $latestVersion,
        $headingText,
        $releaseDate,
        hideDate: true,
    );

    $document = new Document();
    $document->appendChild($result);

    $this->assertInstanceOf(Heading::class, $result);
    $this->assertInstanceOf(Link::class, $result->firstChild());
    $this->assertInstanceOf(Text::class, $result->firstChild()->firstChild());
    $this->assertInstanceOf(Text::class, $result->firstChild()->firstChild());

    $renderedMarkdown = $markdownRenderer->renderDocument($document);
    $this->assertEquals('## [v1.0.0](https://github.com/org/repo/compare/v0.1.0...v1.0.0)', trim($renderedMarkdown->getContent()));
});
