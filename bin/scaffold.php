#!/usr/bin/env php
<?php
/**
 * Fallback scaffold script when composer-scaffold-plugin is not available.
 *
 * Prefer (Bedrock): composer require beapi/composer-scaffold-plugin
 *                    composer scaffold-plugin web/app/plugins/my-plugin
 *
 * Usage:
 *   composer run scaffold -- my-plugin "My Plugin Name" "BEA\\MyPlugin" "MY_PLUGIN"
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 1 );
}

$slug             = $argv[1] ?? '';
$plugin_name      = $argv[2] ?? '';
$namespace        = $argv[3] ?? '';
$constant_prefix  = rtrim( $argv[4] ?? '', '_' ) . '_';
$init_function    = 'init_' . str_replace( '-', '_', $slug ) . '_plugin';
$views_folder     = $slug;
$old_main_file    = 'bea-plugin-boilerplate.php';
$new_main_file    = $slug . '.php';

if ( '' === $slug || '' === $plugin_name || '' === $namespace ) {
	fwrite(
		STDERR,
		"Usage: composer run scaffold -- <slug> \"Plugin Name\" \"Vendor\\\\Namespace\" [CONSTANT_PREFIX]\n"
	);
	exit( 1 );
}

$root = dirname( __DIR__ );

$replacements = [
	'bea-plugin-boilerplate'  => $slug,
	'BEA\\PB\\'               => $namespace . '\\',
	'BEA\\PB'                 => $namespace,
	'BEA_PB_'                 => $constant_prefix,
	'Plugin Boilerplate'      => $plugin_name,
	'BEA Plugin Name'         => $plugin_name,
	'init_bea_pb_plugin'      => $init_function,
	'bea-pb'                  => $views_folder,
	'bea_pb_'                 => strtolower( str_replace( '-', '_', $slug ) ) . '_',
	'BEA Plugin Boilerplate'  => $plugin_name,
];

$paths = [
	$root,
];

$skip = [
	'vendor',
	'.git',
	'node_modules',
	'.phpunit.cache',
	'composer.lock',
];

$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS )
);

foreach ( $iterator as $file ) {
	/** @var SplFileInfo $file */
	$pathname = $file->getPathname();
	$relative = str_replace( $root . DIRECTORY_SEPARATOR, '', $pathname );

	foreach ( $skip as $part ) {
		if ( str_contains( $relative, $part ) ) {
			continue 2;
		}
	}

	if ( $file->isDir() ) {
		continue;
	}

	if ( ! preg_match( '/\.(php|json|xml|yml|yaml|md|txt|pot|po|neon|dist)$/i', $pathname ) ) {
		continue;
	}

	$contents = file_get_contents( $pathname );
	if ( false === $contents ) {
		continue;
	}

	$new_contents = str_replace( array_keys( $replacements ), array_values( $replacements ), $contents );

	if ( $new_contents !== $contents ) {
		file_put_contents( $pathname, $new_contents );
	}
}

if ( is_file( $root . '/' . $old_main_file ) ) {
	rename( $root . '/' . $old_main_file, $root . '/' . $new_main_file );
}

fwrite( STDOUT, "Fallback scaffold complete.\n" );
fwrite( STDOUT, "- Main file: {$new_main_file}\n" );
fwrite( STDOUT, "- Namespace: {$namespace}\n" );
fwrite( STDOUT, "- Constants prefix: {$constant_prefix}\n" );
fwrite( STDOUT, "Next steps:\n" );
fwrite( STDOUT, "- On Bedrock, prefer composer-scaffold-plugin for new plugins (see README.md)\n" );
fwrite( STDOUT, "- Register the PSR-4 namespace in your Bedrock root composer.json\n" );
fwrite( STDOUT, "- Run composer dump-autoload -o at the Bedrock project root\n" );
fwrite( STDOUT, "- For standalone dev of this repo: composer install\n" );
