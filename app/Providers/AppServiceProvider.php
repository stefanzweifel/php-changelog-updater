<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\GitHubActionsOutput;
use Illuminate\Support\ServiceProvider;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Mention\MentionExtension;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(GitHubActionsOutput::class, fn () => new GitHubActionsOutput());

        $this->app->bind(Environment::class, function () {
            $config = [
                'mentions' => [
                    // GitHub handler mention configuration.
                    // Sample Input:  `@colinodell`
                    // Sample Output: `<a href="https://www.github.com/colinodell">@colinodell</a>`
                    'github_handle' => [
                        'prefix' => '@',
                        'pattern' => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                        'generator' => 'https://github.com/%s',
                    ],
                ],
            ];

            $environment = new Environment($config);

            $environment->addExtension(new MentionExtension());

            return $environment;
        });
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
