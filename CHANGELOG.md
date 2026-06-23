# Changelog

All notable changes to this project are documented in this file.

Release dates and entries are derived from git tags and commit history. Boilerplate versions correspond to the `Version Boilerplate` plugin header.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [3.6.0] - 2026-06-23

### Added
- Working examples for custom post type, ACF block, native block, shortcode, router, controller, and cron.
- Composer PSR-4 autoload bootstrap and admin notice when `vendor/` is missing.
- Activation and deactivation hooks with rewrite rule flushing.
- PHPUnit test suite, PHPStan configuration, scaffold script, `readme.txt`, `LICENSE`, `uninstall.php`, and `.distignore`.
- `Native_Block` abstraction and Quote block example without ACF.

### Changed
- Extract ACF and Posts 2 Posts helpers from `Model` into `Acf_Aware` and `P2p_Aware` traits.
- Replace `extract()` in view rendering with an explicit `$view_data` array.
- Move `Cron` to `classes/Cron.php` for PSR-4 compliance.
- Rewrite `Router` bootstrapping via the `bea_pb_rewrite_elements` filter.
- Rework CI workflows (lint, PHPCS, PHPStan, PHPUnit).
- Rewrite project documentation.

### Fixed
- `Controller` singleton usage and namespace check in `filter_classes()`.
- `User::get_permalink()` inverted logic.
- Misleading Singleton comment in `Shortcode_Factory`.

### Removed
- Psalm tooling removed after 3.5.1 (superseded by PHPStan in 3.6.0).

## [3.5.1] - 2025-12-11

### Changed
- Improve `Model` performance by avoiding loading all ACF fields eagerly.
- Use the user object with `get_field()`.
- Use `get_the_terms()` when possible to retrieve post terms.

### Fixed
- Update Composer dependencies to address a security issue in `symfony/process`.

## [3.5.0] - 2024-07-02

### Added
- `Acf_Json_Block` abstract class to register ACF Gutenberg blocks from `block.json`.
- Block data passed to the admin invalid block template.

### Changed
- Implement `get_block_args()` in `Acf_Json_Block`.

## [3.4.2] - 2023-10-23

### Added
- Missing Composer Psalm command.

### Changed
- Use `plugin_basename()` to resolve the plugin directory.

### Fixed
- PHPCS and Psalm warnings and errors across multiple files.
- Rename restricted WordPress variables to `post_obj` and `user_obj`.

## [3.4.1] - 2021-11-23

### Fixed
- Wrong escaping for HTML block class names in ACF blocks.

## [3.4.0] - 2021-09-20

### Removed
- Compatibility class.
- Unused default `Admin` and `Plugin` classes.

### Fixed
- Fatal error caused by an incorrect bool return type in `Model`.

## [3.3.0] - 2021-03-15

### Added
- Psalm static analysis configuration.

### Changed
- Enforce return types across the codebase.
- Use short array syntax.
- Rename `get_ID()` to `get_id()`.
- Restrict `get_url_complex()` to array arguments.
- Enhance `phpcs:ignore` rules for skeleton compatibility.

### Fixed
- All Psalm errors reported at the time of release.
- Use `InvalidArgumentException` when a model is instantiated with the wrong post type.

### Removed
- Thumbnail deletion on `remove_post_thumbnail`.
- Protected `_*` methods from models.

## [3.2.0] - 2021-03-01

### Added
- Interfaces and abstract classes to register Gutenberg blocks.
- Additional data in block render helpers.

### Changed
- Update GitHub Actions workflow.

## [3.1.1] - 2021-02-25

### Changed
- Improve PHPCS rules and coding standards compliance.
- Rename hook `BEA/Helpers/locate_template/templates` to `beapi_helpers_locate_template_templates`.

## [3.1.0] - 2021-01-28

### Changed
- Update `Singleton` for PHP 8.0 compatibility.
- Raise minimum PHP version requirements and tooling (PHPCS, PHPCompatibility, Grumphp, PHPLint).

## [3.0.0] - 2020-05-12

### Changed
- Migrate to PSR-4 autoloading via `composer.json`.
- Move compatibility class into the `classes/` directory.

### Removed
- Legacy `autoload.php` bootstrap file.
- Widget feature (see [2.2.0]).

## [2.2.0] - 2019-02-04

### Removed
- Widget feature.

## [2.1.8] - 2018-09-02

### Fixed
- Misuse of Singleton in the shortcode factory.

## [2.1.7] - 2017-06-14

### Fixed
- Wrong use of `get_object_term_cache()` and PHP `Exception` typing.

## [2.1.6] - 2016-11-22

### Fixed
- Non-static method `init_translations()` called statically.

## [2.1.5] - 2016-11-16

### Fixed
- `get_model()` now uses `model_class` from the post type object.

## [2.1.4] - 2016-10-06

### Added
- French translations.

### Fixed
- Text domain loading.

## [2.1.3] - 2016-04-13

### Fixed
- Model class name resolution with namespaces.

## [2.1.2] - 2016-03-16

### Fixed
- User model filename.

## [2.1.1] - 2016-03-06

### Fixed
- Plugin version number.

## [2.1.0] - 2016-02-12

### Added
- Shortcode base class and factory.

## [2.0.1] - 2016-01-11

### Fixed
- Widget title display in views.

## [2.0.0] - 2015-10-13

### Added
- Singleton trait.
- Boilerplate version header.

### Changed
- Convert main plugin classes to Singleton instances.

## [1.1.2] - 2015-09-30

### Fixed
- Widget registration.

## [1.1.1] - 2015-09-04

### Fixed
- Wrong parameter passed to `locate_template()`.

## [1.1.0] - 2015-09-04

### Added
- Filter hook on `locate_template()`.

## [1.0.0] - 2015-09-04

### Added
- Initial plugin boilerplate with models, helpers, router, controller, cron base, views, and compatibility layer.

[3.6.0]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/3.5.1...3.6.0
[3.5.1]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/3.5.0...3.5.1
[3.5.0]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/3.4.2...3.5.0
[3.4.2]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/3.4.1...3.4.2
[3.4.1]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/3.4.0...3.4.1
[3.4.0]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/3.3.0...3.4.0
[3.3.0]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/3.2.0...3.3.0
[3.2.0]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/3.1.1...3.2.0
[3.1.1]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/3.0.0...3.1.0
[3.0.0]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.1.8...3.0.0
[2.2.0]: https://github.com/BeAPI/bea-plugin-boilerplate/commit/9586227
[2.1.8]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.1.7...2.1.8
[2.1.7]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.1.6...2.1.7
[2.1.6]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.1.5...2.1.6
[2.1.5]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.1.4...2.1.5
[2.1.4]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.1.3...2.1.4
[2.1.3]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.1.2...2.1.3
[2.1.2]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.0.1...2.1.0
[2.0.1]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/1.1.2...2.0.0
[1.1.2]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/BeAPI/bea-plugin-boilerplate/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/BeAPI/bea-plugin-boilerplate/releases/tag/1.0.0
