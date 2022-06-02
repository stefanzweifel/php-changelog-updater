<?php

declare(strict_types=1);

namespace App\QueryExpressions;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\Query\ExpressionInterface;

class HeadingLevel implements ExpressionInterface
{
    public function __construct(private readonly int $level)
    {
    }

    public function __invoke(Node $node): bool
    {
        if ($node instanceof Heading) {
            return $node->getLevel() === $this->level;
        }

        return false;
    }
}
