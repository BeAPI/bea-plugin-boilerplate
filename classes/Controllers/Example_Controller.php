<?php

namespace BEA\PB\Controllers;

/**
 * Example front-end controller bound to the example-page rewrite slug.
 */
class Example_Controller extends Controller {

	/**
	 * @var string
	 */
	protected $page_slug = 'example-page';

	/**
	 * @inheritDoc
	 */
	protected function init(): void {
		parent::init();
		add_action( 'init', [ $this, 'register_rewrite_rules' ], 5 );
	}

	/**
	 * Register the example rewrite rule and query var.
	 */
	public function register_rewrite_rules(): void {
		add_rewrite_tag( '%bea_pb_page%', '([^&]+)' );
		add_rewrite_rule( '^example-page/?$', 'index.php?bea_pb_page=example-page', 'top' );
	}
}
