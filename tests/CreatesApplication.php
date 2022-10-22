<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    public readonly string $gitHubOutputTestfile;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $this->gitHubOutputTestfile = base_path('tests/github_output.txt');

        putenv("GITHUB_OUTPUT=$this->gitHubOutputTestfile");

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
