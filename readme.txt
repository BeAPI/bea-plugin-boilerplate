=== BEA Plugin Boilerplate ===
Contributors: beapi
Tags: boilerplate, beapi, gutenberg, acf
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.0
Requires Plugins: advanced-custom-fields
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Foundation for building BeAPI WordPress plugins with examples for CPT, blocks, shortcodes, routing, and cron.

== Description ==

The BEA Plugin Boilerplate provides a PSR-4 plugin structure, reusable abstractions, and working examples:

* Custom post type and taxonomy
* ACF block registered from `block.json`
* Native block registered from `block.json`
* Shortcode with template rendering
* Router, controller, and cron examples

== Installation ==

1. Add the plugin to your Bedrock project under `web/app/plugins/`.
2. Register the plugin PSR-4 namespace in the Bedrock root `composer.json`.
3. Run `composer dump-autoload -o` at the Bedrock root.
4. Activate the plugin through the WordPress admin.

For standalone development of this repository, run `composer install` in the plugin directory.

== Frequently Asked Questions ==

= Is Advanced Custom Fields required? =

Only for the Hello ACF block example. The Quote block works without ACF.

= How do I rename the boilerplate? =

Use composer-scaffold-plugin from your Bedrock project (see README.md), or the in-repo fallback `composer run scaffold -- <slug> "Plugin Name" "Vendor\\Namespace" [CONSTANT_PREFIX]`. Manual replacements are also documented in the README.

== Changelog ==

See [CHANGELOG.md](CHANGELOG.md).

= 3.6.0 =
* Add working examples, autoload bootstrap, activation hooks, tests, PHPStan, CI, scaffold script, and WordPress.org files.

= 3.5.1 =
* Improve Model performance with lazy ACF field loading.

== Upgrade Notice ==

= 3.6.0 =
Run `composer dump-autoload -o` at the Bedrock root after adding the plugin namespace. Flush permalinks after activation if custom routes do not resolve.
