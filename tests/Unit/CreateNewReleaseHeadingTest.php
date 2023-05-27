<?php

declare(strict_types=1);


use App\CreateNewReleaseHeading;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Inline\Text;
use Wnx\CommonmarkMarkdownRenderer\MarkdownRendererExtension;
use Wnx\CommonmarkMarkdownRenderer\Renderer\MarkdownRenderer;

it('creates new release heading ast', function () {
    $latestVersion = 'v1.0.0';
    $headingText = $latestVersion;
    $releaseDate = '2021-02-01';

    $environment = new Environment();
    $environment->addExtension(new MarkdownRendererExtension());
    $markdownRenderer = new MarkdownRenderer($environment);

    /** @var Document $result */
    $result = app(CreateNewReleaseHeading::class)->create(
        $headingText,
        $releaseDate,
        hideDate: false,
    );

    $document = new Document();
    $document->appendChild($result);

    $this->assertInstanceOf(Heading::class, $result);
    $this->assertInstanceOf(Text::class, $result->firstChild());

    $renderedMarkdown = $markdownRenderer->renderDocument($document);
    $this->assertEquals('## v1.0.0 - 2021-02-01', trim($renderedMarkdown->getContent()));
});

it('creates new release heading ast without a date if hideDate boolean is true', function () {
    $latestVersion = 'v1.0.0';
    $headingText = $latestVersion;
    $releaseDate = '2021-02-01';

    $environment = new Environment();
    $environment->addExtension(new MarkdownRendererExtension());
    $markdownRenderer = new MarkdownRenderer($environment);

    /** @var Document $result */
    $result = app(CreateNewReleaseHeading::class)->create(
        $headingText,
        $releaseDate,
        hideDate: true,
    );

    $document = new Document();
    $document->appendChild($result);

    $this->assertInstanceOf(Heading::class, $result);
    $this->assertInstanceOf(Text::class, $result->firstChild());

    $renderedMarkdown = $markdownRenderer->renderDocument($document);
    $this->assertEquals('## v1.0.0', trim($renderedMarkdown->getContent()));
});
