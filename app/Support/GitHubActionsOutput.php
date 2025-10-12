<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\MessageBag;

class GitHubActionsOutput extends MessageBag
{
    public function render(OutputStyle $output): void
    {
        foreach ($this->messages() as $key => $message) {
            $value = head($message);

            if ($this->hasGithubOutputEnvironment()) {
                $this->setOutput($key, $value);
            }
        }
    }

    private function setOutput(string $name, string $value): void
    {
        $pathToGitHubOutput = getenv('GITHUB_OUTPUT');
        $gitHubOutput = file_get_contents($pathToGitHubOutput);

        $gitHubOutput .= sprintf('%s=%s%s', $name, $value, PHP_EOL);

        file_put_contents($pathToGitHubOutput, $gitHubOutput, FILE_APPEND | LOCK_EX);
    }

    private function hasGithubOutputEnvironment(): bool
    {
        $gitHubOutput = getenv('GITHUB_OUTPUT');

        return ! empty($gitHubOutput);
    }
}
