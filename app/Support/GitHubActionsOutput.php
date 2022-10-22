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
            } else {
                $output->text(sprintf("::set-output name=%s::%s", $key, $value));
            }
        }
    }

    public function setOutput($name, $value): void
    {
        $pathToGitHubOutput = getenv('GITHUB_OUTPUT');
        $gitHubOutput = file_get_contents($pathToGitHubOutput);

        $gitHubOutput .= "$name=$value\n";

        file_put_contents($pathToGitHubOutput, $gitHubOutput, FILE_APPEND | LOCK_EX);
    }

    private function hasGithubOutputEnvironment(): bool
    {
        $gitHubOutput = getenv('GITHUB_OUTPUT');

        return ! empty($gitHubOutput);
    }
}
