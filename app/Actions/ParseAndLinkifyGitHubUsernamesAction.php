<?php

declare(strict_types=1);

namespace App\Actions;

class ParseAndLinkifyGitHubUsernamesAction
{
    public function execute(?string $releaseNotes): ?string
    {
        if ($releaseNotes === null) {
            return null;
        }

        // Pattern explanation:
        // - `(?<!\[)` and `(?!\])` are negative lookbehind and lookahead assertions, respectively.
        //   They ensure that the GitHub username is not preceded or followed by a square
        //   bracket [ or ], which indicates that the username is already wrapped in a link.
        // - `@([A-Za-z0-9_]+)` matches the GitHub username itself. It starts with
        //   the @ symbol and consists of alphanumeric characters and underscores.
        $pattern = '/(?<!\[)@([A-Za-z0-9_]+)(?!\])/';

        $replacement = '[@$1](https://github.com/$1)';

        return preg_replace($pattern, $replacement, $releaseNotes);
    }
}
