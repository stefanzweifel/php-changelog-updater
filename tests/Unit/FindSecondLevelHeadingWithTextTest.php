<?php

declare(strict_types=1);

use App\Queries\FindSecondLevelHeadingWithText;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Parser\MarkdownParser;

it('finds previous version heading in markdown ast', function () {
    $markdown = <<<'MD'
    ## [v1.0.0](http://example.com)
    MD;

    $env = new Environment;
    $env->addExtension(new CommonMarkCoreExtension);
    $parser = new MarkdownParser($env);

    $ast = $parser->parse($markdown);

    $result = app(FindSecondLevelHeadingWithText::class)->find($ast, 'v1.0.0');

    $this->assertNotNull($result);
    $this->assertInstanceOf(Heading::class, $result);
});

it('returns null if no previous version heading can be found in the markdown ast', function () {
    $markdown = <<<'MD'
    There is no heading here.
    MD;

    $env = new Environment;
    $env->addExtension(new CommonMarkCoreExtension);
    $parser = new MarkdownParser($env);

    $ast = $parser->parse($markdown);

    $result = app(FindSecondLevelHeadingWithText::class)->find($ast, 'v1.0.0');

    $this->assertNull($result);
});
