<?php

declare(strict_types=1);

namespace App\Queries;

use App\QueryExpressions\HeadingLevel;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\Query;

class FindFirstSecondLevelHeading
{
    /**
     * @param Document $document
     * @return Node|null
     */
    public function find(Document $document): ?Node
    {
        return (new Query())
            ->where(Query::type(Heading::class))
            ->andWhere(new HeadingLevel(2))
            ->findOne($document);
    }
}
