# changelog-updater

A PHP CLI to update a changelog with the latest release notes.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/wnx/changelog-updater.svg?style=flat-square)](https://packagist.org/packages/wnx/changelog-updater)
[![Tests](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/run-tests.yml/badge.svg)](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/php-cs-fixer.yml)
[![Psalm](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/psalm.yml/badge.svg)](https://github.com/stefanzweifel/php-changelog-updater/actions/workflows/psalm.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/wnx/changelog-updater.svg?style=flat-square)](https://packagist.org/packages/wnx/changelog-updater)

Want to automate the process of updating your changelog with GitHub Actions? Checkout the [stefanzweifel/changelog-updater-action](https://github.com/stefanzweifel/changelog-updater-action) which does exactly that.

*If your changelog follows the ["Keep a Changelog"](https://keepachangelog.com/) format and has a "Unreleased" heading in it, the CLI will update the link in and place the release notes below heading.*

## Installation

You can install the changelog-updater CLI as a composer dependency in your project or globally. It requires PHP 8.1 or higher.

```bash
composer global require wnx/changelog-updater
```

## Usage

You can use `changelog-updater` by running the `update` command with the following options.

```php
php changelog-updater update \
--release-notes="### Added
- New Feature A
- New Feature B

### Changed
- Update Feature C

### Removes
- Remove Feature D" \
--latest-version="v1.0.0" \
--release-date="2021-08-07" \
--path-to-changelog="CHANGELOG.md" \
--compare-url-target-revision="1.x" \
--write
```

`--release-date`, `--path-to-changelog`, `--compare-url-target-revision` and `--write` are optional. Learn more about the options by running `php changelog-updater update --help`.

If a given version already exists in the CHANGELOG, the CLI will display a warning and not update the CHANGELOG.

### CLI Options

### `--release-notes`
**Required**. The release notes you want to add to your changelog. Should be markdown.

### `--latest-version`
**Required**. Version number of the latest release. The value will be used as the heading text if `--heading-text` is not set. If the changelog has a "Unreleased" heading, the value will be used in the updated compare URL.

Example: `v1.0.0`

### `--release-date`
Optional (Defaults to current date). The date the latest version has been released. The value will be used in the release heading.

### `--path-to-changelog`
Optional (Defaults to `CHANGELOG.md`). Path to CHANGELOG.md file.

### `--compare-url-target-revision`
Optional (Defaults to `HEAD`). If the changelog has a "Unreleased" heading, the value will be used together with the `--latest-version` value in the updated compare URL.

### `--write`
Optional. Write the changes to `CHANGELOG.md` or to the value of `--path-to-changelog`.

### `--github-actions-output`
Optional. Will output values for `UNRELEASED_COMPARE_URL` and `RELEASE_COMPARE_URL` that can be picked up by GitHub Actions and further used in ones Workflow. 

### `--heading-text`
Optional (Defaults to value of `--latest-version`). The text value used in the heading that is created for the new release. 

```md
## heading-text - 2021-02-01
## [heading-text](https://github.com/org/repo/compare/v0.1.0...v1.0.0) - 2021-02-01
```

### `--parse-release-notes`
Optional. Tell the CLI explicitly to use the content between an "Unreleased Heading" and the previous version heading as release notes. The value from `--release-notes` will be ignored.

### `--hide-release-date`
Optional. Don't add the release date to the release heading.

### `--parse-github-usernames` (experimental 🧪)
Optional. Look for GitHub usernames in `--release-notes` and linkify them. Currently not supported for release notes that are located in the existing changelog.

```diff
- Added a new feature @stefanzweifel
+ Added a new feature [@stefanzweifel](https://github.com/stefanzweifel)
```

## Expected Changelog Formats

The CLI does its best to place the given release notes in the right place. 
The CLI looks for the first second level heading in your current `CHANGELOG.md` file. It assumes that this heading represents the previous version. Here is an example of a `CHANGELOG.md` that the CLI can understand.

```
# Changelog

## v1.0.0 - 2021-11-01

### Added
- Initial Release
```

The CLI will then place the new version and its release notes **above** the existing versions.

```diff
# Changelog

+ ## v1.1.0 - 2021-11-10
+ 
+ ### Added
+ 
+ * New Feature
+ 
## v1.0.0 - 2021-11-01

### Added

* Initial Release
```

If the changelog does not contain a second level heading, it considers the changelog to be "empty". It will then place the release notes at the bottom of the document.

```diff
# Changelog
All notable changes to this project will be documented in this file.

+## v1.0.0 - 2021-11-01
+
+### Added
+
+* Initial Release
```

---

If your changelog follows the ["Keep a Changelog"](https://keepachangelog.com/) format and contains an "Unreleased"-heading with a link to the repository compare view, the CLI will automatically update the link in the Unreleased heading.

Here is an example of a changelog, before it is updated with the CLI.

```
# Changelog

## [Unreleased](https://github.com/org/repo/compare/v1.0.0...HEAD)

Please do not update the unreleased notes.

<!-- Content should be placed here -->

## v1.0.0 - 2021-01-01

### Added
- Initial Release
```

Below you find a diff-view of how the CLI will update the changelog.

```diff
# Changelog

+## [Unreleased](https://github.com/org/repo/compare/v1.1.0...HEAD)
-## [Unreleased](https://github.com/org/repo/compare/v1.0.0...HEAD)

Please do not update the unreleased notes.

<!-- Content should be placed here -->
+## [v1.1.0](https://github.com/org/repo/compare/v1.0.0...v1.1.0) - 2021-02-01
+
+### Added
+
+* New Feature A
+* New Feature B
+

## v1.0.0 - 2021-01-01

### Added
* Initial Release
```

The content between the "Unreleased"-heading and the latest version will remain untouched by the CLI.

---

If you don't have a "Unreleased"-heading in your changelog, but the headings of your previous releases contain a link to the repository compare view, the CLI will add links to the compare view to the added release heading.

```diff
# Changelog

+## [v1.1.0](https://github.com/org/repo/compare/v1.0.0...v1.1.0) - 2021-02-01
+
+### Added
+
+* New Feature A
+* New Feature B
+

## [v1.0.0](https://github.com/org/repo/compare/v0.1.0...v1.0.0) - 2021-01-01

### Added
* Initial Release
```

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
