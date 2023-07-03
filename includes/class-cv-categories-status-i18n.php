<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://cv.com
 * @since      1.0.0
 *
 * @package    Cv_Categories_Status
 * @subpackage Cv_Categories_Status/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Cv_Categories_Status
 * @subpackage Cv_Categories_Status/includes
 * @author     Chetan Vaghela <ck@v.com>
 */
class Cv_Categories_Status_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cv-categories-status',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
