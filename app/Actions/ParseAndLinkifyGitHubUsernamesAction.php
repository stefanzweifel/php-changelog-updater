<?php

declare(strict_types=1);

namespace App\Actions;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Mention\MentionExtension;
use League\CommonMark\Parser\MarkdownParser as CommonmarkMarkdownParser;
use Wnx\CommonmarkMarkdownRenderer\MarkdownRendererExtension;
use Wnx\CommonmarkMarkdownRenderer\Renderer\MarkdownRenderer as CommonmarkRenderer;

class ParseAndLinkifyGitHubUsernamesAction
{
    public function execute(?string $releaseNotes): ?string
    {
        if ($releaseNotes === null) {
            return null;
        }

        $config = [
            'mentions' => [
                // GitHub handler mention configuration.
                'github_handle' => [
                    'prefix' => '@',
                    'pattern' => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                    'generator' => 'https://github.com/%s',
                ],
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new MarkdownRendererExtension());
        $environment->addExtension(new MentionExtension());

        $parser = new CommonmarkMarkdownParser($environment);
        $renderer = new CommonmarkRenderer($environment);

        $parsedReleaseNotes = $parser->parse($releaseNotes);

        return trim($renderer->renderDocument($parsedReleaseNotes)->getContent());
    }
}
