<?php

declare(strict_types=1);

use App\Queries\FindUnreleasedHeading;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Parser\MarkdownParser;

test('find unreleased heading in markdown ast', function () {
    $markdown = <<<'TXT'
    ## [Unreleased](http://example.com)
    TXT;

    $env = new Environment();
    $env->addExtension(new CommonMarkCoreExtension());
    $parser = new MarkdownParser($env);

    $ast = $parser->parse($markdown);

    $result = app(FindUnreleasedHeading::class)->find($ast);

    $this->assertNotNull($result);
    $this->assertInstanceOf(Heading::class, $result);
});

test('returns null if unreleased heading is not level 2', function () {
    $markdown = <<<'TXT'
    ### [Unreleased](http://example.com)
    TXT;

    $env = new Environment();
    $env->addExtension(new CommonMarkCoreExtension());
    $parser = new MarkdownParser($env);

    $ast = $parser->parse($markdown);

    $result = app(FindUnreleasedHeading::class)->find($ast);

    $this->assertNull($result);
});

test('returns null if unreleased heading is not a link', function () {
    $markdown = <<<'TXT'
    ## Unreleased
    TXT;

    $env = new Environment();
    $env->addExtension(new CommonMarkCoreExtension());
    $parser = new MarkdownParser($env);

    $ast = $parser->parse($markdown);

    $result = app(FindUnreleasedHeading::class)->find($ast);

    $this->assertNull($result);
});

test('return null if no unreleased heading is found in markdown ast', function () {
    $markdown = <<<'TXT'
    # Wrong Header
    TXT;

    $env = new Environment();
    $env->addExtension(new CommonMarkCoreExtension());
    $parser = new MarkdownParser($env);

    $ast = $parser->parse($markdown);

    $result = app(FindUnreleasedHeading::class)->find($ast);

    $this->assertNull($result);
});
