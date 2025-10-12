<?php

declare(strict_types=1);

namespace App;

use Illuminate\Support\Str;

class GenerateCompareUrl
{
    public function generate(string $url, string $from, string $to): string
    {
        $url = Str::finish($url, '/');

        return sprintf('%scompare/%s...%s', $url, $from, $to);
    }
}
