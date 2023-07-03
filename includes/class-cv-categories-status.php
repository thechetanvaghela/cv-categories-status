<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://cv.com
 * @since      1.0.0
 *
 * @package    Cv_Categories_Status
 * @subpackage Cv_Categories_Status/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cv_Categories_Status
 * @subpackage Cv_Categories_Status/includes
 * @author     Chetan Vaghela <ck@v.com>
 */
class Cv_Categories_Status {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cv_Categories_Status_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CV_CATEGORIES_STATUS_VERSION' ) ) {
			$this->version = CV_CATEGORIES_STATUS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'cv-categories-status';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cv_Categories_Status_Loader. Orchestrates the hooks of the plugin.
	 * - Cv_Categories_Status_i18n. Defines internationalization functionality.
	 * - Cv_Categories_Status_Admin. Defines all hooks for the admin area.
	 * - Cv_Categories_Status_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cv-categories-status-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cv-categories-status-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cv-categories-status-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cv-categories-status-public.php';

		$this->loader = new Cv_Categories_Status_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cv_Categories_Status_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cv_Categories_Status_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cv_Categories_Status_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		# admin menu register
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'cvcs_admin_menu_callback' );
		# init action
		$this->loader->add_action( 'init', $plugin_admin, 'cvcs_admin_menu_save_callback' );
		# admin notice
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'cvcs_admin_notice_callback' );
		# add removable query arg
		$this->loader->add_filter( 'removable_query_args', $plugin_admin, 'cvcs_add_removable_arg_callback', 9999, 3  );

		$cvcs_selected_taxo = get_option('cvcs-taxonomy-enabled');
		$cvcs_selected_taxo = !empty($cvcs_selected_taxo) ? $cvcs_selected_taxo : array();
		if(!empty($cvcs_selected_taxo))
		{
			foreach ($cvcs_selected_taxo as $key => $selected_tax) 
			{
				if(!empty($selected_tax))
				{
					$this->loader->add_filter( 'bulk_actions-edit-'.$selected_tax, $plugin_admin, 'cvcs_add_custom_bulk_action_option' );
					$this->loader->add_filter( 'handle_bulk_actions-edit-'.$selected_tax, $plugin_admin, 'cvcs_handle_custom_bulk_action', 10, 3 );
					$this->loader->add_filter( $selected_tax.'_row_actions', $plugin_admin, 'cvcs_row_actions_callback', 9999, 2 );

					$this->loader->add_action( $selected_tax.'_edit_form_fields', $plugin_admin, 'cvcs_add_custom_option_field', 9999, 2 );
					$this->loader->add_action( 'edited_'.$selected_tax, $plugin_admin, 'cvcs_save_custom_option_field', 9999, 2 );

				}
			}
		}
		
		$this->loader->add_filter( 'get_terms', $plugin_admin, 'cvcs_modify_term_title', 9999, 3 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cv_Categories_Status_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'cvcs_restrict_terms_by_meta_on_archive_callback' );
		$this->loader->add_filter( 'get_terms', $plugin_public, 'cvcs_exclusions_callback', 9999, 3 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cv_Categories_Status_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
