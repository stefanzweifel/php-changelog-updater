<?php

declare(strict_types=1);

namespace App\Support;

use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContentInterface;

class Markdown
{
    public function __construct(
        private readonly MarkdownParser $markdownParser,
        private readonly MarkdownRenderer $markdownRenderer
    ) {}

    public function parse(string $markdown): Document
    {
        return $this->markdownParser->parse($markdown);
    }

    public function render(Document $markdown): RenderedContentInterface
    {
        return $this->markdownRenderer->render($markdown);
    }
}
