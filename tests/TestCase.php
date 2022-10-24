<?php

declare(strict_types=1);

namespace Tests;

use LaravelZero\Framework\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function tearDown(): void
    {
        file_put_contents($this->gitHubOutputTestfile, '');

        parent::tearDown();
    }

    public function assertGitHubOutputContains($name, $value)
    {
        $this->assertStringContainsString("$name=$value", file_get_contents($this->gitHubOutputTestfile));
    }

    public function assertGitHubOutputDoesntContain($name, $value)
    {
        $this->assertStringNotContainsString("$name=$value", file_get_contents($this->gitHubOutputTestfile));
    }
}
