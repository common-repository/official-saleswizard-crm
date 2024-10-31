<?php 
if (!defined('ABSPATH')) {

    exit(); // Exit if accessed directly.
}

global $sale_wizard_app_obj, $sale_wizard_app_notices;

$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'wpcf7';

$tabs = apply_filters('sales_wizard_settings_tabs', array());
?>

<header>
    <div class="header-container bg-white">
        <h2 class="header-title"><?php _e( 'Official SalesWiazard CRM', 'sales-wizard-app' ); ?></h2>
    </div>
</header>
<div class="wrap">
    <nav class="nav-tab-wrapper navbar">
        <?php
        if (is_array($tabs) && !empty($tabs)) {
            foreach ($tabs as $tab_key => $tab) {
                $tab_classes = 'link ';
                if (!empty($active_tab) && $active_tab === $tab_key) {
                    $tab_classes .= 'nav-tab-active';
                }
                ?>
                <a id="<?php echo esc_attr($tab_key); ?>" href="<?php echo esc_url(admin_url('edit.php?post_type=sales_wizard_report&page=sales-wizard-app') . '&tab=' . esc_attr($tab_key)); ?>" class="nav-tab <?php echo $tab_classes; ?>"><?php echo esc_html($tab); ?></a>
                <?php
            }
        }
        ?>
    </nav>

    <div class="tab-content">
        <?php
        do_action('save_notice_message');
        ?>
        <?php
        do_action( 'wps_swa_before_general_settings_form' );
        // if submenu is directly clicked.
		$file_path = SALES_WIZARD_APP_DIR_PATH . 'admin/partials/sales-wizard-settings.php';
		$sale_wizard_app_obj->swa_plug_load_template( $file_path );
        do_action( 'wps_swa_after_general_settings_form' );
        ?>
    </div>

</div>