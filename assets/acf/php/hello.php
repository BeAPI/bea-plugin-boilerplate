<?php
/**
 * ACF field group for the Hello block example.
 */

add_action(
	'acf/init',
	static function (): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			[
				'key'                   => 'group_bea_pb_hello',
				'title'                 => 'Hello Block',
				'fields'                => [
					[
						'key'   => 'field_bea_pb_hello_message',
						'label' => 'Message',
						'name'  => 'message',
						'type'  => 'text',
					],
				],
				'location'              => [
					[
						[
							'param'    => 'block',
							'operator' => '==',
							'value'    => 'acf/hello',
						],
					],
				],
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'active'                => true,
			]
		);
	}
);
