<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\GitHubActionsOutput;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(GitHubActionsOutput::class, fn () => new GitHubActionsOutput);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
