<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cv.com
 * @since      1.0.0
 *
 * @package    Cv_Categories_Status
 * @subpackage Cv_Categories_Status/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cv_Categories_Status
 * @subpackage Cv_Categories_Status/admin
 * @author     Chetan Vaghela <ck@v.com>
 */
class Cv_Categories_Status_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cv-categories-status-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cv-categories-status-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add bulk item to selected taxonomies
	 *
	 * @since    1.0.0
	 */
	public function cvcs_add_custom_bulk_action_option($actions)
	{
		$actions['move_to_draft'] = 'Draft';
		$actions['move_to_puplic'] = 'Public';
		return $actions;
	}

	/**
	 * Handle custom bulk actions
	 *
	 * @since    1.0.0
	 */
	function cvcs_handle_custom_bulk_action($redirect_to, $action, $term_ids)
	{
		if ($action === 'move_to_draft') 
		{	
			foreach ($term_ids as $term_id) 
			{
				update_term_meta($term_id, 'cv_term_status_draft', 1);
			}
		}

		if ($action === 'move_to_puplic') 
		{	
			foreach ($term_ids as $term_id) 
			{
				update_term_meta($term_id, 'cv_term_status_draft', 0);
			}
		}
		return $redirect_to;
	}

	/**
	 * Remove view from selected taxonomies
	 *
	 * @since    1.0.0
	 */
	function cvcs_row_actions_callback($actions,$tag)
	{
		$custom_meta = get_term_meta($tag->term_id, 'cv_term_status_draft', true);
		if (!empty($custom_meta)) {
			unset($actions['view']);
		}
		return $actions;
	}

	/**
	* Modified title if draft.
	 * @since    1.0.0
	 */
	function cvcs_modify_term_title($terms, $taxonomies, $args)
	{
		if (is_admin()) 
		{
			$cvcs_selected_taxo = get_option('cvcs-taxonomy-enabled');
			$selected_taxo = !empty($cvcs_selected_taxo) ? $cvcs_selected_taxo : array();
			# Check if the taxonomy is your desired taxonomy
			if ( array_intersect($taxonomies, $selected_taxo)) 
			{
				foreach ($terms as $term) 
				{
					if(isset($term->term_id))
					{
						$cv_term_status_draft = get_term_meta($term->term_id, 'cv_term_status_draft', true);
						# Modify the term title based on the term meta
						if (!empty($cv_term_status_draft)) 
						{
							$term->name .= ' - [Draft]';
						}
					}
				}
			}
		}
		return $terms;
	}

	/**
	* Add option field to term edit page
	 * @since    1.0.0
	 */
	function cvcs_add_custom_option_field($term) 
	{
		# Get the current value of the custom option
		$cv_term_status_draft = get_term_meta($term->term_id, 'cv_term_status_draft', true);
		$cv_term_status_draft = !empty($cv_term_status_draft) ? $cv_term_status_draft : 0;
		$term_status = array(  0 => 'Public', 1 => 'Draft');
		?>
		<tr class="form-field">
			<th scope="row"><label for="custom-option"><?php esc_html_e('Status','cv-categories-status'); ?></label></th>
			<td>
				<select name="cvcs-term-status-option">
					<?php 
					foreach ($term_status as $key => $termstatus) 
					{
						$item_checked = ($key == $cv_term_status_draft ) ? 'selected' : '';
						echo '<option value="'.esc_attr($key).'" '.esc_attr($item_checked).'>'.esc_html($termstatus).'</option>';
					} ?>
				</select>
				<?php wp_nonce_field( 'cvcs_save_action_nonce', 'cvcs_save_nonce' ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	* save field to term edit page
	 * @since    1.0.0
	 */
	function cvcs_save_custom_option_field($term_id) {

		if (current_user_can('manage_options')) {

			if (  isset( $_POST['cvcs_save_nonce'] ) &&  wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['cvcs_save_nonce'])), 'cvcs_save_action_nonce' ) ) 
			{
				if (isset($_POST['cvcs-term-status-option'])) 
				{
					$custom_option_value = sanitize_text_field($_POST['cvcs-term-status-option']);
					update_term_meta($term_id, 'cv_term_status_draft', $custom_option_value);
				}
			}
		}
	}

	/**
	 * Add menu page to admin
	 *
	 * @since    1.0.0
	 */
	public function cvcs_admin_menu_callback() {
			# add menu page option to admin
			add_menu_page('CV Categories Status','CV Categories Status','manage_options','cvcs_admin_menu_settings_page',array($this,'cvcs_admin_menu_settings_page_callback'),'dashicons-remove');
	}
	
	/**
	 * callback function menu page.
	 *
	 * @since    1.0.0
	 */
	public function cvcs_admin_menu_settings_page_callback() 
	{

		# get options
	    $cvcs_all_taxo = get_option('cvcs-taxonomy-all-enabled');
	    $cvcs_all_taxo = !empty($cvcs_all_taxo) ? $cvcs_all_taxo : array();
	    $cvcs_selected_taxo = get_option('cvcs-taxonomy-enabled');
	    $cvcs_selected_taxo = !empty($cvcs_selected_taxo) ? $cvcs_selected_taxo : array();
		?>
		<div class="wrap">
			<div id="cvcs-setting-container">
				<div id="cvcs-body">
					<form method="post" enctype="multipart/form-data" id="cvcs-setting-container-form">
						<div id="cvcs-body-content">
							<div class="cvcs-cards-wrap">
								<main class="cvcs-card">
							        <h3><?php esc_html_e('Select Taxonomies','cv-categories-status'); ?></h3>
							        <div class="cvcs-taxonomies-wrap">
								        <?php 
										$output = 'names'; // or objects
								        $taxonomies = get_taxonomies( array( 'public' => true ), $output ); //,'_builtin' => FALSE
								        if(!empty($taxonomies))
								        {
								        	$all_taxo_checked = $cvcs_all_taxo == 'yes' ? 'checked' : '';
								        	$all_taxo_status = $cvcs_all_taxo == 'yes' ? 'On' : 'Off';
								        	$all_taxo_class = $cvcs_all_taxo == 'yes' ? 'on' : '';
								        	?>
								        	<div class="input-row">
										        <label class="cvcs-check-all-items" for="cvcs-check-all-items"><?php esc_html_e('Check all Taxonomies','cv-categories-status'); ?></label>
									            <div class="toggle <?php echo esc_attr($all_taxo_class); ?>">
									                <input type="checkbox" class="checkedAllTaxo" name="cvcs-taxonomy-all-enabled" <?php echo esc_attr($all_taxo_checked); ?> value="<?php echo esc_attr('yes'); ?>" id="<?php echo esc_attr('cvcs-check-all-items'); ?>">
									                <span class="slider"></span>
									                <span class="label"><?php echo esc_attr($all_taxo_status); ?></span>
									            </div>
							        		</div>
							        		<?php
								        	foreach ($taxonomies as $key => $taxonomy) 
								        	{	
								        		$key_item = isset($key) ? 'cvcs-'.$key : '';
								        		$item_checked = in_array($key , $cvcs_selected_taxo) ? 'checked' : '';
								        		$taxo_status = in_array($key , $cvcs_selected_taxo) ? 'On' : 'Off';
								        		$taxo_class = in_array($key , $cvcs_selected_taxo) ? 'on' : '';
										        ?>
								        		<div class="input-row">
										            	<label for="<?php echo esc_attr($key_item); ?>"><?php echo esc_html($taxonomy); ?></label>
														<div class="toggle <?php echo esc_attr($taxo_class); ?>">
															<input class="checkSingleptaxo" type="checkbox" name="cvcs-taxo-item[]" <?php echo esc_attr($item_checked); ?> value="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key_item); ?>">
															<span class="slider"></span>
															<span class="label"><?php echo esc_html($taxo_status); ?></span>
														</div>
								        		</div>
										        <?php  
								           	} 
									    } 
									    ?>
								    </div>
							    </main>
							</div>
						</div>
					    <div class="wpct-save-button-wrap">
						    <?php wp_nonce_field( 'cvcs_action_nonce', 'cvcs_nonce' ); ?>
		                    <?php submit_button( 'Save Settings', 'primary', 'cv-category-status-form-settings'  ); ?>
		                </div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}


	/**
	 * admin notice removable callback function
	 *
	 * @since    1.0.0
	 */
	public function cvcs_add_removable_arg_callback($args)
	{
		array_push($args,'cvcs-msg');
    	return $args;
	}

	/**
	 * admin notice callback function
	 *
	 * @since    1.0.0
	 */
	public function cvcs_admin_notice_callback() {

		# admin notice for form submit
		if (isset($_REQUEST['cvcs-msg']) && !empty($_REQUEST['cvcs-msg'])) 
		{
			if($_REQUEST['cvcs-msg'] == 'success')
			{
				$message = 'Settings Saved';
				$notice_class = 'updated notice-success';
			}
			else if($_REQUEST['cvcs-msg'] == 'error')
			{
				$message = 'Sorry, your nonce did not verify';
				$notice_class = 'notice-error';
			}
			else
			{
				$message = 'Something went wrong!';
				$notice_class = 'notice-error';
			}
			# print admin notice
			printf('<div id="message" class="notice '.esc_attr($notice_class).' is-dismissible"><p>' . esc_html__('%s.', 'cv-categories-status') . '</p></div>', esc_attr($message));
		}

	}

	/**
	 * Save options
	 *
	 * @since    1.0.0
	 */
	public function cvcs_admin_menu_save_callback()
	{
		# declare variables
		$status = "";
		$save_selected_items = array();
		# check current user have manage options permission
		if ( current_user_can('manage_options') ) 
		{
			# check form submission
	      	if (isset($_POST['cv-category-status-form-settings'])) 
	     	{
	        	# current page url
		        $pluginurl = admin_url('admin.php?page=cvcs_admin_menu_settings_page');
	        	# check nonce
	        	if ( ! isset( $_POST['cvcs_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['cvcs_nonce'])), 'cvcs_action_nonce' ) ) 
	        	{
	        		$redirect_url = add_query_arg('cvcs-msg', 'error',$pluginurl);
		            wp_safe_redirect( $redirect_url);
		            exit();
				} 
				else 
				{	
					$status = 'success';
					# all taxonomies
	            	$all_taxo = isset($_POST['cvcs-taxonomy-all-enabled']) ? sanitize_text_field($_POST['cvcs-taxonomy-all-enabled']) : 'no';
	            	update_option('cvcs-taxonomy-all-enabled', sanitize_text_field($all_taxo));

	            	# selected taxonomies
	            	if(isset($_POST['cvcs-taxo-item']) && !empty($_POST['cvcs-taxo-item']))
	            	{
	            		$selected_items = array_map( 'sanitize_text_field', $_POST['cvcs-taxo-item'] );
	                	$save_selected_items = !empty($selected_items) ? $selected_items : array();
	            		update_option('cvcs-taxonomy-enabled', $save_selected_items);
	            	}
	            	else
	            	{
	            		update_option('cvcs-taxonomy-enabled', '');
	            	}
	            	
	            	$redirect_url = add_query_arg('cvcs-msg',$status,$pluginurl);
	                wp_safe_redirect( $redirect_url);
					exit();
				}
			}
		}
	}
}
