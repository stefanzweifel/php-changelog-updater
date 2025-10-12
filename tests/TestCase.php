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

    public function assertGitHubOutputContains($name, $value): void
    {
        $this->assertStringContainsString(sprintf('%s=%s', $name, $value), file_get_contents($this->gitHubOutputTestfile));
    }

    public function assertGitHubOutputDoesntContain($name, $value): void
    {
        $this->assertStringNotContainsString(sprintf('%s=%s', $name, $value), file_get_contents($this->gitHubOutputTestfile));
    }
}
