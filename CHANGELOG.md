# Changelog

## v0.9.0 (2020-05-07)
### Changed
- Clear compiled views in local env to keep translations fresh [#18](https://github.com/thepinecode/i18n/pull/18)

## v0.8.0 (2020-03-09)
### Changed
- Migrate tests, set Laravel versions

## v0.7.0 (2020-01-15)
### Changed
- Upgraded testbench version
- Updated license date
- Refactored serviceprovider

## v0.6.7 (2018-07-05)
### Fixed
- Only one package added to the list of translations [#12](https://github.com/thepinecode/i18n/issues/12)

## v0.6.6 (2018-06-22)
### Fixed
- JS string conversions

## v0.6.5 (2018-06-13)
### Fixed
- add fallback for {}-s as well

## v0.6.4 (2018-06-13)
### Fixed
- add fallback for invalid translations [#11](https://github.com/thepinecode/i18n/issues/11)

## v0.6.3 (2018-06-07)
### Fixed
- trim whitespaces [#10](https://github.com/thepinecode/i18n/issues/10)

## v0.6.2 (2018-06-03)
### Changed
- revert JS regexp to have better browser support [#8](https://github.com/thepinecode/i18n/issues/8)

## v0.6.1 (2018-06-03)
### Fixed
- use concat in JS instead of push
- fix parameter ordering when mapping package translations

## v0.6.0 (2018-06-02)
### Added
- support multilang applications and packages [#7](https://github.com/thepinecode/i18n/issues/7)
- tests for multilang feature

## v0.5.1 (2018-05-29)
### Added
- new test for custom translations' object key
### Chaged
- JS regex for object extracting
- trimming quotes from translations' object key

## v0.5.0 (2018-04-02)
### Added
- support package translations, thanks for the idea to [Jonathan](https://github.com/sardoj)

## v0.4.0 (2018-03-24)
### Added
- phpunit.xml
### Changed
- .gitignore
- orchestra/testbench version (3.5.0 -> 3.8.0)
- composer.json

## v0.3.1 (2018-05-31)
### Added
- Transforming replacements

## v0.3.0 (2018-05-31)
### Added
- Add pluralization range handling
- Extend readme with docs
### Fixed
- Fixed typos in readme

## v0.2.1 (2018-05-16)
### Changed
- Update readme
- Refactor service provider

## v0.2.0 (2017-11-11)
### Fixed
- Fix typos in readme
### Removed
- Config file
- View composer
- Unnecessary tests

## v0.1.1 (2017-11-01)
### Changed
- Update readme
- Wrap the translations in a `<script>` tag
- JS handles assigned key to the `window` object

## v0.1.0 (2017-10-31)
### Initial release
