<?php

declare(strict_types=1);

namespace App\Actions;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Query;

class ShiftHeadingLevelInDocument
{
    public function execute(Document $document, int $minLevel): Document
    {
        $headings = (new Query())
            ->where(Query::type(Heading::class))
            ->findAll($document);

        /** @var Heading $heading */
        foreach ($headings as $heading) {

            $currentHeadingLevel = $heading->getLevel();
            $diffToDesiredLevel = $minLevel - $currentHeadingLevel;

            if ($heading->getLevel() <= $minLevel) {
                $heading->setLevel($heading->getLevel() + $diffToDesiredLevel);
            }
        }

        return $document;
    }
}
