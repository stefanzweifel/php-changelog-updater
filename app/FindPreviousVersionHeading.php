<?php

declare(strict_types=1);

namespace App;

use App\QueryExpressions\HeadingLevel;
use App\QueryExpressions\HeadingText;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\Query;

class FindPreviousVersionHeading
{
    /**
     * @param Document $document
     * @return Node|null
     */
    public function find(Document $document, string $previousVersion): ?Node
    {
        return (new Query())
            ->where(Query::type(Heading::class))
            ->andWhere(new HeadingLevel(2))
            ->andWhere(new HeadingText($previousVersion))
            ->findOne($document);
    }
}
