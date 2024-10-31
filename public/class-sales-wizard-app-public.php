<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://webmonch.com
 * @since      1.0.0
 *
 * @package    Sales_Wizard_App
 * @subpackage Sales_Wizard_App/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Sales_Wizard_App
 * @subpackage Sales_Wizard_App/public
 * @author     Shafiq <shafiq6171@gmail.com>
 */
class Sales_Wizard_App_Public {

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
		 * defined in Sales_Wizard_App_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sales_Wizard_App_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sales-wizard-app-public.css', array(), $this->version, 'all' );

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
		 * defined in Sales_Wizard_App_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sales_Wizard_App_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sales-wizard-app-public.js', array( 'jquery' ), $this->version, false );

	}
	public function swa_sent_contact_form7_data_to_crm( $contact_form){
		$title 		= $contact_form->title;
		$form_id 	= $contact_form->id();
		$submission = WPCF7_Submission::get_instance();  
		
		if ( $submission ) {
			$posted_data = $submission->get_posted_data();
		}
		$form_type = 'wpcf7';
		$fields = get_mapping_fields_by_taxonomy($form_id,$form_type);
		update_option('field_map_data',$fields);
		$webhook_id = $fields['webhook_id'];
		$table_data = array(
			'form_id'=>$form_id,
			'form_name'=>$title,
		);
		if(!empty($fields)){
			foreach($fields['mapping_fields'] as $index=>$field){
				$crm_field 	= $field['crm_field'];
				$form_field = $field['form_field'];
				if(isset($posted_data[$form_field])){
					$form_field_data = $posted_data[$form_field];
					if(is_array($form_field_data)){
						$form_field_data = implode(',',$form_field_data);
					}
					$table_data[$crm_field] = $form_field_data;
					
				}
			}
		}
		update_option('report_table_data',$table_data);
		$this->form_data_insert($webhook_id,$table_data);
		
	}
	public function swa_sent_wpform_data_to_crm($fields, $entry, $form_data, $entry_id){
		$table_data = array();
		$form_id 	= absint($form_data[ 'id' ]);
		if( $form_data[ 'id' ]){
			$table_data['form_id'] = $form_id;
			$table_data['form_name'] = get_the_title(absint($form_data[ 'id' ]));
		}
		$form_type = 'wpforms';
		$fields = get_mapping_fields_by_taxonomy($form_id,$form_type);
		$webhook_id = $fields['webhook_id'];
		if(!empty($fields)){
			$wpform_ids = $fields_mapping['wpform_ids'];
			foreach($fields['mapping_fields'] as $index=>$field){
				$crm_field 	= $field['crm_field'];
				$form_field = $field['form_field'];
				if(isset($wpform_ids[$form_field])){
					$field_id_number = $wpform_ids[$form_field];
					$form_field_data = $entry['fields'][$field_id_number];
					if(is_array($form_field_data)){
						$form_field_data = implode(',',$form_field_data);
					}
					$table_data[$crm_field] = $form_field_data;
				}
			}
		}
		$this->form_data_insert($webhook_id,$table_data);
	}
	public function form_data_insert($webhookid,$table_data = array()){
		global $wpdb;
		
		// Create post object
		if(!empty($table_data)){
			$table_data['lead_id'] = random_text_generator(7);
			$form_data = array(
				'post_type' => 'sales_wizard_report',
				'post_title'    => $table_data['form_name'],
				'post_content'  => '',
				'post_status'   => 'publish',
			);
			$result = wp_insert_post( $form_data );
			
			if ( $result && ! is_wp_error( $result ) ) {
				$post_id = $result;
				update_post_meta($post_id,'sales_wizard_lead_id',$table_data['lead_id']);
				update_post_meta($post_id,'sales_wizard_form_id',$table_data['form_id']);
				update_post_meta($post_id,'sales_wizard_webhook_id',$webhookid);
				update_post_meta($post_id,'sales_wizard_form_data',json_encode($table_data));
				update_post_meta($post_id,'sales_wizard_sent_trigger_qty',0);
				update_post_meta($post_id,'sales_wizard_log_status',0);
				$response = form_data_send_to_crm($webhookid,$table_data);
				if($response){
					if(isset($response['code']) && $response['code'] ==200){
						update_post_meta($post_id,'sales_wizard_log_status',1);
					}
				}
			}
		}
	}
	public function trigger_schedule_event(){
		if (! wp_next_scheduled ( 'sales_wizard_report_unsent_event_hook' )) {
			wp_schedule_event( time(), 'daily', 'sales_wizard_report_unsent_event_hook' );
		}
	}
	public function sent_unsent_sales_wizard_report(){
		 $args = [
			'post_type' 		=> 'sales_wizard_report',
			'post_status' 	=> 'publish',
			'posts_per_page' 	=> -1,
			'numberposts' 	=> -1,
			'fields'			=>'ids',
			'meta_query' 	=> array(
			'relation' => 'AND',
			   array(
				   'key' => 'sales_wizard_log_status',
				   'value' => 0,
				   'compare' => '=',
			   ),
			   array(
				   'key' => 'sales_wizard_sent_trigger_qty',
				   'value' => 3,
				   'compare' => '<=',
			   )
			)
		];
		$all_id = get_posts($args);
		if(!empty($all_id)){
			foreach($all_id as $contact_id){
				$form_data 	= get_post_meta($contact_id,'sales_wizard_form_data',true);
				$webhook_id = esc_attr(get_post_meta($contact_id,'sales_wizard_webhook_id',true));
				$trigger_qty = esc_attr(get_post_meta($contact_id,'sales_wizard_sent_trigger_qty',true));
				if(!empty($form_data)){
					$contacts = json_decode($form_data,JSON_OBJECT_AS_ARRAY); 
					$response = form_data_send_to_crm($webhook_id,$contacts);
					if($response){
						if(isset($response['code']) && $response['code'] ==200){
							update_post_meta($contact_id,'sales_wizard_log_status',1);
						}
					}else{
						$trigger_qty +=1;
						update_post_meta($contact_id,'sales_wizard_sent_trigger_qty',$trigger_qty);
					}
				}
			}
		}
	}
	

}
