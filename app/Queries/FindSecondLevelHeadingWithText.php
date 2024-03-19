<?php

declare(strict_types=1);

namespace App\Queries;

use App\QueryExpressions\HeadingLevel;
use App\QueryExpressions\HeadingText;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Query;

class FindSecondLevelHeadingWithText
{
    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function find(Document $document, string $previousVersion): ?Heading
    {
        /**
         * @phpstan-var Heading|null
         */
        return (new Query())
            ->where(Query::type(Heading::class))
            ->andWhere(new HeadingLevel(2))
            ->andWhere(new HeadingText($previousVersion))
            ->findOne($document);
    }
}
