<?php
/**
 * Class SampleTest
 *
 * @package Vandergraaf
 */

/**
 * Sample test case.
 */
class FilterTest extends WP_UnitTestCase {

	private $cwd = "";


	function __construct()
	{
		$this->cwd = getcwd();
	}

	/**
	 * A single example test.
	 */

	function test_page_generator_installed() {

		$this->assertTrue( is_plugin_active('vandergraaf/vandergraaf.php') );

	}

	function test_actions_installed() {

		$VdG = new Van_der_Graaf();

		$this->assertTrue( has_action( "admin_init", array( $VdG, "admin_init" ) ) == 10 );

		$this->assertTrue( has_action( "admin_menu", array( $VdG, "Van_der_Graaf_Config" ) ) == 10 );

		$this->assertTrue( has_action( 'post_submitbox_misc_actions', array( $VdG, "add_generate_button_to_editor" ) ) == 10 );


	}


}
