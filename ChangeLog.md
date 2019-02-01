# Changes in sebastianbergmann/environment

All notable changes in `sebastianbergmann/environment` are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [4.1.0] - 2019-02-01

### Added

* Implemented `Runtime::getNameWithVersionAndCodeCoverageDriver()` method
* Implemented [#34](https://github.com/sebastianbergmann/environment/pull/34): Support for PCOV extension

## [4.0.2] - 2019-01-28

### Fixed

* Fixed [#33](https://github.com/sebastianbergmann/environment/issues/33): `Runtime::discardsComments()` returns true too eagerly

### Removed

* Removed support for Zend Optimizer+ in `Runtime::discardsComments()`

## [4.0.1] - 2018-11-25

### Fixed

* Fixed [#31](https://github.com/sebastianbergmann/environment/issues/31): Regressions in `Console` class

## [4.0.0] - 2018-10-23 [YANKED]

### Fixed

* Fixed [#25](https://github.com/sebastianbergmann/environment/pull/25): `Console::hasColorSupport()` does not work on Windows

### Removed

* This component is no longer supported on PHP 7.0

## [3.1.0] - 2017-07-01

### Added

* Implemented [#21](https://github.com/sebastianbergmann/environment/issues/21): Equivalent of `PHP_OS_FAMILY` (for PHP < 7.2) 

## [3.0.4] - 2017-06-20

### Fixed

* Fixed [#20](https://github.com/sebastianbergmann/environment/pull/20): PHP 7 mode of HHVM not forced

## [3.0.3] - 2017-05-18

### Fixed

* Fixed [#18](https://github.com/sebastianbergmann/environment/issues/18): `Uncaught TypeError: preg_match() expects parameter 2 to be string, null given`

## [3.0.2] - 2017-04-21

### Fixed

* Fixed [#17](https://github.com/sebastianbergmann/environment/issues/17): `Uncaught TypeError: trim() expects parameter 1 to be string, boolean given`

## [3.0.1] - 2017-04-21

### Fixed

* Fixed inverted logic in `Runtime::discardsComments()`

## [3.0.0] - 2017-04-21

### Added

* Implemented `Runtime::discardsComments()` for querying whether the PHP runtime discards annotations

### Removed

* This component is no longer supported on PHP 5.6

[4.1.0]: https://github.com/sebastianbergmann/phpunit/compare/4.0.2...4.1.0
[4.0.2]: https://github.com/sebastianbergmann/phpunit/compare/4.0.1...4.0.2
[4.0.1]: https://github.com/sebastianbergmann/phpunit/compare/66691f8e2dc4641909166b275a9a4f45c0e89092...4.0.1
[4.0.0]: https://github.com/sebastianbergmann/phpunit/compare/3.1.0...66691f8e2dc4641909166b275a9a4f45c0e89092
[3.1.0]: https://github.com/sebastianbergmann/phpunit/compare/3.0...3.1.0
[3.0.4]: https://github.com/sebastianbergmann/phpunit/compare/3.0.3...3.0.4
[3.0.3]: https://github.com/sebastianbergmann/phpunit/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/sebastianbergmann/phpunit/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/phpunit/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/phpunit/compare/2.0...3.0.0

