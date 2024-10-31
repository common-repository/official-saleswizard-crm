<?php

class sales_wizard_Register_Post_Column {

    public function __construct() {
		add_filter('manage_sales_wizard_report_posts_columns', [$this, 'render_sales_wizard_report_post_columns']);
		add_filter('manage_sales_wizard_webhook_posts_columns',[$this, 'render_sales_wizard_webhook_post_columns']);
	}

    public function render_sales_wizard_report_post_columns($original_columns) {
		
        $listing_columns = array(
            'cb'		=> '<input type="checkbox"/>',
			'title'		=> esc_html__('Form Name', 'sales-wizard-app'),
			'webhook'	=> esc_html__('Webhook', 'sales-wizard-app'),
			//'contacts'	=> esc_html__('Contacts', 'sales-wizard-app'),
			'status'	=> esc_html__('Status', 'sales-wizard-app'),
			'lead'		=> esc_html__('Lead', 'sales-wizard-app'),
			'action'	=> esc_html__('Action', 'sales-wizard-app'),
			'date'		=> esc_html__('Date', 'sales-wizard-app'),
        );

        return apply_filters('sales_wizard_report_columns', $listing_columns);
    }
	public function render_sales_wizard_webhook_post_columns($columns) {
		$columns = array(
            'cb'		=> '<input type="checkbox"/>',
			'title'		=> esc_html__('Webhook', 'sales-wizard-app'),
			'test_mode'	=> esc_html__('Test Mode', 'sales-wizard-app'),
			'form_type'	=> esc_html__('Form Type', 'sales-wizard-app'),
			'form_name'	=> esc_html__('Form name', 'sales-wizard-app'),
        );

        return apply_filters('sales_wizard_webhook_columns', $columns);
    }

}

return new sales_wizard_Register_Post_Column();
