<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://webmonch.com
 * @since      1.0.0
 *
 * @package    Sales_Wizard_App
 * @subpackage Sales_Wizard_App/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sales_Wizard_App
 * @subpackage Sales_Wizard_App/admin
 * @author     Shafiq <shafiq6171@gmail.com>
 */
class Sales_Wizard_App_Admin {

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
		 * defined in Sales_Wizard_App_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sales_Wizard_App_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style('sales-wizard-app-fields',  SALES_WIZARD_APP_DIR_URL.'admin/css/sales-wizard-app-fields.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, SALES_WIZARD_APP_DIR_URL.'admin/css/sales-wizard-app-admin.css', array(), $this->version, 'all' );

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
		 * defined in Sales_Wizard_App_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sales_Wizard_App_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sales-wizard-app-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'sales_wizard_app',array(
			'ajaxUrl'		=>	admin_url('admin-ajax.php'),
			'ajax_nonce' 	=> 	wp_create_nonce( 'nonce' ),
			'crm_fields'  	=> 	sales_wizard_app_fields(),
			'wpcf7'  		=> 	__('Field name of contact form 7','sales-wizard-app'),
			'WPForm'  		=> 	__('Field id of WPFORM','sales-wizard-app'),
		));
	}
	public function sales_wizard_app_admin_menu(){
		$capability = 'manage_options';
        $parent_slug = 'edit.php?post_type=sales_wizard_report';
		global $submenu;
		unset($submenu[$parent_slug][10]);
		/*if ( empty( $GLOBALS['admin_page_hooks']['sales-wizard-app'] ) ) {
			add_menu_page( 	__( 'SalesWizard CRM', 'sales-wizard-app' ), __('SalesWizard CRM plugin', 'sales-wizard-app' ), $capability,  $parent_slug, array( $this, 'wp_swa_options_menu_html' ), SALES_WIZARD_APP_DIR_URL . 'admin/images/sales-wizard-app-logo.png', 31 );
		}*/
		//add_submenu_page('edit.php?post_type=sales_wizard_report',__( 'Field Mapping', 'sales-wizard-app' ), __( 'Field Mapping', 'sales-wizard-app' ), $capability, 'sales-wizard-app',[$this,'wp_swa_options_menu_callback_function'], 20);
	}
	public function wp_swa_options_menu_callback_function() {
		include_once SALES_WIZARD_APP_DIR_PATH . 'admin/partials/sales-wizard-app-admin-menu.php';
	}
	public function sales_wizard_settings_tabs($tabs) {
        $tabs = array(
            'wpcf7'		=> __('Contact Form 7', 'sales-wizard-app'),
            'wpforms' 	=> __('WPForms', 'sales-wizard-app'),
        );
        return $tabs;
    }
	/**
	 * generate settings fields array .
	 *
	 * @name swa_admin_general_settings_page.
	 * @since 1.0.0
	 */
	public function sales_wizard_settings_fields( $settings_fields ) {

		$settings_fields = array(
			'wpcf7'=>array(
			
				array(
					'label' => __( 'Field name of contact form 7', 'sales-wizard-app' ),
					'type'  => 'field_mapping',
					'desc'  => __( 'Check this box to send data to CRM.', 'sales-wizard-app' ),
					'id'    => 'wpcf7',
					'class' => '',
					//'value' => esc_attr(get_option('wpcf7')),
				),
				
				array(
                    'type' => 'button',
                    'id' => 'general-settings',
                    'button_text' => __('Save Settings', 'sales-wizard-app'),
                    'class' => 'primary',
                )
			),
			'wpforms'=>array(
				array(
					'label' => __( 'WPFORM Field Mapping', 'sales-wizard-app' ),
					'type'  => 'title',
					'desc'  => __( 'In column ID use codes listed below. Use codes with uppercase letters and with underscore. Column ID is used to recognize data sent to SalesWizard CRM. You must use Column ID as a field name in Contact Form 7 and WP Form. Field ID use only for WP Form which refers to WP Forms field ID. ', 'sales-wizard-app' ),
					'id' => '',
				),
				array(
					'label' => __( 'Field id of WPFORM', 'sales-wizard-app' ),
					'type'  => 'field_mapping',
					'desc'  => __( 'Check this box to send data to CRM.', 'sales-wizard-app' ),
					'id'    => 'wpforms',
					'class' => '',
				),
				array(
                    'type' => 'button',
                    'id' => 'general-settings',
                    'button_text' => __('Save Settings', 'sales-wizard-app'),
                    'class' => 'primary',
                )
			)
		);
		
        return $settings_fields;
		// Add general settings.
	}
	/**
	 * sales wizard app settings tab save.
	 *
	 * @name swa_admin_save_tab_settings.
	 * @since 1.0.0
	 */
	public function swa_admin_save_tab_settings() {
	
		
		$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'wpcf7';
        if (isset($_POST['setting_submit_fields']) && wp_verify_nonce($_POST['setting_submit_fields'], 'setting_submit_nonce')) {
         	
		 $settings_fields = apply_filters('sales_wizard_settings_fields', array());
		  foreach ($settings_fields as $key => $fields) {
                if ($key === $active_tab) {
                    foreach ($fields as $field) {
                        $field_name = $field['id'];
						
						if(isset($_POST['field_map_'.$field_name])){
							if (is_array($_POST['field_map_'.$field_name])) {
								$posted_value = map_deep($_POST['field_map_'.$field_name], 'sanitize_text_field');
								update_option('field_map_'.$field_name, $posted_value);
							}
						}else{
							if (isset($_POST[$field_name])) {
								
								if (is_array($_POST[$field_name])) {
									$posted_value = map_deep($_POST[$field_name], 'sanitize_text_field');
									update_option($field_name, $_POST[$field_name]);
								} else {
									if($field['type'] =='wysiwyg'){
										$posted_value = wp_unslash($_POST[$field_name]);
										update_option($field_name, $posted_value);
									}else{
										$posted_value = sanitize_text_field(wp_unslash($_POST[$field_name]));
										update_option($field_name, $posted_value);
									}
								}
							} else {
								delete_option($field_name);
							}
						}
                    }
                }
            }
            do_action('save_sales_wizard_settings_fields', $_POST);
        }
	}
	
	/*
	* send contact data to crm from admin dashboard which are not sent initialy
	*/
	public function sfw_admin_send_not_sent_contacts(){
		global $wpdb; 	
		$contacts = array();
		
		 if (isset($_GET['sales_wizard_status_admin']) && $_GET['sales_wizard_status_admin'] = 'send' && isset($_GET['contact_id']) && isset($_GET['_wpnonce']) && !empty($_GET['_wpnonce'])) {
           $redirect_url = admin_url() . "edit.php?post_type=sales_wizard_report";
		   
			$contact_id = sanitize_text_field(wp_unslash($_GET['contact_id']));
			$form_data 	= get_post_meta($contact_id,'sales_wizard_form_data',true);
			$webhook_id = esc_attr(get_post_meta($contact_id,'sales_wizard_webhook_id',true));
			if(!empty($form_data)){
				$contacts = json_decode($form_data,JSON_OBJECT_AS_ARRAY); 
				$response = form_data_send_to_crm($webhook_id,$contacts);
				if($response){
					if(isset($response['code']) && $response['code'] ==200){
						update_post_meta($contact_id,'sales_wizard_log_status',1);
						wp_admin_notice(__( 'Data has been sent!','sales-wizard-app'), [ 'type' => 'success','dismissible'=>true ] );
					}else{
						add_action( 'admin_notices', [$this,'send_not_sent_contacts'] );
						
					}
				}else{
					add_action( 'admin_notices', [$this,'send_not_sent_contacts'] );
				}
			}
			wp_safe_redirect($redirect_url);
			exit;
        } 
		
	}
	public function send_not_sent_contacts(){
		 $message = __( 'Data sent faild!','sales-wizard-app');
		 ?>
		 <div class="notice notice-error is-dismissible">
			<?php echo wpautop( $message ); ?>
		</div>
		 <?php
	}
	public function sfw_admin_export_contacts(){
		/**
		* csv export request
		*/
		$contacts = array();
		if (isset($_REQUEST['export_quote_excel'])) {
		     if (isset($_GET['post']) && !empty($_GET['post'])) {
				$all_id = $_GET['post'];
				if(is_array($all_id)){
					foreach ($all_id as $post_id) {
						$form_data 		= get_post_meta($post_id,'sales_wizard_form_data',true);
						$contacts[] = json_decode($form_data,JSON_OBJECT_AS_ARRAY); 
					}
				}
			 }else{
				 $args = [
				  'post_type' 		=> 'sales_wizard_report',
				  'post_status' 	=> 'publish',
				  'posts_per_page' 	=> -1,
				  'numberposts' 	=> -1,
				  'fields'			=>'ids'
				];
				$all_id = get_posts($args);
				
				if(is_array($all_id)){
					foreach ($all_id as $post_id) {
						$form_data 		= get_post_meta($post_id,'sales_wizard_form_data',true);
						$contacts[] = json_decode($form_data,JSON_OBJECT_AS_ARRAY); 
					}
				}
			 }
			
			$headers_column = array();
			$row_number = 0;
			if(!empty($contacts)){
				foreach ($contacts as $contact_value){
					$row_number++;
					if($row_number ==1){
						foreach($contact_value as $key=>$value){
							$headers_column[] = str_replace('_',' ',ucwords(strtolower($key))); 
						 }
						break;
					}
				}
			}
			export_contacts_csv($contacts,$headers_column);
			exit;
        }
	}
	public function sales_wizard_new_menu_redirect(){
	
		if(isset($_GET['page']) && $_GET['page'] =='sales-wizard-app'){
			$redirect_url = admin_url('edit.php?post_type=sales_wizard_webhook');
			wp_safe_redirect($redirect_url);
			exit;
		}
	}
}