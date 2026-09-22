<?php
/**
 * Gutenberg block: Modular Cost Calculator.
 *
 * @package ModularConstructionCalculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MCC_Block {
	public static function register() {
		register_block_type(
			'mcc/modular-cost-calculator',
			array(
				'api_version'     => 2,
				'title'           => 'Modular Cost Calculator',
				'description'     => 'Factory-to-set modular construction feasibility calculator. Output is labeled Included modular stack (2026).',
				'category'        => 'widgets',
				'icon'            => 'calculator',
				'keywords'        => array( 'modular', 'construction', 'cost', 'calculator' ),
				'supports'        => array(
					'html'     => false,
					'multiple' => true,
					'align'    => array( 'wide', 'full' ),
				),
				'render_callback' => array( __CLASS__, 'mcc_render' ),
			)
		);
	}

	public static function mcc_render() {
		MCC_Assets::mcc_enqueue();
		return MCC_Shortcode::mcc_markup();
	}
}
