<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profile.webmonch.com
 * @since      1.0.0
 *
 * @package    Quote_Rides
 * @subpackage Quote_Rides/includes
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
 * @package    Quote_Rides
 * @subpackage Quote_Rides/includes
 * @author     Shafiq <shafiq6171@gmail.com>
 */
class Sales_Wizard_App_Post_Metabox {

    public static function register_custom_post_metabox() {
		add_action('add_meta_boxes', [__CLASS__, 'sales_wizard_app_webhook_metabox']);
		add_action('save_post',[__CLASS__, 'sales_wizard_app_webhook_metabox_save'], 10, 2);
    }

    public static function sales_wizard_app_webhook_metabox() {
			add_meta_box(
					'sales_wizard_report_metabox_id',
					__('Contacts','booking-quote'),
					[__CLASS__, 'render_sales_wizard_report_metabox_callback'],
					'sales_wizard_report',
					'normal',
					'high'
			);
			add_meta_box(
					'sales_wizard_app_webhook_metabox_id',
					__('Webhook settings','sales-wizard-app'),
					[__CLASS__, 'render_sales_wizard_app_webhook_metabox_callback'],
					'sales_wizard_webhook',
					'normal',
					'high'
			);
		  
    }
	public static function render_sales_wizard_report_metabox_callback(){
		global $post;
		$screen = get_current_screen();
		 if( 'add' != $screen->action ){
		$post_id		= $post->ID;
		$webhook_id 	= esc_attr(get_post_meta($post_id,'sales_wizard_webhook_id',true));
		$lead_id 		= esc_attr(get_post_meta($post_id,'sales_wizard_lead_id',true));	
		$webhook_mode 	= esc_attr(get_post_meta($post_id,'sales_wizard_log_status',true));
		$form_data 		= get_post_meta($post_id,'sales_wizard_form_data',true);
		$form_data_array = json_decode($form_data,true);
		
		?>
		<style>
		#postbox-container-1{
			display: none
		}
		</style>
		<div class="quote-wrapper">
				<table>
					<tbody>
						<tr>
							<td><strong><?php _e('Form Name','sales-wizard-app');?>:</strong></td>
							<td><?php echo $post->post_title;?></td>
						</tr>
						<tr>
							<td><strong><?php _e('Webhook','sales-wizard-app');?>:</strong></td>
							<td><?php echo get_the_title($webhook_id);?></td>
						</tr>
						<tr>
							<td><strong><?php _e('Lead','sales-wizard-app');?></strong></td>
							<td><?php echo $lead_id;?></td>
						</tr>
						<tr>
							<td><strong><?php _e('Status','sales-wizard-app');?></strong></td>
							<td><?php echo ($webhook_mode==1)?__('Sent','sales-wizard-app'):__('Not send','sales-wizard-app');?></td>
						</tr>
						<tr>
							<td><strong><?php _e('Date','sales-wizard-app');?></strong></td>
							<td><?php echo $post->post_date;?></td>
						</tr>
					</tbody>
				</table>
				<br/>
				<h3><?php _e('User data','sales-wizard-app');?></h3>
				<table width="100%">
					
					<tbody>
						<?php
						if(!empty($form_data_array)):
						unset($form_data_array['form_id']);
						unset($form_data_array['form_name']);
						unset($form_data_array['lead_id']);
						foreach($form_data_array as $key=>$data_array){
							$label = str_replace('_',' ',$key);
						?>
						<tr>
							<td><strong><?php echo ucwords(strtolower($label));?></strong></td>
							<td><strong><?php echo $data_array;?></strong></td>
						</tr>
						<?php } ?>
						<?php endif;?>
					</tbody>
				</table>
				
				
			</div>
		<?php
		 }
	}

    public static function render_sales_wizard_app_webhook_metabox_callback() {
        global $post;
		
        $fields = array(
			array(
				'label' 	=> __('Enable/Disable', 'sales-wizard-app'),
				'type' 		=> 'checkbox',
				'id' 		=> 'sales_wizard_app_webhook_enable',
				'value' 	=> '1',
				'checked' 	=> ( '1' === esc_attr(get_post_meta($post->ID, 'sales_wizard_app_webhook_enable', true)) ? '1' : '' ),
				'desc'		=> __('Check this box to send data to CRM.')
			),
			array(
				'label' 	=> __('Webhook URL', 'sales-wizard-app'),
				'type' 		=> 'text',
				'id'		=> 'sales_wizard_app_webhook_url',
				'value'		=> esc_url(get_post_meta($post->ID, 'sales_wizard_app_webhook_url', true)),
				'class'		=> 'regular-text'
			),
			array(
				'label' 	=> __('Password', 'sales-wizard-app'),
				'type' 		=> 'password',
				'id'		=> 'sales_wizard_app_webhook_password',
				'value'		=> esc_attr(get_post_meta($post->ID, 'sales_wizard_app_webhook_password', true)),
				'class'		=> 'regular-text'
			),
			array(
				'label' 	=> __('API Version', 'sales-wizard-app'),
				'type' 		=> 'text',
				'id'		=> 'sales_wizard_app_webhook_version',
				'value'		=> esc_attr(get_post_meta($post->ID, 'sales_wizard_app_webhook_version', true)),
				'class'		=> 'small-text',
				'placeholder'=>'1.0'
			),
			array(
				'label' 	=> __('Test mode', 'sales-wizard-app'),
				'type' 		=> 'checkbox',
				'id' 		=> 'sales_wizard_app_webhook_mode',
				'value' 	=> '1',
				'checked' 	=> ( '1' === esc_attr(get_post_meta($post->ID, 'sales_wizard_app_webhook_mode', true)) ? '1' : '' ),
			),
			array(
				'label' 	=> __('Form Type', 'sales-wizard-app'),
				'type' 		=> 'select',
				'id'		=> 'sales_wizard_app_webhook_form_type',
				'value'		=> esc_attr(get_post_meta($post->ID, 'sales_wizard_app_webhook_form_type', true)),
				'class'		=> 'small-text',
				'options'	=> array(
					'WPCF7'	=>'Contact Form 7',
					'WPFORMS'=>'WPForms',
				)
			),
			array(
				'label' 	=> __('Form ', 'sales-wizard-app'),
				'type' 		=> 'select',
				'id'		=> 'sales_wizard_app_webhook_wpcf7form_id',
				'value'		=> esc_attr(get_post_meta($post->ID, 'sales_wizard_app_webhook_form_id', true)),
				'class'		=> 'small-text wpcf7',
				'options'	=> get_all_wpcf7_contact_form(),
				'wrapper_class'=>'hidden-el sales_wizard_app_wpcf7'
			),
			array(
				'label' 	=> __('Form ', 'sales-wizard-app'),
				'type' 		=> 'select',
				'id'		=> 'sales_wizard_app_webhook_wpform_id',
				'value'		=> esc_attr(get_post_meta($post->ID, 'sales_wizard_app_webhook_form_id', true)),
				'class'		=> 'small-text wpform',
				'options'	=> get_all_wpforms(),
				'wrapper_class'=>'hidden-el sales_wizard_app_wpform'
			),
			array(
				'label' => __( 'Field name of contact form 7', 'sales-wizard-app' ),
				'type'  => 'field_mapping',
				'desc'  => __( 'Check this box to send data to CRM.', 'sales-wizard-app' ),
				'id'    => 'sales_wizard_app_mapping_field',
				'value'		=> get_post_meta($post->ID, 'sales_wizard_app_mapping_field', true),
				'class' => '',
				//'value' => esc_attr(get_option('wpcf7')),
			),
        );
        $fields = apply_filters('sales_wizard_app_webhook_fields', $fields);
       sales_wizard_app_fields_html($fields);
    }
	public static function sales_wizard_app_webhook_metabox_save($post_id, $post){
		if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit'])) {
				return;
			}
			if (isset($post->post_type) && 'revision' == $post->post_type) {
				return;
			}
			if (isset($post->post_type) && 'sales_wizard_webhook' == $post->post_type) {
			
			$webhook_enable		= (isset($_POST['sales_wizard_app_webhook_enable']) && !empty($_POST['sales_wizard_app_webhook_enable']))?1:"";
			$webhook_url		= isset($_POST['sales_wizard_app_webhook_url'])?sanitize_text_field($_POST['sales_wizard_app_webhook_url']):"";
			$webhook_password	= isset($_POST['sales_wizard_app_webhook_password'])?sanitize_text_field($_POST['sales_wizard_app_webhook_password']):"";
			$webhook_version	= isset($_POST['sales_wizard_app_webhook_version'])?sanitize_text_field($_POST['sales_wizard_app_webhook_version']):"";
			$webhook_mode		= (isset($_POST['sales_wizard_app_webhook_mode']) && !empty($_POST['sales_wizard_app_webhook_mode']))?1:"";
			$form_type			= isset($_POST['sales_wizard_app_webhook_form_type'])?sanitize_text_field($_POST['sales_wizard_app_webhook_form_type']):"";
			$wpcf7form_id		= isset($_POST['sales_wizard_app_webhook_wpcf7form_id'])?sanitize_text_field($_POST['sales_wizard_app_webhook_wpcf7form_id']):"";
			$wpform_id			= isset($_POST['sales_wizard_app_webhook_wpform_id'])?sanitize_text_field($_POST['sales_wizard_app_webhook_wpform_id']):"";
			$mapping_field		= !empty($_POST['sales_wizard_app_mapping_field'])?map_deep($_POST['sales_wizard_app_mapping_field'],'sanitize_text_field'):array();
		

			update_post_meta($post_id,'sales_wizard_app_webhook_enable',$webhook_enable);
			update_post_meta($post_id,'sales_wizard_app_webhook_url',$webhook_url);
			update_post_meta($post_id,'sales_wizard_app_webhook_password',$webhook_password);
			update_post_meta($post_id,'sales_wizard_app_webhook_version',$webhook_version);
			update_post_meta($post_id,'sales_wizard_app_webhook_mode',$webhook_mode);
			update_post_meta($post_id,'sales_wizard_app_webhook_form_type',$form_type);
			
			update_post_meta($post_id,'sales_wizard_app_mapping_field',$mapping_field);
			if($form_type =='wpcf7'){
				update_post_meta($post_id,'sales_wizard_app_webhook_form_id',$wpcf7form_id);
			}else{
				update_post_meta($post_id,'sales_wizard_app_webhook_form_id',$wpform_id);
			}
			
		}
	}
}
