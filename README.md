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
--repository="https://github.com/stefanzweifel/php-changelog-updater" \
--latest-version="v1.0.0" \
--path-to-changelog="CHANGELOG.md" \
--release-date="2021-08-07"
--write
```

### `--release-notes`
The release notes you want to add to your CHANGELOG. Should be markdown.

### `--repository`
The URL which points to your repository. The URL will be used to update the "Unreleased" heading in your CHANGELOG.

### `--latest-version`
Semantic version number of the latest release. The value will be used as the heading text and as a parameter in the compare URL.

Example: `v1.0.0`

### `--path-to-changelog`
Path to your CHANGELOG.md file. Defaults to `CHANGELOG.md`.

### `--release-date`
The date the latest version has been released. The value will be used in the release heading.

### `--write`
Option to write the changed CHANGELOG to `CHANGELOG.md`.

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
