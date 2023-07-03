<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cv.com
 * @since      1.0.0
 *
 * @package    Cv_Categories_Status
 * @subpackage Cv_Categories_Status/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cv_Categories_Status
 * @subpackage Cv_Categories_Status/public
 * @author     Chetan Vaghela <ck@v.com>
 */
class Cv_Categories_Status_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cv_Categories_Status_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cv_Categories_Status_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cv-categories-status-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cv_Categories_Status_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cv_Categories_Status_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cv-categories-status-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Remove terms from archive page if draft.
	 *
	 * @since    1.0.0
	 */
	function cvcs_restrict_terms_by_meta_on_archive_callback($query)
	{
		$queried_object = get_queried_object();
        $taxonomy = isset($queried_object->taxonomy) ? $queried_object->taxonomy : '';
        $term_slug = isset($queried_object->slug) ? $queried_object->slug : '';

		# Check if the current query is an archive page
		if ($query->is_archive() && !is_admin()) 
		{
			# Check if the query is for selected taxonomy
			$cvcs_selected_taxo = get_option('cvcs-taxonomy-enabled');
			$selected_taxo = !empty($cvcs_selected_taxo) ?  $cvcs_selected_taxo : array();
			if(in_array($taxonomy,$selected_taxo))
			{	
				if(!empty($taxonomy) && !empty($term_slug))
				{
					$term = get_term_by( 'slug', $term_slug, $taxonomy );
					$meta_key = 'cv_term_status_draft'; 
        			$meta_value = '1'; 
					# Check if the term has the specified meta value
					if ( $term && get_term_meta( $term->term_id, $meta_key, true ) === $meta_value ) 
					{
						# Exclude the term from the query
						$query->set( 'tax_query', array(
							array(
								'taxonomy' => $taxonomy,
								'field' => 'slug',
								'terms' => $term_slug,
								'operator' => 'NOT IN',
							),
						) );
						
					}
				}
			}
		}
	}
	
	/**
	 * Remove draft terms from frontend.
	 *
	 * @since    1.0.0
	 */
	function cvcs_exclusions_callback($terms, $taxonomies, $args)
	{

		if (!is_admin()) 
		{
			$cvcs_selected_taxo = get_option('cvcs-taxonomy-enabled');
			$selected_taxo = !empty($cvcs_selected_taxo) ? $cvcs_selected_taxo : array();

			if ( array_intersect($taxonomies, $selected_taxo)) 
			{
				# Filter terms based on selected criteria
				$filtered_terms = array_filter($terms, function($term) 
				{
					if(isset($term->term_id))
					{
						# Retrieve the term meta value
						$meta_value = get_term_meta($term->term_id, 'cv_term_status_draft', true);
						if ($meta_value === '1') 
						{
							return false; # Exclude the term
						}
					}
					return true; # Include the term
				});
				return $filtered_terms;
			}
		}
		return $terms;
	}
}
