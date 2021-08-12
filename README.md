# changelog-updater

A PHP CLI to update a CHANGELOG following the "Keep a Changelog" format with the latest release notes.

> ⚠️ This package/CLI is still in active development. Breaking changes might occur until v1 is tagged.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/wnx/changelog-updater.svg?style=flat-square)](https://packagist.org/packages/wnx/changelog-updater)
[![Tests](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/run-tests.yml/badge.svg)](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/php-cs-fixer.yml)
[![Psalm](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/psalm.yml/badge.svg)](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/psalm.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/wnx/changelog-updater.svg?style=flat-square)](https://packagist.org/packages/wnx/changelog-updater)


## Installation

You can install the changelog-updater CLI as a global composer dependency:

```bash
composer global require wnx/changelog-updater
```

## Usage

You can use `changelog-updater` by running the `update` command with the following options.

```php
php changelog-updater update \
--release-notes="# Added" \
--latest-version="v1.0.0" \
--release-date="2021-08-07" \
--write \ 
--path-to-changelog="CHANGELOG.md"
```

Note that the CHANGELOG MUST to follow the "[Keep a Changelog](https://keepachangelog.com/en/1.0.0/)"-format. In detail, the CLI looks for a second level "Unreleased"-heading with a link. The link MUST to point to the compare view of the latest version and `HEAD`.
The CLI also looks for a second level heading with the name of the previous version. (If you release `v1.1.0` now, the previous version might be `v1.0.0`.) 
Here is an example Markdown file.


```md
# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/org/repo/compare/v0.1.0...HEAD)

*Please do not manually update this file. We've automated the process.*

## v0.1.0 - 2021-01-01

### Added
- Initial Release
```

### CLI Options

### `--release-notes`
**Required**. The release notes you want to add to your CHANGELOG. Should be markdown.

### `--latest-version`
**Required**. Semantic version number of the latest release. The value will be used as the heading text and as a parameter in the compare URL.

Example: `v1.0.0`

### `--release-date`
**Required.** The date the latest version has been released. The value will be used in the release heading.

### `--path-to-changelog`
Defaults to `CHANGELOG.md`. Path to CHANGELOG.md file.

### `--write`
Optional. Write the changes to `CHANGELOG.md` or to the value of `--path-to-changelog`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Stefan Zweifel](https://github.com/stefanzweifel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
