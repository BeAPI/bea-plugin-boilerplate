# Agent guide — BEA Plugin Boilerplate

Instructions for AI agents creating or extending a WordPress plugin from this repository.

## What this repository is

This is a **plugin boilerplate**, not a finished product. It ships:

- A PSR-4 PHP architecture under `classes/`
- **Example code** (CPT, blocks, shortcode, router, controller, cron) meant to be copied, renamed, or removed
- Dev tooling: PHPCS, PHPStan, PHPUnit, Grumphp, GitHub Actions

Target stack:

- WordPress 6.0+ on **Bedrock** (or equivalent Composer-managed site)
- PHP 8.0+
- PSR-4 autoload registered in the **Bedrock root** `composer.json`

Optional at runtime: ACF (ACF blocks + model meta helpers), Posts 2 Posts (`P2p_Aware`), Bea_Log (cron logging).

## Goal of an agent task

Turn the boilerplate into a **project-specific plugin** by:

1. Renaming identifiers (slug, namespace, constants, text domain)
2. Removing unused boilerplate examples
3. Adding only the features requested by the user
4. Keeping BeAPI conventions and passing quality checks

Do **not** treat example classes as production features unless the user explicitly wants them.

---

## Workflow (follow in order)

### 1. Collect requirements

Confirm with the user (or infer from the task):

| Input | Example |
| --- | --- |
| Plugin slug | `my-events` |
| Plugin name | `My Events` |
| PHP namespace | `BEA\MyEvents` |
| Constant prefix | `MY_EVENTS_` |
| Text domain | usually same as slug: `my-events` |
| Features needed | CPT, ACF blocks, native blocks, shortcodes, custom routes, cron, admin UI, etc. |
| ACF required? | Remove `Requires Plugins: advanced-custom-fields` from the main file if no ACF block is kept |

### 2. Scaffold the plugin

**Preferred (Bedrock):** [composer-scaffold-plugin](https://github.com/BeAPI/composer-scaffold-plugin)

```bash
composer require beapi/composer-scaffold-plugin
composer scaffold-plugin web/app/plugins/my-events
```

Use the interactive prompts for namespace, constant prefix, and optional components. Autoload is registered in the Bedrock root `composer.json` unless you pass `--no-autoload`.

**Fallback (cloned boilerplate / no Composer plugin):** in-repo script that renames identifiers in place:

```bash
composer run scaffold -- my-events "My Events" "BEA\\MyEvents" "MY_EVENTS"
```

Then register autoload in the **Bedrock root** `composer.json`:

```json
{
  "autoload": {
    "psr-4": {
      "BEA\\MyEvents\\": "web/app/plugins/my-events/classes/"
    }
  }
}
```

Run `composer dump-autoload -o` at the Bedrock root. The plugin does **not** ship a production `vendor/` directory.

Then verify replacements did not miss edge cases:

```bash
rg "bea-plugin-boilerplate|BEA\\\\PB|BEA_PB_|bea_pb_|bea-pb|init_bea_pb_plugin" .
```

Update anything still matching manually (filter names may keep the old prefix if scaffold was run before renaming filters — adjust consistently).

### 3. Update the plugin header

Edit the main plugin file (`{slug}.php`):

- `Plugin Name`, `Description`, `Version`, `Text Domain`
- `Requires at least`, `Requires PHP`, `Requires Plugins` (only if ACF or other deps are required)
- Reset `Version` to `1.0.0` for a new plugin; keep `Version Boilerplate` only if this repo stays a boilerplate fork

Define project constants in the main file (keep the existing pattern):

```php
define( 'MY_EVENTS_VERSION', '1.0.0' );
define( 'MY_EVENTS_DIR', plugin_dir_path( __FILE__ ) );
define( 'MY_EVENTS_URL', plugin_dir_url( __FILE__ ) );
define( 'MY_EVENTS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
```

Remove unused example constants (`*_CPT_NAME`, `*_TAXO_NAME`, `*_VIEWS_FOLDER_NAME`) or repurpose them.

### 4. Clean up boilerplate examples

Remove what the project does not need:

| Example | Files / hooks to remove or replace |
| --- | --- |
| Example CPT | `classes/Post_Types/Custom_Post_Type.php`, `classes/Models/Custom_Post_Type_Model.php`, calls in `Main::register_post_types()` |
| Hello ACF block | `classes/Blocks/Hello_Block.php`, `assets/blocks/hello/`, `assets/acf/php/hello.php`, `views/block-hello.php`, entry in `Blocks::$blocks` |
| Quote native block | `classes/Blocks/Quote_Block.php`, `assets/blocks/quote/`, `views/block-quote.php`, entry in `Blocks::$blocks` |
| Hello shortcode | `classes/Shortcodes/Hello.php`, `views/shortcode-hello.php`, call in `Main::register_shortcodes()` |
| Example controller | `classes/Controllers/Example_Controller.php`, instantiation in `Main`, rewrite rule + `Router` default slug |
| Example cron | `classes/Cron/Example_Cron.php` |

Update `classes/Plugin.php` activation hook: register only the CPTs/taxonomies the plugin actually uses before `flush_rewrite_rules()`.

### 5. Implement requested features

Use the patterns below. Match existing naming, hook priorities, and file layout.

### 6. Run quality gates

Before finishing:

```bash
composer install
composer lint
composer cs
composer phpstan
composer test
```

Fix PHPCS issues in project code. Do not disable rules without a strong reason.

Update translations when adding user-facing strings (`languages/default.pot`, locale `.po` files).

Update `CHANGELOG.md` for meaningful releases.

---

## Architecture map

```
{slug}.php              Bootstrap, constants, inc/autoload.php, activation hooks
inc/autoload.php        Bedrock/root autoload detection + local vendor fallback
classes/
  Main.php              Hook registration: i18n, CPTs, shortcodes, router boot
  Blocks.php            Gutenberg block registry
  Plugin.php            activate/deactivate (flush rewrites)
  Helpers.php           Template location and rendering
  Singleton.php         Singleton trait for service classes
  Post_Types/           One class per CPT registration (static register())
  Models/               One model class per CPT (extends Model)
  Blocks/               Block classes + abstract bases (Acf_Block, Native_Block, …)
  Shortcodes/           Shortcode classes + Shortcode_Factory
  Controllers/          Front-end controllers (extend Controller)
  Routes/Router.php     Rewrite slug → URL helpers
  Cron.php              Abstract cron with lock files
  Cron/                 Concrete cron jobs
  Traits/               Acf_Aware, P2p_Aware (used by Model)
views/                  PHP templates (use $view_data, never extract())
assets/blocks/{slug}/   block.json for Gutenberg blocks
assets/acf/php/         Local ACF field groups (optional)
languages/              .pot and .po files
tests/Unit/             PHPUnit tests for testable logic
```

Boot sequence:

1. Main plugin file loads `inc/autoload.php`
2. Autoload resolves via Bedrock root Composer, or `{plugin}/vendor/autoload.php` in standalone dev
3. `plugins_loaded` → `Main::get_instance()` + `Blocks::get_instance()`
4. `init` (priority 0): router boot, CPT registration
5. `init` (default): translations, shortcodes
6. `init` (priority 1): block registration

---

## Conventions (mandatory)

### PHP

- Namespace: PSR-4 mapped in `composer.json` → `"Vendor\\Plugin\\": "classes/"`
- Classes: one class per file, under `classes/`
- Service classes: use `Singleton` trait; wire hooks in `protected function init()`
- Return types: declare them on new/edited methods
- Security: sanitize input, escape output, check capabilities + nonces in admin/AJAX code

### Internationalization

- Text domain = plugin slug
- Wrap user-facing strings in `__()`, `esc_html__()`, etc.
- Load text domain on **`init`**, not `plugins_loaded`:

```php
add_action( 'init', [ $this, 'init_translations' ] );
```

### Views

Templates live in `views/`. Render via `Helpers::render()` or `Helpers::load_template()`.

Templates receive data through **`$view_data`**:

```php
// views/my-template.php
<p><?php echo esc_html( $view_data['title'] ); ?></p>
```

Do not use `extract()`.

Theme override path (via filter): `views/{views-folder}/{template}.php`.

### Hooks and filters

| Filter | Purpose |
| --- | --- |
| `{prefix}_blocks` | Register block class names (default filter name after scaffold: check codebase) |
| `{prefix}_rewrite_elements` | Map internal query vars to public slugs |
| `bea_pb_controllers` | Filter registered controller class names |
| `beapi_helpers_locate_template_templates` | Add theme template paths (legacy name — may be renamed by scaffold) |

Prefer filters over hardcoding lists in `Blocks.php` when building extensible plugins.

---

## Recipes

### Custom post type + model

1. Add constants for post type and taxonomy slugs in the main file.
2. Create `classes/Post_Types/My_Post_Type.php` with `public static function register(): void`.
3. Create `classes/Models/My_Post_Type_Model.php`:

```php
class My_Post_Type_Model extends Model {
    protected $post_type = MY_EVENTS_CPT_NAME;
}
```

4. Pass `'model_class' => My_Post_Type_Model::class` in `register_post_type()` args.
5. Register on `init` priority 0 from `Main`.
6. Register the same CPT in `Plugin::activate()` before `flush_rewrite_rules()`.

Retrieve a model from a post:

```php
$model = Model::get_model( $post );
```

### ACF block (block.json)

1. Create `assets/blocks/{slug}/block.json` with `"name": "acf/{slug}"`.
2. Create `assets/acf/php/{slug}.php` field group (hook fields on `acf/init`).
3. Extend `Acf_Json_Block`, implement `get_slug()` and `validate()`.
4. Create `views/block-{slug}.php`.
5. Register class in `Blocks::$blocks` or via filter.

ACF must be active. Block render uses `Helpers::load_template( 'block-{slug}' )`.

### Native block (no ACF)

1. Create `assets/blocks/{slug}/block.json` (e.g. `"name": "{text-domain}/{slug}"`).
2. Extend `Native_Block`, implement `get_slug()` and `render()` returning HTML string.
3. Create `views/block-{slug}.php`.
4. Register in `Blocks`.

### Shortcode

1. Create `classes/Shortcodes/My_Shortcode.php` extending `Shortcode`.
2. Set `$tag` and `$defaults`.
3. Implement `render()`; return HTML string (use output buffering with `Helpers::render()` if using a view).
4. Register: `Shortcode_Factory::register( 'My_Shortcode' );` from `Main`.

### Custom front-end route + controller

1. Add slug mapping via `{prefix}_rewrite_elements` or `Router::register_rewrite_elements()`.
2. Create controller extending `Controller`, set `$page_slug`, register rewrite rules on `init`.
3. Call `parent::init()` when overriding `init()` so the controller registers itself.
4. Instantiate controller from `Main` via `My_Controller::get_instance()`.
5. Resolve current controller: `Controller::get_current_controller()`.

Controllers are registered explicitly (via `Controller::register_controller()` on singleton boot), not discovered with `get_declared_classes()`. Extend the list with the `bea_pb_controllers` filter if needed.

### Cron job

1. Create `classes/Cron/My_Cron.php` extending `Cron`.
2. Set `protected $type = 'my-job';`.
3. Implement `process()` using lock helpers (`create_lock_file()`, `delete_lock_file()`, `add_log()`).
4. Schedule with `wp_schedule_event()` from activation or a dedicated setup class — the boilerplate does not auto-schedule crons.

---

## Composer and autoload

This boilerplate assumes a **Bedrock root autoloader**. The plugin `composer.json` declares PSR-4 for reference and for standalone dev/CI.

After adding classes in a Bedrock project:

1. Update the plugin `composer.json` autoload map (documentation + standalone dev).
2. Mirror the same PSR-4 entry in the **Bedrock root** `composer.json`.
3. Run `composer dump-autoload -o` at the Bedrock root.

Standalone development of this repository:

```bash
composer install
composer dump-autoload -o
```

Production Bedrock deploy: no `composer install` inside the plugin directory. Dev dependencies stay out of production; only the root autoloader loads plugin classes.

Boot checks are handled by `inc/autoload.php`: classes from the root autoloader, otherwise local `{plugin}/vendor/autoload.php`.

---

## Performance

Apply these rules when building production plugins from this boilerplate.

### High impact

| Risk | Guidance |
| --- | --- |
| `Model::get_all_data()` | Never call in loops, archives, or REST responses. It loads all meta, ACF fields, and terms. Fetch only required keys with `get_meta()`. |
| `get_field()` on the front | Prefer `get_post_meta()` or `get_field( ..., false )` when ACF formatting is not needed. |
| Unconditional boot | Do not load blocks, admin code, or controllers on every request. Gate with context checks (`is_admin()`, `has_block()`, route query vars). |

### Built-in optimizations

- `Helpers::locate_template()` caches resolved paths per request.
- `Model::filter_post_keys()` uses a static allowlist (no repeated `get_class_vars()`).
- Controllers use an explicit registry (`Controller::register_controller()`), not `get_declared_classes()`.

### When adding features

- Register controllers by booting their Singleton from `Main`; always call `parent::init()` in overridden `init()` methods.
- Cache expensive template lookups — reuse `Helpers::render()` instead of duplicating path resolution.
- Avoid N+1 queries: batch term/meta reads in custom queries instead of wrapping each post in `Model::get_model()` + `get_all_data()`.
- Production Bedrock: root `composer dump-autoload -o`, not a plugin-local `vendor/`.

### Measure before optimizing

Use Query Monitor or Server-Timing on real pages before micro-optimizing. Profile `get_all_data()`, block render callbacks, and custom routes first.

---

## Testing

Add unit tests under `tests/Unit/` for logic that does not require a full WordPress stack.

- Bootstrap: `tests/bootstrap.php` (do not load full WordPress stubs before Brain Monkey in tests).
- Use Brain Monkey to stub WordPress functions.
- Run `composer test` after adding tests.

---

## Common agent mistakes

| Mistake | Correct approach |
| --- | --- |
| Hooking `load_plugin_textdomain` on `plugins_loaded` | Hook on `init` |
| Leaving all boilerplate examples in the shipped plugin | Remove unused examples |
| Forgetting Bedrock root autoload registration | Add PSR-4 to Bedrock `composer.json`, run `composer dump-autoload -o` at project root |
| Using `extract()` in new views | Use `$view_data['key']` |
| Hardcoding `BEA\PB` after scaffold | Use the project namespace everywhere |
| Registering CPTs without activation flush | Call CPT register in `Plugin::activate()` then `flush_rewrite_rules()` |
| Creating blocks without `block.json` or view template | Follow `assets/blocks/` + `views/` layout |
| Skipping PHPCS on new PHP files | Run `composer cs` and fix violations |
| Adding ACF blocks without ACF dependency | Keep `Requires Plugins: advanced-custom-fields` or drop ACF blocks |
| Overriding `Controller::init()` without `parent::init()` | Call `parent::init()` so the controller is registered |
| Calling `Model::get_all_data()` in templates or loops | Load only the meta keys you need |

---

## Files to touch for every new plugin

- [ ] `{slug}.php` — header, constants, bootstrap
- [ ] `composer.json` — `name`, `autoload.psr-4` namespace
- [ ] `classes/Main.php` — register only needed services
- [ ] `classes/Plugin.php` — activation/deactivation CPT list
- [ ] `classes/Blocks.php` — block list (or use filter only)
- [ ] `phpcs.xml` — `text_domain` property
- [ ] `languages/default.pot` + locale `.po`
- [ ] `readme.txt` — if distributing on WordPress.org
- [ ] `CHANGELOG.md` — first release entry
- [ ] `README.md` — project-specific documentation
- [ ] Remove unused example files

---

## Reference documentation

- Human overview: [README.md](README.md)
- Bedrock scaffold (preferred): [composer-scaffold-plugin](https://github.com/BeAPI/composer-scaffold-plugin)
- Version history: [CHANGELOG.md](CHANGELOG.md)
- Block examples: `classes/Blocks/Hello_Block.php`, `classes/Blocks/Quote_Block.php`
- CPT example: `classes/Post_Types/Custom_Post_Type.php`

When unsure, **copy the closest example**, rename consistently, delete what is not needed, then run the quality gates.
