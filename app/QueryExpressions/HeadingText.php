<?php

declare(strict_types=1);

namespace App\QueryExpressions;

use Illuminate\Support\Str;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\Query\ExpressionInterface;

class HeadingText implements ExpressionInterface
{
    public function __construct(private readonly string $text) {}

    public function __invoke(Node $node): bool
    {
        if ($node->hasChildren() === false) {
            return false;
        }

        $textNode = $this->getTextNode($node);

        if ($textNode === null) {
            return false;
        }

        return Str::startsWith($textNode->getLiteral(), $this->text);
    }

    private function getTextNode(Node $node): ?Text
    {
        if ($node instanceof Text) {
            return $node;
        }

        if ($node->hasChildren() === false) {
            return null;
        }

        return $this->getTextNode($node->firstChild());
    }
}
