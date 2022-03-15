<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ReleaseAlreadyExistsInChangelogException extends Exception
{
    public function __construct(public string $release)
    {
        parent::__construct("CHANGELOG was not updated as release notes for {$release} already exist.");
    }
}
