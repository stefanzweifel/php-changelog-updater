<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\GitHubActionsOutput;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(GitHubActionsOutput::class, fn() => new GitHubActionsOutput());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
