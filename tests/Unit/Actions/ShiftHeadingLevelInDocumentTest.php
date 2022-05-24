<?php

use App\Actions\ShiftHeadingLevelInDocument;
use App\MarkdownParser;
use App\MarkdownRenderer;

test('shifts headings to be below min heading level', function () {

    $document = app(MarkdownParser::class)->parse(<<<MD
    # Level 1 Heading

    ## Level 2 Heading

    ### Level 3 Heading

    #### Level 4 Heading

    ##### Level 5 Heading
    MD);

    $result = app(ShiftHeadingLevelInDocument::class)->execute($document, 3);

    $updatedDocument = app(MarkdownRenderer::class)->render($result);

    $this->assertEquals(<<<MD
### Level 1 Heading

### Level 2 Heading

### Level 3 Heading

#### Level 4 Heading

##### Level 5 Heading
MD
, trim($updatedDocument->getContent()));

});
