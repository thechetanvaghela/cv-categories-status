<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/thechetanvaghela/cv-categories-status
 * @since             1.0.0
 * @package           Cv_Categories_Status
 *
 * @wordpress-plugin
 * Plugin Name:       Categories Status
 * Plugin URI:        https://github.com/thechetanvaghela/cv-categories-status
 * Description:       This plugin help to add custom status(draft) to selected taxonomies.
 * Version:           1.0.0
 * Requires PHP:      7.0
 * Author:            Chetan Vaghela
 * Author URI:        https://github.com/thechetanvaghela/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cv-categories-status
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CV_CATEGORIES_STATUS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cv-categories-status-activator.php
 */
function activate_cv_categories_status() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cv-categories-status-activator.php';
	Cv_Categories_Status_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cv-categories-status-deactivator.php
 */
function deactivate_cv_categories_status() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cv-categories-status-deactivator.php';
	Cv_Categories_Status_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cv_categories_status' );
register_deactivation_hook( __FILE__, 'deactivate_cv_categories_status' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cv-categories-status.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cv_categories_status() {

	$plugin = new Cv_Categories_Status();
	$plugin->run();

}
run_cv_categories_status();