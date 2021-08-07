<?php

declare(strict_types=1);

namespace App;

use Illuminate\Support\Str;

class GenerateCompareUrl
{
    public function generate(string $url, string $from, string $to): string
    {
        $url = Str::finish($url, '/');

        return "{$url}compare/{$from}...{$to}";
    }
}
