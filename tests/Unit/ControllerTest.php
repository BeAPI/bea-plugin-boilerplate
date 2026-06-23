<?php

namespace BEA\PB\Tests\Unit;

use BEA\PB\Controllers\Controller;
use BEA\PB\Controllers\Example_Controller;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		Example_Controller::destroy();
		parent::tearDown();
	}

	public function test_example_controller_is_registered_on_boot(): void {
		Example_Controller::get_instance();

		$this->assertContains( Example_Controller::class, Controller::get_registered_controllers() );
	}

	public function test_get_current_controller_returns_matching_controller(): void {
		Functions\when( 'get_query_var' )->alias(
			static function ( $key, $fallback = '' ) {
				if ( 'bea_pb_page' === $key ) {
					return 'example-page';
				}

				return $fallback;
			}
		);

		Example_Controller::get_instance();

		$controller = Controller::get_current_controller();

		$this->assertInstanceOf( Example_Controller::class, $controller );
	}
}
