<?php

class Manage_sales_wizard_Post_Column {

    public function __construct() {
        add_action('manage_sales_wizard_webhook_posts_custom_column', [$this, 'render_sales_wizard_webhook_columns'], 10, 2);
        add_action('manage_sales_wizard_report_posts_custom_column', [$this, 'render_sales_wizard_report_columns'], 10, 2);
	}

    function render_sales_wizard_webhook_columns($column_name, $post_id) {
		switch ($column_name) {
			case 'test_mode':
				$webhook_mode =  esc_attr(get_post_meta($post_id,'sales_wizard_app_webhook_mode',true));
				echo ($webhook_mode==1)?__('Yes','sales-wizard-app'):__('No','sales-wizard-app');
			break;
			case 'form_type':
				$form_type =  esc_attr(get_post_meta($post_id,'sales_wizard_app_webhook_form_type',true));
				echo ($form_type=='wpcf7')?__('Contact form 7','sales-wizard-app'):__('WPFORM','sales-wizard-app');
			break;
			case 'form_name':
				$form_id =  esc_attr(get_post_meta($post_id,'sales_wizard_app_webhook_form_id',true));
				echo get_the_title($form_id);
			break;
        }
    } 
	function render_sales_wizard_report_columns($column_name, $post_id) {
		switch ($column_name) {
			case 'status':
				$webhook_mode =  esc_attr(get_post_meta($post_id,'sales_wizard_log_status',true));
				echo ($webhook_mode==1)?__('Sent','sales-wizard-app'):__('Not send','sales-wizard-app');
			break;
			case 'webhook':
				$webhook_id =  esc_attr(get_post_meta($post_id,'sales_wizard_webhook_id',true));
				echo get_the_title($webhook_id);
			break;
			case 'contacts':
				/*$form_data 		= get_post_meta($post_id,'sales_wizard_form_data',true);
				$contacts = json_decode($form_data,true);
				$contact_string = "";
				unset($contacts['form_id']);
				unset($contacts['form_name']);
				foreach($contacts as $contact_key=>$contact_value){
					$contact_string .= '<p><strong>'.str_replace('_',' ',ucwords(strtolower($contact_key))).':</strong> '.$contact_value.'</p>';
				}
                echo $contact_string ;*/
			break;
			case 'lead':
				$lead_id =  esc_attr(get_post_meta($post_id,'sales_wizard_lead_id',true));
				echo $lead_id;
			break;
			case 'action':
				$url = admin_url( 'post.php?post='.$post_id.'&action=edit');
				$edit_link = add_query_arg( array( 'action' => 'edit' ), $url );
				$actions = sprintf( '<a href="%1$s">%2$s</a>',esc_url( $edit_link ),esc_html( __( 'View Details', 'sales-wizard-app' ) ) );
				$status =  esc_attr(get_post_meta($post_id,'sales_wizard_log_status',true));
				if($status !=1){
					$actions .= '|'. sprintf('<a href="%s">%s</a>', wp_nonce_url(admin_url('post.php?post='.$post_id.'&action=edit&sales_wizard_status_admin=send&contact_id=' . $post_id), $post_id), __('Send again', 'sales-wizard-app'));
				}
				print_r($actions);
			break;
        }
    }
}

return new Manage_sales_wizard_Post_Column();
