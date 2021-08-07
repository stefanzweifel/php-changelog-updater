<?php

declare(strict_types=1);

namespace App\QueryExpressions;

use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;

class HeadingText implements \League\CommonMark\Node\Query\ExpressionInterface
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function __invoke(Node $node): bool
    {
        if ($node->hasChildren() === false) {
            return false;
        }

        if ($node->firstChild()->hasChildren() === false) {
            return false;
        }

        $textNode = $node->firstChild()->firstChild();

        if (! $textNode instanceof Text) {
            return false;
        }

        if ($textNode->getLiteral() !== $this->text) {
            return false;
        }

        return true;
    }
}
