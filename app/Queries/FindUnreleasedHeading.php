<?php

declare(strict_types=1);

namespace App\Queries;

use App\QueryExpressions\HeadingLevel;
use App\QueryExpressions\HeadingText;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\Query;

class FindUnreleasedHeading
{
    /**
     * @param Document $document
     * @return Node|null
     */
    public function find(Document $document): ?Node
    {
        return (new Query())
            ->where(Query::type(Heading::class))
            ->andWhere(Query::hasChild(Query::type(Link::class)))
            ->andWhere(Query::hasChild(Query::hasChild(Query::type(Text::class))))
            ->andWhere(new HeadingLevel(2))
            ->andWhere(new HeadingText('Unreleased'))
            ->findOne($document);
    }
}
