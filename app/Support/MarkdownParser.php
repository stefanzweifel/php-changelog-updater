<?php

declare(strict_types=1);

namespace App\Support;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Parser\MarkdownParser as CommonmarkMarkdownParser;

class MarkdownParser
{
    private CommonmarkMarkdownParser $parser;

    public function __construct(Environment $environment)
    {
        $environment->addExtension(new CommonMarkCoreExtension());
        $this->parser = new CommonmarkMarkdownParser($environment);
    }

    public function parse(string $markdown): Document
    {
        return $this->parser->parse($markdown);
    }
}
