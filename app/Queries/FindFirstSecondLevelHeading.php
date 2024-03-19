<?php

declare(strict_types=1);

namespace App\Queries;

use App\QueryExpressions\HeadingLevel;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Query;

class FindFirstSecondLevelHeading
{
    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function find(Document $document): ?Heading
    {
        /**
         * @phpstan-var Heading|null
         */
        return (new Query())
            ->where(Query::type(Heading::class))
            ->andWhere(new HeadingLevel(2))
            ->findOne($document);
    }
}
