<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ReleaseNotesNotProvidedException extends Exception
{
    public function __construct()
    {
        parent::__construct("Release Notes were not provided. Pass them through the `--release-notes`-option.");
    }
}
