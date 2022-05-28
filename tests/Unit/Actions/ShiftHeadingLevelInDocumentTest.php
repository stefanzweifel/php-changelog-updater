<?php

declare(strict_types=1);

use App\Actions\ShiftHeadingLevelInDocumentAction;
use App\Support\Markdown;

test('shifts headings to be below min heading level', function () {
    $document = app(Markdown::class)->parse(<<<MD
    # Level 1 Heading

    ## Level 2 Heading

    ### Level 3 Heading

    #### Level 4 Heading

    ##### Level 5 Heading
    MD);

    $result = app(ShiftHeadingLevelInDocumentAction::class)->execute($document, 3);

    $updatedDocument = app(Markdown::class)->render($result);

    expect(trim($updatedDocument->getContent()))
        ->toEqual(
            <<<MD
            ### Level 1 Heading

            #### Level 2 Heading

            ##### Level 3 Heading

            ###### Level 4 Heading

            ###### Level 5 Heading
            MD,
        );
});

test('shifts headings and keeps hierarchy', function () {
    $document = app(Markdown::class)->parse(<<<MD
    # Level 1 becomes Level 2

    ## Level 2 becomes Level 3

    ### Level 3 becomes Level 4

    #### Level 4 becomes Level 5

    ##### Level 5 becomes Level 6

    ###### Level 6 becomes Level 6
    MD);

    $result = app(ShiftHeadingLevelInDocumentAction::class)->execute($document, 2);

    $updatedDocument = app(Markdown::class)->render($result);

    expect(trim($updatedDocument->getContent()))
        ->toEqual(
            <<<MD
            ## Level 1 becomes Level 2

            ### Level 2 becomes Level 3

            #### Level 3 becomes Level 4

            ##### Level 4 becomes Level 5

            ###### Level 5 becomes Level 6

            ###### Level 6 becomes Level 6
            MD
        );
});
