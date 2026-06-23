<?php

namespace BEA\PB;

use BEA\PB\Post_Types\Custom_Post_Type;

/**
 * Plugin lifecycle hooks.
 */
class Plugin {

	/**
	 * Run on plugin activation.
	 */
	public static function activate(): void {
		Custom_Post_Type::register();
		flush_rewrite_rules();
	}

	/**
	 * Run on plugin deactivation.
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}
}
