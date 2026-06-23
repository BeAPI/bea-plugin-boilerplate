<?php

namespace BEA\PB\Tests\Unit;

use BEA\PB\Shortcodes\Hello;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

final class HelloShortcodeTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Functions\when( '__' )->returnArg( 1 );
		Functions\when( 'esc_html' )->returnArg( 1 );
		Functions\when( 'locate_template' )->justReturn( '' );
		Functions\when( 'shortcode_atts' )->alias(
			static function ( $defaults, $attributes ) {
				return array_merge( $defaults, $attributes );
			}
		);
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_render_outputs_greeting(): void {
		$shortcode = new Hello();

		$output = $shortcode->render( [ 'name' => 'BeAPI' ] );

		$this->assertStringContainsString( 'Hello, BeAPI!', $output );
	}
}
