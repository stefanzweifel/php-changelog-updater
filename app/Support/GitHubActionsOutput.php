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
            $output->text(sprintf("::set-output name=%s::%s", $key, head($message)));
        }
    }
}
