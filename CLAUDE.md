# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP CLI tool (Laravel Zero) that updates CHANGELOG files following the "Keep a Changelog" format. Published as `wnx/changelog-updater` and also used via `stefanzweifel/changelog-updater-action` GitHub Action.

Requires PHP 8.5+.

## Common Commands

- **Run tests:** `composer test` (runs Pest)
- **Run single test:** `vendor/bin/pest --filter="test name"`
- **Run single test file:** `vendor/bin/pest tests/Feature/UpdateCommandTest.php`
- **Lint/format:** `vendor/bin/pint`
- **Static analysis:** `vendor/bin/phpstan analyse`
- **Build PHAR:** `composer build`

## Architecture

Built on Laravel Zero with a single command (`UpdateCommand`) as the entry point.

### Key Patterns

- **Actions** (`app/Actions/`) — Service objects containing business logic. The main orchestrator is `AddReleaseNotesToChangelogAction`, which detects changelog structure and delegates to either `PlaceReleaseNotesAtTheTopAction` (no Unreleased section) or `PlaceReleaseNotesBelowUnreleasedHeadingAction` (Keep a Changelog format with Unreleased heading).
- **Queries** (`app/Queries/`) — Encapsulated document traversal using League\CommonMark's `Node\Query` with custom `QueryExpressions`.
- **Support** (`app/Support/`) — `Markdown` facade wraps League\CommonMark parsing and `wnx/commonmark-markdown-renderer` for AST-to-Markdown rendering. `GitHubActionsOutput` handles writing to `$GITHUB_OUTPUT`.

### Markdown Processing Flow

1. Parse changelog to CommonMark AST via `MarkdownParser`
2. Traverse/modify AST using Query objects with custom `QueryExpressions` (`HeadingLevel`, `HeadingText`)
3. Render modified AST back to Markdown via `MarkdownRenderer`

## Code Quality

- **Pint:** Laravel preset with `declare_strict_types` enforced
- **PHPStan:** Level 6 with Larastan extension
- **CI:** Tests run on PHP 8.5 (plus PHP 8.6 as an allowed-to-fail job) with 90% coverage minimum

## Testing

Tests use Pest. Feature tests in `tests/Feature/` test the full command pipeline. Unit tests in `tests/Unit/` test individual actions/queries. Test fixtures (changelog stubs) live in `tests/Stubs/`.

The base `TestCase` mocks `GitHubActionsOutput` and provides `assertGitHubOutputContains()`/`assertGitHubOutputDoesntContain()` helpers.
