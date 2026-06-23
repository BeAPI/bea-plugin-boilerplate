<?php

namespace BEA\PB\Tests\Unit;

use BEA\PB\Routes\Router;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Router::register_rewrite_elements(
			[
				'example-page' => 'example-page',
			]
		);
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_rewrite_slug_returns_mapped_slug(): void {
		$this->assertSame( 'example-page', Router::rewrite_slug( 'example-page' ) );
	}

	public function test_get_url_builds_home_url_with_mapped_slug(): void {
		Functions\when( 'home_url' )->alias(
			static function ( $path ) {
				return 'https://example.test/' . ltrim( (string) $path, '/' );
			}
		);
		Functions\when( 'trailingslashit' )->returnArg();

		$this->assertSame( 'https://example.test/example-page', Router::get_url( 'example-page' ) );
	}
}
