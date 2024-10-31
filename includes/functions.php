<?php
/**
 * Exit if accessed directly
 *
 * @since      1.0.0
 * @package    Sales_Wizard_App
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(!function_exists('sales_wizard_app_fields_html')){
	function sales_wizard_app_fields_html($components = array(),$post_id=""){
		include SALES_WIZARD_APP_DIR_PATH.'admin/partials/html-fields.php';
	}
}
if(!function_exists('random_text_generator')){
	/**
	 * This function is used to get random text.
	 *
	 */
	function random_text_generator($n = 7) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $n; $i++) {
			$index = rand(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}
		return strtolower($randomString);
	}
}
if(!function_exists('get_sales_wizard_client_ip')){
	/*
	 *
	 * This function is used to get client ip.
	 */
	function get_sales_wizard_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			//check ip from share internet
			$ipaddress = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
		} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			//to check ip is pass from proxy
			$ipaddress = sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
		} else {
			$ipaddress = sanitize_text_field($_SERVER['REMOTE_ADDR']);
		}
		if(filter_var($ipaddress, FILTER_VALIDATE_IP)){
		  return $ipaddress;
		}
		return  'Not Valid IP';
	}
}
if(!function_exists('form_data_send_to_crm')){
	/*
	 *  contact form 7 or wpform data by array as funtion parameters 
	 * call remote api.
	 * send contact form 7 or wpform field to sales wizard app crm.
	 */
	function form_data_send_to_crm($webhook_id,$user_column_data = array()){
		
		$lead_id = $user_column_data['lead_id'];
		$form_id = $user_column_data['form_id'];
		unset($user_column_data['lead_id']);
		unset($user_column_data['form_name']);
		unset($user_column_data['form_id']);
		
		$sales_wizard_app_enable 	= ( '1' === esc_attr(get_post_meta($webhook_id,'sales_wizard_app_webhook_enable',true)) ? true : false );
		
		$sales_wizard_api_url 		= esc_url(get_post_meta($webhook_id, 'sales_wizard_app_webhook_url', true));
		$sales_wizard_api_key 		= esc_attr(get_post_meta($webhook_id, 'sales_wizard_app_webhook_password', true));
		$sales_wizard_api_version 	= esc_attr(get_post_meta($webhook_id, 'sales_wizard_app_webhook_version', true));
		$sales_wizard_api_mode 		= ( '1' === esc_attr(get_post_meta($webhook_id, 'sales_wizard_app_webhook_mode', true)) ? true : false);
		$user_columns = array();
		foreach($user_column_data as $column_key=>$column_value){
			$user_columns[] = array('string_value'=>$column_value,'column_id'=>$column_key);	
		}
		$colum_data = array(
			'client_ip'			=> get_sales_wizard_client_ip(),
			'lead_id'			=> $lead_id,
			'user_column_data'	=> $user_columns,
			'api_version'		=> $sales_wizard_api_version,
			'form_id'			=> $form_id,
			'campaign_id'		=> $form_id,
			'wordpress_key'		=> $sales_wizard_api_key,
			'is_test'			=> $sales_wizard_api_mode,
		);
		if($sales_wizard_app_enable && $sales_wizard_api_url && $sales_wizard_api_key ){
			$data = wp_remote_post($sales_wizard_api_url, array(
					'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
					'body'        => json_encode($colum_data),
					'method'      => 'POST',
					'data_format' => 'body',
				));
			return $data['response'];
		}
	}
}
if(!function_exists('export_contacts_csv')){
	/**
	* export csv contact form 7 or wpform  stored data
	*/
	function export_contacts_csv($contacts = array(),$headers_column = array()){
			$file_name = 'contacts-'.time().'.csv';
			header('Content-Type: text/csv; charset=utf-8');
			header("Content-Disposition: attachment; filename=$file_name");
			$output = fopen("php://output", "wb");
			fputcsv($output, $headers_column);
			foreach ($contacts as $row){
				fputcsv($output, $row);
			}
			fclose($output);
	}
}
if(!function_exists('sales_wizard_app_fields')){
	function sales_wizard_app_fields(){
		$fields = array(
			'FULL_NAME'					=>__('Full Name','sales-wizard-app'),
			'FIRST_NAME'				=>__('First Name','sales-wizard-app'),
			'LAST_NAME'					=>__('Last Name','sales-wizard-app'),
			'EMAIL'						=>__('Email','sales-wizard-app'),
			'PHONE_NUMBER'				=>__('Phone Number','sales-wizard-app'),
			'POSTAL_CODE'				=>__('Postal Code','sales-wizard-app'),
			'COMPANY_NAME'				=>__('Company Name','sales-wizard-app'),
			'JOB_TITLE'					=>__('Job Title','sales-wizard-app'),
			'WORK_EMAIL'				=>__('Work Email','sales-wizard-app'),
			'WORK_PHONE'				=>__('Work Phone','sales-wizard-app'),
			'STREET_ADDRESS'			=>__('Street Address','sales-wizard-app'),
			'CITY'						=>__('City','sales-wizard-app'),
			'REGION'					=>__('Region','sales-wizard-app'),
			'COUNTRY'					=>__('Country','sales-wizard-app'),
			'VEHICLE_MODEL'				=>__('Vehicle Model','sales-wizard-app'),
			'VEHICLE_TYPE'				=>__('Vehicle Type','sales-wizard-app'),
			'PREFERRED_DEALERSHIP'		=>__('Preferred dealership','sales-wizard-app'),
			'VEHICLE_CONDITION'			=>__('Vehicle Condition','sales-wizard-app'),
			'VEHICLE_OWNERSHIP'			=>__('Vehicle Ownership','sales-wizard-app'),
			'VEHICLE_PAYMENT_TYPE'		=>__('Vehicle Payment Type','sales-wizard-app'),
			'COMPANY_SIZE'				=>__('Company Size','sales-wizard-app'),
			'ANNUAL_SALES'				=>__('Annual Sales','sales-wizard-app'),
			'YEARS_IN_BUSINESS'			=>__('Years in Business','sales-wizard-app'),
			'JOB_DEPARTMENT'			=>__('Job Department','sales-wizard-app'),
			'JOB_ROLE'					=>__('Job Role','sales-wizard-app'),
			'EDUCATION_PROGRAM'			=>__('Education Program','sales-wizard-app'),
			'EDUCATION_COURSE'			=>__('Education Course','sales-wizard-app'),
			'PRODUCT'					=>__('Product','sales-wizard-app'),
			'SERVICE'					=>__('Service','sales-wizard-app'),
			'OFFER'						=>__('Offer','sales-wizard-app'),
			'CATEGORY'					=>__('Category','sales-wizard-app'),
			'PREFERRED_CONTACT_METHOD'	=>__('Preferred Contact method','sales-wizard-app'),
			'PREFERRED_LOCATION'		=>__('Preferred Location','sales-wizard-app'),
			'PREFERRED_CONTACT_TIME'	=>__('Preferred Contact Time','sales-wizard-app'),
			'PURCHASE_TIMELINE'			=>__('Purchase Timeline','sales-wizard-app'),
			'YEARS_OF_EXPERIENCE'		=>__('Year of Experience','sales-wizard-app'),
			'JOB_INDUSTRY'				=>__('Job Industry','sales-wizard-app'),
			'LEVEL_OF_EDUCATION'		=>__('Level Of Education','sales-wizard-app'),
			'PROPERTY_TYPE'				=>__('Property Type','sales-wizard-app'),
			'REALTOR_HELP_GOAL'			=>__('Realtor Help Goal','sales-wizard-app'),
			'PROPERTY_COMMUNITY'		=>__('Property Community','sales-wizard-app'),
			'PRICE_RANGE'				=>__('Price Range','sales-wizard-app'),
			'NUMBER_OF_BEDROOMS'		=>__('Number Of Bedrooms','sales-wizard-app'),
			'FURNISHED_PROPERTY'		=>__('Furnished Property','sales-wizard-app'),
			'PETS_ALLOWED_PROPERTY'		=>__('Pets Allowed Property','sales-wizard-app'),
			'NEXT_PLANNED_PURCHASE'		=>__('Next Planned Purchase','sales-wizard-app'),
			'EVENT_SIGNUP_INTEREST'		=>__('Event Signup Interest','sales-wizard-app'),
			'PREFERRED_SHOPPING_PLACES'	=>__('Preferred Shopping Places','sales-wizard-app'),
			'FAVORITE_BRAND'			=>__('Favorite Brand','sales-wizard-app'),
			'TRANSPORTATION_COMMERCIAL_LICENSE_TYPE'=>__('Transportation Commercial License Type','sales-wizard-app'),
			'EVENT_BOOKING_INTEREST'	=>__('Event Booking Interest','sales-wizard-app'),
			'DESTINATION_COUNTRY'		=>__('Destination Country','sales-wizard-app'),
			'DESTINATION_CITY'			=>__('Destination City','sales-wizard-app'),
			'DEPARTURE_COUNTRY'			=>__('Departure Country','sales-wizard-app'),
			'DEPARTURE_CITY'			=>__('Departure City','sales-wizard-app'),
			'DEPARTURE_DATE'			=>__('Departure Date','sales-wizard-app'),
			'RETURN_DATE'				=>__('Return Date','sales-wizard-app'),
			'NUMBER_OF_TRAVELERS'		=>__('Number Of travelers','sales-wizard-app'),
			'TRAVEL_BUDGET'				=>__('Travel Budget','sales-wizard-app'),
			'TRAVEL_ACCOMMODATION'		=>__('Travel Accommodation','sales-wizard-app'),
			'others'		=>__('Other','sales-wizard-app'),
		);
		return apply_filters('sales_wizard_app_fields',$fields);
	}
}
if(!function_exists('get_all_wpforms')){
	function get_all_wpforms(){
		$options = array();
		$args = [
		  'post_type' => 'wpforms',
		  'post_status' => 'publish',
		  'posts_per_page' => -1,
		  'numberposts' => -1,
		];
		$posts = get_posts($args);
		foreach($posts as $post){
			$options[$post->ID] = $post->post_title;
		}
		return $options;
	}
}
if(!function_exists('get_all_wpcf7_contact_form')){
	function get_all_wpcf7_contact_form(){
		$options = array();
		$args = [
		  'post_type' 		=> 'wpcf7_contact_form',
		  'post_status' 	=> 'publish',
		  'posts_per_page' 	=> -1,
		  'numberposts' 	=> -1,
		];
		$posts = get_posts($args);
		foreach($posts as $post){
			$options[$post->ID] = $post->post_title;
		}
		return $options;
	}
}
function get_mapping_fields_by_taxonomy($form_id,$form_type){
		$fields = array();
		$args = [
		  'post_type' 		=> 'sales_wizard_webhook',
		  'post_status' 	=> 'publish',
		  'posts_per_page' 	=> 1,
		  'numberposts' 	=> 1,
		  'fields'			=>'ids',
		   'meta_query' 	=> array(
			'relation' => 'AND',
			   array(
				   'key' => 'sales_wizard_app_webhook_form_type',
				   'value' => $form_type,
				   'compare' => '=',
			   ),
			   array(
				   'key' => 'sales_wizard_app_webhook_form_id',
				   'value' => $form_id,
				   'compare' => '=',
			   )
		   )
		];
		$posts = get_posts($args);
		if(!empty($posts)){
			foreach($posts as $post_id){
				$fields['mapping_fields'] = get_post_meta($post_id, 'sales_wizard_app_mapping_field', true);
				$fields['webhook_id'] = $post_id;
			}
		}
		
		return $fields;
}
/*$ks = get_option('report_table_data');
print_r($ks);*/

add_action( 'load-edit.php', function(){

   $screen = get_current_screen(); 

    // Only edit post screen:
   if( 'sales_wizard_report' === $screen->post_type || 'sales_wizard_webhook'=== $screen->post_type){
        // Before:
        add_action( 'all_admin_notices', function(){
            echo '<div class="salewizard-logo"><img src="'.SALES_WIZARD_APP_DIR_URL.'admin/images/saleswizard.png"></div>';
        });
    }
});


function sales_wizard_upgrade_new_version_two_completed() {
	
	$plugindata   = get_file_data(
			SALES_WIZARD_APP_FILE,
			array(
				'plugin_uri'  => 'Plugin URI',
				'plugin_name' => 'Plugin Name',
				'version'     => 'Version',
			)
		);
	$version = $plugindata['version'];
	if($version =='1.0.1'){
		$webhook_password 	= esc_attr(get_option('sales-wizard-api-key'));
		$update_check 		= esc_attr(get_option('sales-wizard-upgrade-confirm'));
		//delete_option('sales-wizard-upgrade-confirm');
		if($webhook_password && !$update_check){
			update_option('sales-wizard-upgrade-confirm','ok');
			$webhookid = create_new_upgrade_hook();
			upgrade_old_data_to_new_data($webhookid);
			$redirect_url = admin_url('post.php?post='.$webhookid.'&action=edit');
			wp_redirect($redirect_url);
		}
	}
}

add_action('admin_init','sales_wizard_upgrade_new_version_two_completed');

function create_new_upgrade_hook(){
	$webhook_url 		= esc_url(get_option('sales-wizard-api-url'));
	$webhook_password 	= esc_attr(get_option('sales-wizard-api-key'));
	$webhook_version 	= esc_attr(get_option('sales-wizard-api-version'));
	$webhook_mode 		= esc_attr(get_option('sales-wizard-app-test-mode'));
	$form_data = array(
				'post_type' => 'sales_wizard_webhook',
				'post_title'    => 'Form 1',
				'post_content'  => '',
				'post_status'   => 'publish',
			);
	$webhook_id = wp_insert_post( $form_data );
	
	update_post_meta($webhook_id,'sales_wizard_app_webhook_enable',1);
	update_post_meta($webhook_id,'sales_wizard_app_webhook_url',$webhook_url);
	update_post_meta($webhook_id,'sales_wizard_app_webhook_password',$webhook_password);
	update_post_meta($webhook_id,'sales_wizard_app_webhook_version',$webhook_version);
	update_post_meta($webhook_id,'sales_wizard_app_webhook_mode',$webhook_mode);
	
	
	
	$fiields_map = get_option('fields_mapping',true);
	$map = array();
	$form_type = 'wpcf7';
	foreach($fiields_map['column_ids'] as $index=>$field){
		$formid = $fiields_map['wpform_ids'][$index];
		if (is_numeric($formid)) {
			$form_type = 'wpforms';
		}
		$map[] = array('crm_field'=>$field,'form_field'=>$fiields_map['wpform_ids'][$index]);
	}
	update_post_meta($webhook_id,'sales_wizard_app_mapping_field',$map);
	
	update_post_meta($webhook_id,'sales_wizard_app_webhook_form_type',$form_type);
	if($form_type =='wpcf7'){
		//update_post_meta($webhook_id,'sales_wizard_app_webhook_form_id',$wpcf7form_id);
	}else{
		//update_post_meta($webhook_id,'sales_wizard_app_webhook_form_id',$wpform_id);
	}
	
	return $webhook_id;
}
function upgrade_old_data_to_new_data($webhookid){
	global $wpdb;
	$table_name = $wpdb->prefix.'sales_wizard_log';
	$results = $wpdb->get_results($wpdb->prepare( "SELECT * FROM {$table_name} WHERE sales_wizard_data"),ARRAY_A);
	if(!empty($results)){
		foreach($results as $result){
			$form_data = array(
				'post_type' => 'sales_wizard_report',
				'post_title'    => $result['form_name'],
				'post_content'  => '',
				'post_status'   => 'publish',
			);
			$report_id = wp_insert_post( $form_data );
			
			if ( $report_id && ! is_wp_error( $report_id ) ) {
				
				update_post_meta($report_id,'sales_wizard_lead_id',$result['lead_id']);
				update_post_meta($report_id,'sales_wizard_form_id',$result['form_id']);
				update_post_meta($report_id,'sales_wizard_webhook_id',$webhookid);
				update_post_meta($report_id,'sales_wizard_form_data',$result['sales_wizard_data']);
				update_post_meta($report_id,'sales_wizard_sent_trigger_qty',1);
				update_post_meta($report_id,'sales_wizard_log_status',1);
			}
		}
	}
}
 function disable_official_saleswizard_crm_updates( $value ) {
  //create an array of plugins you want to exclude from updates ( string composed by folder/main_file.php)
   $pluginsNotUpdatable = ['official-saleswizard-crm/sales-wizard-app.php'];

  if ( isset($value) && is_object($value) ) {
    foreach ($pluginsNotUpdatable as $plugin) {
        if ( isset( $value->response[$plugin] ) ) {
            unset( $value->response[$plugin] );
        }
      }
  }
  return $value;
}
add_filter( 'site_transient_update_plugins', 'disable_official_saleswizard_crm_updates' );
/*
$ks = get_option('report_table_data');
print_r($ks);*/