<?php

namespace BEA\PB\Tests\Unit;

use BEA\PB\Models\User;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_get_permalink_returns_false_when_author_url_is_missing(): void {
		Functions\when( 'get_the_author_meta' )->justReturn( '' );

		$user_obj     = new \WP_User();
		$user_obj->ID = 1;

		$user = new User( $user_obj );

		$this->assertFalse( $user->get_permalink() );
	}

	public function test_get_permalink_returns_url_with_query_args(): void {
		Functions\when( 'get_the_author_meta' )->justReturn( 'https://example.test/author/' );
		Functions\expect( 'add_query_arg' )
			->once()
			->with( [ 'ref' => 'test' ], 'https://example.test/author/' )
			->andReturn( 'https://example.test/author/?ref=test' );

		$user_obj     = new \WP_User();
		$user_obj->ID = 1;

		$user = new User( $user_obj );

		$this->assertSame( 'https://example.test/author/?ref=test', $user->get_permalink( [ 'ref' => 'test' ] ) );
	}
}
