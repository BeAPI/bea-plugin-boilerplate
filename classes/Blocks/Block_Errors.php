<?php

namespace BEA\PB\Blocks;

use Countable;
use Iterator;

class Block_Errors implements Iterator, Countable {
	/**
	 * @var [string] : Stored error messages
	 */
	protected $errors = [];
	private $current = 0;

	/**
	 * Add a message to the error stack.
	 *
	 * @param string $message
	 *
	 * @author Nicolas JUEN
	 */
	public function add( string $message ): void {
		$this->errors[] = $message;
	}

	/**
	 * Get all errors if needed
	 *
	 * @return array
	 * @author Nicolas JUEN
	 */
	public function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Current object counter
	 *
	 * @return int
	 * @author Nicolas JUEN
	 */
	public function count() {
		return count( $this->errors );
	}

	public function current() {
		return $this->errors[ array_keys( $this->errors )[ $this->current ] ];
	}

	public function next(): void {
		++ $this->current;
	}

	public function key() {
		return array_keys( $this->errors )[ $this->current ];
	}

	public function valid() {
		return $this->current < ( count( $this->errors ) );
	}

	public function rewind() : void {
		$this->current = 0;
	}
}