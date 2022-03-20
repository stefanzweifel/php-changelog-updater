# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/stefanzweifel/php-changelog-updater/compare/v1.4.1...HEAD)

<!-- New Release notes will be placed here automatically -->
## [v1.4.1](https://github.com/stefanzweifel/php-changelog-updater/compare/v1.4.0...v1.4.1) - 2022-03-20

## Fixed

- Fix detection of empty release notes ([#19](https://github.com/stefanzweifel/php-changelog-updater/pull/19))

## [v1.4.0](https://github.com/stefanzweifel/php-changelog-updater/compare/v1.3.0...v1.4.0) - 2022-03-19

## Added

- Output GitHub Actions Output Variables ([#18](https://github.com/stefanzweifel/php-changelog-updater/pull/18))
- Throw Warning if Release already exists in Changelog ([#16](https://github.com/stefanzweifel/php-changelog-updater/pull/16))

## Changed

- Make Release Notes Optional ([#17](https://github.com/stefanzweifel/php-changelog-updater/pull/17))

## [v1.3.0](https://github.com/stefanzweifel/php-changelog-updater/compare/v1.2.2...v1.3.0) - 2022-02-09

## Changed

- Upgrade to Laravel Zero 9 ([#15](https://github.com/stefanzweifel/php-changelog-updater/pull/15))

## [v1.2.2](https://github.com/stefanzweifel/php-changelog-updater/compare/v1.2.1...v1.2.2) - 2022-02-09

## Fixed

- Append release notes at the end if no previous heading can be found ([#14](https://github.com/stefanzweifel/php-changelog-updater/pull/14))

## [v1.2.1](https://github.com/stefanzweifel/php-changelog-updater/compare/v1.2.0...v1.2.1) - 2022-02-01

## Added

- New Build

## [v1.2.0](https://github.com/stefanzweifel/php-changelog-updater/compare/v1.1.1...v1.2.0) - 2022-02-01

## Added

- Add `--compare-url-target-revision` option ([#13](https://github.com/stefanzweifel/php-changelog-updater/pull/13))

## [v1.1.1](https://github.com/stefanzweifel/php-changelog-updater/compare/v1.1.0...v1.1.1) - 2021-11-25

## Changed

- Run Tests on PHP 8.1 ([#12](https://github.com/stefanzweifel/php-changelog-updater/pull/12))

## Fixed

- Upgrade commonmark-markdown-renderer to v1.0.2 ([#11](https://github.com/stefanzweifel/php-changelog-updater/pull/11))

## [v1.1.0](https://github.com/stefanzweifel/php-changelog-updater/compare/v1.0.0...v1.1.0) - 2021-11-07

## Added

- Validate Input Options [#10](https://github.com/stefanzweifel/php-changelog-updater/pull/10)
- Add Release Notes at the end if no heading was found [#9](https://github.com/stefanzweifel/php-changelog-updater/pull/9)
- Paste Release Notes at the top if no Unreleased Heading can be found [#7](https://github.com/stefanzweifel/php-changelog-updater/pull/7)

## Changed

- Refactor Internals (#8) @stefanzweifel

## [v1.0.0](https://github.com/stefanzweifel/php-changelog-updater/compare/v0.4.1...v1.0.0) - 2021-08-14

Release v1.0.0.

## [v0.4.1](https://github.com/stefanzweifel/php-changelog-updater/compare/v0.4.0...v0.4.1) - 2021-08-14

### Fixed

- Fallback to current date if `--release-date` is empty (empty string or null) [#5](https://github.com/stefanzweifel/php-changelog-updater/pull/5)

## [v0.4.0](https://github.com/stefanzweifel/php-changelog-updater/compare/v0.3.0...v0.4.0) - 2021-08-14

### Changed

- Make `--release-date` Option optional [#4](https://github.com/stefanzweifel/php-changelog-updater/pull/4)

## [v0.3.0](https://github.com/stefanzweifel/php-changelog-updater/compare/v0.2.1...v0.3.0) - 2021-08-12

### Removed

- Remove  option [#2](https://github.com/stefanzweifel/php-changelog-updater/pull/2)

## [v0.2.1](https://github.com/stefanzweifel/php-changelog-updater/compare/v0.2.0...v0.2.1) - 2021-08-07

### Changed

- Upgrade Dependencies

## [v0.2.0](https://github.com/stefanzweifel/php-changelog-updater/compare/v0.1.0...v0.2.0) - 2021-08-07

### Added

- Add  option

## [v0.1.0](https://github.com/stefanzweifel/php-changelog-updater/releases/tag/v0.1.0) - 2021-08-07

- Initial Release
