# Changelog

All notable changes to `laravel-attribute-observer` will be documented in this file.

## 1.6.0 - 2025-03-14

* Compatibility update for Laravel 12+

## 1.5.0 - 2024-02-18

* Make config parsing more forgiving and easier to reason about
    + Convert objects to classes, in the off chance
* Enforce the fact that deletion events are always valid
* Ensure `created` events are captured properly by @dm-pf in https://github.com/alexstewartja/laravel-attribute-observer/pull/5
* Prevent conflicts between method-local vs. arrow-function variable name
* Dynamically check if the model was changed or is dirty by @dm-pf in https://github.com/alexstewartja/laravel-attribute-observer/pull/4
* Fix for Laravel 9 by @neopheus in https://github.com/alexstewartja/laravel-attribute-observer/pull/3

**All changes squashed**: https://github.com/alexstewartja/laravel-attribute-observer/compare/1.2.1...1.5.0

## 1.2.1 - 2021-12-02

- Updated code style in Service Provider - Credit: @RobinBastiaan
- Added Artisan command tests
- Updated GH Sponsor/FUNDING information

## 1.0.4 - 2021-10-09

- Optimized error handling logic
- Updated README

## 1.0.3 - 2021-09-30

- Relaxed PHP and Laravel version requirements
- Updated README

## 1.0.2 - 2021-09-30

- Made PHP 8+ a hard requirement
- Updated README

## 1.0.1 - 2021-09-30

- Optimized parsing of attribute observer methods
- Updated README

## 1.0.0 - 2021-09-29

- Initial release
