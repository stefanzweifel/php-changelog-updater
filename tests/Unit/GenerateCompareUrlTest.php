<?php

declare(strict_types=1);

use App\GenerateCompareUrl;

it('creates a new compare URL based on the given repository URL, previous version, and latest version', function (string $url, string $from, string $to, string $expected) {
    $generateCompareUrl = app(GenerateCompareUrl::class);
    $result = $generateCompareUrl->generate($url, $from, $to);

    expect($result)->toEqual($expected);

})->with([
    [
        'url' => 'https://github.com/org/repo',
        'from' => 'v1.0.0',
        'to' => 'HEAD',
        'expected' => 'https://github.com/org/repo/compare/v1.0.0...HEAD',
    ],
    [
        'url' => 'https://github.com/org/repo/',
        'from' => 'v1.0.0',
        'to' => 'HEAD',
        'expected' => 'https://github.com/org/repo/compare/v1.0.0...HEAD',
    ],
    [
        'url' => 'https://example.com',
        'from' => 'v1.0.0',
        'to' => 'HEAD',
        'expected' => 'https://example.com/compare/v1.0.0...HEAD',
    ],
    [
        'url' => 'https://example.com/',
        'from' => 'v1.0.0',
        'to' => 'HEAD',
        'expected' => 'https://example.com/compare/v1.0.0...HEAD',
    ],
]);
