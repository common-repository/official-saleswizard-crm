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
class Sales_Wizard_App_Custom_Post{
	public function __construct(){
		add_action('init',[$this,'register_custom_post']);
		add_filter('enter_title_here',[$this, 'post_title_placeholder']);
		add_filter('post_updated_messages', [$this,'post_published'] );
		add_filter('post_row_actions',[$this,'remove_view_link'],10,2);
		add_action('restrict_manage_posts', [$this,'add_extra_fields_wp_list_table']);
		add_filter( 'bulk_actions-edit-sales_wizard_report', [$this,'sales_wizard_report_extend_bulk_action_option_actions'] );
		add_filter( 'bulk_actions-edit-sales_wizard_webhook', [$this,'sales_wizard_webhook_extend_bulk_action_option_actions'] );
		add_filter( 'months_dropdown_results', [$this,'sales_wizard_report_remove_date'],10,1 );
	}
	public function register_custom_post(){
		$this->sales_wizard_app_report_post_type();
		$this->sales_wizard_app_webhook_post_type();
	}
	public function sales_wizard_app_report_post_type() {
	  $post_labels = array(
		'name'                  => '%2$s',
		'singular_name'         => '%1$s',
		'add_new'               => esc_html__('Add New %1$s', 'sales-wizard-app'),
		'add_new_item'          => esc_html__('Add New %1$s', 'sales-wizard-app'),
		'edit_item'             => esc_html__('Edit %1$s', 'sales-wizard-app'),
		'new_item'              => esc_html__('New %1$s', 'sales-wizard-app'),
		'all_items'             => esc_html__(' %2$s', 'sales-wizard-app'),
		'view_item'             => esc_html__('View %1$s', 'sales-wizard-app'),
		'search_items'          => esc_html__('Search %2$s', 'sales-wizard-app'),
		'not_found'             => esc_html__('No %2$s found', 'sales-wizard-app'),
		'not_found_in_trash'    => esc_html__('No %2$s found in Trash', 'sales-wizard-app'),
		'parent_item_colon'     => '',
		'menu_name'             => __('SalesWizard CRM','sales-wizard-app'),
		'featured_image'        => esc_html__('%1$s Image', 'sales-wizard-app'),
		'set_featured_image'    => esc_html__('Set %1$s Image', 'sales-wizard-app'),
		'remove_featured_image' => esc_html__('Remove %1$s Image', 'sales-wizard-app'),
		'use_featured_image'    => esc_html__('Use as %1$s Image', 'sales-wizard-app'),
		'filter_items_list'     => esc_html__('Filter %2$s list', 'sales-wizard-app'),
		'items_list_navigation' => esc_html__('%2$s list navigation', 'sales-wizard-app'),
		'items_list'            => esc_html__('%2$s list', 'sales-wizard-app'),
	  );

	  $singular_name 	= esc_html__('Report', 'sales-wizard-app');
	  $plural_name 		= esc_html__('Reports', 'sales-wizard-app');

	  foreach ($post_labels as $key => $value) {
		$post_labels[$key] = sprintf($value, $singular_name, $plural_name);
	  }

	 $post_args = array(
		'labels'            => apply_filters('sales_wizard_app_report_post_type_labels', $post_labels),
		'public'            => true,
		'publicly_queryable'=> false,
		'show_ui'           => true,
		'show_in_menu'      => true,
		'query_var'         => false,
		'menu_icon'         => SALES_WIZARD_APP_DIR_URL.'admin/images/sales-wizard-app-logo.png',
		'map_meta_cap'      => true,
		'has_archive'       => false,
		'hierarchical'      => false,
		'capabilities' => array('create_posts' => 'do_not_allow'),
		'supports'          => apply_filters('sales_wizard_app_report_fields_supports', array(""))
	  );

	  register_post_type(
		'sales_wizard_report',
		apply_filters('sales_wizard_app_report_post_type_args', $post_args)
	  );
	}
	public function sales_wizard_app_webhook_post_type(){
		$post_labels = array(
		'name'                  => '%2$s',
		'singular_name'         => '%1$s',
		'add_new'               => esc_html__('Add New %1$s', 'sales-wizard-app'),
		'add_new_item'          => esc_html__('Add New %1$s', 'sales-wizard-app'),
		'edit_item'             => esc_html__('Edit %1$s', 'sales-wizard-app'),
		'new_item'              => esc_html__('New %1$s', 'sales-wizard-app'),
		'all_items'             => esc_html__(' %2$s', 'sales-wizard-app'),
		'view_item'             => esc_html__('View %1$s', 'sales-wizard-app'),
		'search_items'          => esc_html__('Search %2$s', 'sales-wizard-app'),
		'not_found'             => esc_html__('No %2$s found', 'sales-wizard-app'),
		'not_found_in_trash'    => esc_html__('No %2$s found in Trash', 'sales-wizard-app'),
		'parent_item_colon'     => '',
		'menu_name'             => __('Booking Schedule','sales-wizard-app'),
		'featured_image'        => esc_html__('%1$s Image', 'sales-wizard-app'),
		'set_featured_image'    => esc_html__('Set %1$s Image', 'sales-wizard-app'),
		'remove_featured_image' => esc_html__('Remove %1$s Image', 'sales-wizard-app'),
		'use_featured_image'    => esc_html__('Use as %1$s Image', 'sales-wizard-app'),
		'filter_items_list'     => esc_html__('Filter %2$s list', 'sales-wizard-app'),
		'items_list_navigation' => esc_html__('%2$s list navigation', 'sales-wizard-app'),
		'items_list'            => esc_html__('%2$s list', 'sales-wizard-app'),
	  );

	  $singular_name 	= esc_html__('Webhook', 'sales-wizard-app');
	  $plural_name 		= esc_html__('Webhooks', 'sales-wizard-app');

	  foreach ($post_labels as $key => $value) {
		$post_labels[$key] = sprintf($value, $singular_name, $plural_name);
	  }

	  $post_args = array(
		'labels'            => apply_filters('sales_wizard_webhook_post_type_labels', $post_labels),
		'public'            => false,
		'publicly_queryable'=> true,
		'show_ui'           => true,
		'show_in_menu'       => 'edit.php?post_type=sales_wizard_report',
		'query_var'         => true,
		'menu_icon'         => 'dash_con',
		'map_meta_cap'      => true,
		'has_archive'       => true,
		'hierarchical'      => false,
		//'capabilities' 		=> array( 'create_posts' => false ),
		'supports'          => apply_filters('sales_wizard_webhook_supports', array('title'))
	  );

	  register_post_type(
		'sales_wizard_webhook',
		apply_filters('sales_wizard_webhook_post_type_args', $post_args)
	  );
	}
	
	
	
	public function post_title_placeholder($title) {
	  $screen = get_current_screen();
	  switch ( $screen->post_type ) {
		  case 'sales_wizard_webhook':
			$title = esc_html__('Webhook name', 'sales-wizard-app');
		  break;
		  case 'custom_fields':
			$title = esc_html__('Field Name', 'sales-wizard-app');
		  break;
	  }

	  return $title;
	}
	public function post_published( $messages ){
		$screen = get_current_screen();
		if($screen->id == 'sales_wizard_webhook'){
			$messages['post'][1] = __('Webhook has been updated');
		}
		return $messages;
	}
	public function remove_view_link($actions, $post){

		if ($post->post_type == "sales_wizard_webhook") {
			unset( $actions['view'] );
			unset($actions['inline hide-if-no-js']);
		}
		if ($post->post_type == "sales_wizard_report") {
			unset( $actions['view'] );
			unset($actions['inline hide-if-no-js']);
			unset($actions['edit']);
			//unset($actions['trash']);
			/*$url = admin_url( 'post.php?post='.$post->ID.'&action=edit');
			$edit_link = add_query_arg( array( 'action' => 'edit' ), $url );
			$actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>',esc_url( $edit_link ),esc_html( __( 'View Details', 'sales-wizard-app' ) ) );
			$status =  esc_attr(get_post_meta($post->ID,'sales_wizard_log_status',true));
			if($status !=1){
				$actions['send'] = sprintf('<a href="%s">%s</a>', wp_nonce_url(admin_url('post.php?post='.$post->ID.'&action=edit&sales_wizard_status_admin=send&contact_id=' . $post->ID), $post->ID), __('Send again', 'sales-wizard-app'));
			}*/
			//$actions['send'] = sprintf( '<a href="%1$s">%2$s</a>',esc_url( $edit_link ),esc_html( __( 'Send', 'sales-wizard-app' ) ) );
		}
		return $actions;
	}
	public function add_extra_fields_wp_list_table($where){
		if ($where === 'sales_wizard_report') {
			?>
			<div class="alignleft actions">
				<label for="filter-by-date" class="screen-reader-text"><?php _e('Filter by date range','sales-wizard-app');?></label>
				<button type="submit" name="export_quote_excel" id="export-quote-excel" class="button" value="export" ><?php _e('Export excel','sales-wizard-app');?></button>
			</div>
			<?php
		}
	}
	public function sales_wizard_report_extend_bulk_action_option_actions($bulk_array){
		$screen = get_current_screen();
		if($screen->id =='edit-sales_wizard_report'){
			unset($bulk_array['edit']);
			unset($bulk_array['trash']);
		}
		
		return $bulk_array;
	}
	public function sales_wizard_webhook_extend_bulk_action_option_actions($bulk_array){
		$screen = get_current_screen();
		if($screen->id =='edit-sales_wizard_webhook'){
			unset($bulk_array['edit']);
			unset($bulk_array['trash']);
		}
		
		return $bulk_array;
	}
	public function sales_wizard_report_remove_date($date){
		$screen = get_current_screen();
		if($screen->id =='edit-sales_wizard_webhook'){
			return array();
		}
		return $date;
	}
	
}
return new Sales_Wizard_App_Custom_Post();
