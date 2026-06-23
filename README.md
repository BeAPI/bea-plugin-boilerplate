# BEA Plugin Boilerplate

Foundation for building WordPress plugins at BeAPI.

> **AI agents:** read [AGENTS.md](AGENTS.md) before scaffolding or extending a plugin from this boilerplate.

## Requirements

- WordPress 6.0+
- PHP 8.0+
- Composer
- [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) for the Hello block example

## Installation

This boilerplate targets **Bedrock** (or any Composer-managed WordPress stack). Plugin classes are autoloaded from the **project root** `composer.json`, not from a local `vendor/` inside the plugin.

### Bedrock project

1. Add the plugin under `web/app/plugins/{slug}/` (path may vary with your Bedrock layout).
2. Register its namespace in the **Bedrock root** `composer.json`:

```json
{
  "autoload": {
    "psr-4": {
      "BEA\\MyPlugin\\": "web/app/plugins/my-plugin/classes/"
    }
  }
}
```

3. Run `composer dump-autoload -o` at the Bedrock root.
4. Activate the plugin in WordPress.

The plugin bootstraps with `inc/autoload.php`: it uses the root autoloader when classes are already available, and falls back to `{plugin}/vendor/autoload.php` only for standalone development of this repository.

### Standalone development (this repository / CI)

```bash
git clone git@github.com:beapi/bea-plugin-boilerplate.git
cd bea-plugin-boilerplate
composer install
```

Dev dependencies (PHPCS, PHPUnit, PHPStan) live in the plugin `vendor/` for local tooling and GitHub Actions. They are not required in Bedrock production.

## Quick start

### Bedrock (recommended)

Use [composer-scaffold-plugin](https://github.com/BeAPI/composer-scaffold-plugin) from your Bedrock project root:

```bash
composer require beapi/composer-scaffold-plugin
composer scaffold-plugin web/app/plugins/my-plugin
```

The interactive command lets you pick components, rename identifiers, and register the PSR-4 namespace in the root `composer.json` (pass `--no-autoload` only if you manage autoload manually).

Then activate the plugin in WordPress.

### Fallback: in-repo scaffold script

When working inside a cloned boilerplate (or without `composer-scaffold-plugin`), use the built-in script:

```bash
composer run scaffold -- my-plugin "My Plugin Name" "BEA\\MyPlugin" "MY_PLUGIN"
```

Then register the namespace in the Bedrock root `composer.json` and run `composer dump-autoload -o` at the project root.

## Manual renaming

If you prefer manual replacements, enable case-sensitive search and replace:

| Search | Replace with |
| --- | --- |
| `bea-plugin-boilerplate` | `my-plugin` |
| `BEA\PB` | `BEA\MyPlugin` |
| `BEA_PB_` | `MY_PLUGIN_` |
| `Plugin Boilerplate` | `My plugin name` |
| `init_bea_pb_plugin` | `init_my_plugin` |
| `bea-pb` | `my-plugin` |

Then rename `bea-plugin-boilerplate.php` to `my-plugin.php` and update the plugin header.

## Included examples

| Feature | Location |
| --- | --- |
| Custom post type + taxonomy | `classes/Post_Types/Custom_Post_Type.php` |
| Post model | `classes/Models/Custom_Post_Type_Model.php` |
| ACF block (`block.json`) | `classes/Blocks/Hello_Block.php` |
| Native block (`block.json`) | `classes/Blocks/Quote_Block.php` |
| Shortcode `[bea_hello]` | `classes/Shortcodes/Hello.php` |
| Router + controller | `classes/Routes/Router.php`, `classes/Controllers/Example_Controller.php` |
| Cron base + example | `classes/Cron.php`, `classes/Cron/Example_Cron.php` |

Extend blocks via the `bea_pb_blocks` filter. Extend rewrite slugs via `bea_pb_rewrite_elements`.

## Development

```bash
composer cs
composer cb
composer lint
composer phpstan
composer test
```

Grumphp runs the same checks locally before commits when configured.

## Project structure

- `inc/` Composer autoload bootstrap (`autoload.php`)
- `classes/` PSR-4 application code
- `views/` PHP templates rendered through `Helpers`
- `assets/blocks/` Block metadata (`block.json`)
- `assets/acf/php/` Local ACF field groups for block examples
- `languages/` Translation files
- `tests/` PHPUnit unit tests

Views receive data through a `$view_data` array instead of `extract()`.

## Autoload

PSR-4 autoloading is declared in the plugin `composer.json`:

```json
"autoload": {
  "psr-4": {
    "BEA\\PB\\": "classes/"
  }
}
```

On Bedrock, **merge this mapping into the root** `composer.json` (adjust the path to your plugin directory). Do not rely on `composer install` inside the deployed plugin.

For local work on this repository or CI, run `composer install` in the plugin directory so dev tools and the fallback autoloader are available.

## Optional dependencies

- ACF for ACF-based blocks and model meta helpers
- Posts 2 Posts for `P2p_Aware` connection helpers
- Bea_Log for extended cron logging (falls back to `error_log()`)

## Changelog

See [CHANGELOG.md](CHANGELOG.md).
