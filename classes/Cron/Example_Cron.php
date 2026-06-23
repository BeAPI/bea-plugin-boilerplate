<?php

namespace BEA\PB\Cron;

use BEA\PB\Cron;

/**
 * Example cron job. Schedule it from your project when needed.
 */
class Example_Cron extends Cron {

	/**
	 * @var string
	 */
	protected $type = 'example';

	/**
	 * @inheritDoc
	 */
	public function process() {
		if ( $this->is_locked() ) {
			return false;
		}

		$this->create_lock_file();
		$this->add_log( 'Example cron processed.' );
		$this->delete_lock_file();

		return true;
	}
}
