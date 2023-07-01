<?php

declare(strict_types=1);

namespace App\Actions;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Query;

class ShiftHeadingLevelInDocumentAction
{
    private const MAX_HEADER_SIZE = 6;

    public function execute(Document $document, int $baseHeadingLevel): Document
    {
        $headings = (new Query())
            ->where(Query::type(Heading::class))
            ->findAll($document);

        /**
         * @var array<Heading> $headings
         *
         * @psalm-suppress InvalidArgument
         */
        $headings = iterator_to_array($headings);

        // Find the lowest heading level
        $lowestHeadingLevel = $this->findLowestHeadingLevel($headings);
        // Calculate the amount to increase the header levels by
        $increaseBy = $baseHeadingLevel - $lowestHeadingLevel;

        foreach ($headings as $heading) {
            // Don't shift heading if the level is below the lowest level
            if ($heading->getLevel() < $lowestHeadingLevel) {
                continue;
            }

            $heading->setLevel(
                min($heading->getLevel() + $increaseBy, self::MAX_HEADER_SIZE)
            );
        }

        return $document;
    }

    /**
     * @param  array<Heading>  $headings
     */
    public function findLowestHeadingLevel(array $headings): int|null
    {
        return array_reduce(
            $headings,
            function (int|null $level, Heading $heading) {
                if ($level === null) {
                    return $heading->getLevel();
                }

                return min($level, $heading->getLevel());
            },
            null
        );
    }
}
