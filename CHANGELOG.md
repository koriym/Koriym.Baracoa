# Changelog

All notable changes to this project will be documented in this file.

## [2.0.0] - 2026-01-22

### Added

- PHP 8.2+ support with typed properties and constructor property promotion
- PHPUnit 11 support
- PHPStan (level max) static analysis
- Psalm (errorLevel 1) static analysis
- Doctrine Coding Standard with PHP_CodeSniffer
- GitHub Actions CI workflow
- `#[Override]` attributes on interface implementations

### Changed

- Minimum PHP version raised from 7.1 to 8.2
- Updated `psr/simple-cache` to ^3.0
- Updated `symfony/cache` to ^7.2
- Modernized code with PHP 8.2+ features (readonly properties, typed properties)
- Replaced yarn with npm in documentation
- Upgraded Redux example to React 18.3, Redux 5, webpack 5, Babel 7
- Upgraded Handlesbar example to webpack 5, Babel 7, Handlebars 4.7

### Removed

- PHP 7.x support
- Travis CI configuration (replaced with GitHub Actions)
- php-cs-fixer (replaced with PHP_CodeSniffer + Doctrine standard)

## [1.0.0] - 2017

### Added

- Initial release
- `Baracoa` class for JavaScript server-side rendering
- `CacheBaracoa` class for V8Js snapshot caching
- V8Js and PhpExecJs execution engines
- PSR-16 cache support
