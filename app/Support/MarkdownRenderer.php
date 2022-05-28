<?php

declare(strict_types=1);

namespace App\Support;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContentInterface;
use Wnx\CommonmarkMarkdownRenderer\MarkdownRendererExtension;
use Wnx\CommonmarkMarkdownRenderer\Renderer\MarkdownRenderer as CommonmarkRenderer;

class MarkdownRenderer
{
    private CommonmarkRenderer $renderer;

    public function __construct(Environment $environment)
    {
        $environment->addExtension(new MarkdownRendererExtension());

        $this->renderer = new CommonmarkRenderer($environment);
    }

    public function render(Document $markdown): RenderedContentInterface
    {
        return $this->renderer->renderDocument($markdown);
    }
}
