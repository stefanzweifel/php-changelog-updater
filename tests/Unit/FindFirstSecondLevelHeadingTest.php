<?php

declare(strict_types=1);

use App\Queries\FindFirstSecondLevelHeading;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Parser\MarkdownParser;

it('finds first second level heading in markdown ast', function () {
    $markdown = <<<'MD'
    ## v1.1.0
    ## v1.0.0
    MD;

    $env = new Environment;
    $env->addExtension(new CommonMarkCoreExtension);
    $parser = new MarkdownParser($env);

    $ast = $parser->parse($markdown);

    $result = app(FindFirstSecondLevelHeading::class)->find($ast);

    $this->assertNotNull($result);
    $this->assertInstanceOf(Heading::class, $result);

    $this->assertEquals('v1.1.0', $result->firstChild()->getLiteral());
});

it('finds first second level heading in markdown ast if heading contains a link', function () {
    $markdown = <<<'MD'
    ## [v1.1.0](http://example.com)
    ## [v1.0.0](http://example.com)
    MD;

    $env = new Environment;
    $env->addExtension(new CommonMarkCoreExtension);
    $parser = new MarkdownParser($env);

    $ast = $parser->parse($markdown);

    /** @var Heading $result */
    $result = app(FindFirstSecondLevelHeading::class)->find($ast);

    $this->assertNotNull($result);
    $this->assertInstanceOf(Heading::class, $result);

    $this->assertEquals('v1.1.0', $result->firstChild()->firstChild()->getLiteral());
});
