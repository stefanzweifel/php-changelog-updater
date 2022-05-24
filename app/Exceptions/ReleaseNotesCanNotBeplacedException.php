<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ReleaseNotesCanNotBeplacedException extends Exception
{
    public function __construct()
    {
        parent::__construct("Release notes could not be placed. Is the CHANGELOG empty? Does it contain at least one heading?");
    }
}
