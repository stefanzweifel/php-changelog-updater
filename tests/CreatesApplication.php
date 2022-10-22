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

        // Can be enabled by default, once the old set-output syntax has been removed.
        // $this->hasGitHubOutputEnvironment();

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function hasGitHubOutputEnvironment()
    {
        putenv("GITHUB_OUTPUT=$this->gitHubOutputTestfile");
    }
}
