<?php

declare(strict_types=1);

use App\Actions\ParseAndLinkifyGitHubUsernamesAction;

it('returns null if release notes are null also', function () {

    $result = app(ParseAndLinkifyGitHubUsernamesAction::class)->execute(null);

    expect($result)->toBeNull();
});

it('does not replace anything if there are no GitHub usernames in string', function () {

    $result = app(ParseAndLinkifyGitHubUsernamesAction::class)->execute('This is a string with no GitHub usernames.');

    expect($result)->toEqual('This is a string with no GitHub usernames.');
});

it('replaces GitHub usernames with links', function () {

    $result = app(ParseAndLinkifyGitHubUsernamesAction::class)->execute('This is a string with a @username in it.');

    expect($result)->toEqual('This is a string with a [@username](https://github.com/username) in it.');
});
