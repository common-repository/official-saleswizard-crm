<?php
if (!defined('ABSPATH')) {
    exit;
}

$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'wpcf7';

$settings_fields = apply_filters('sales_wizard_settings_fields', array());
?>
<div class="postbox" style="padding: 10px;margin-top: 10px">
    <!--  template file for admin settings. -->
    <form action="" method="POST" class="gen-section-form">
        <div class="section">
            <?php
            wp_nonce_field('setting_submit_nonce', 'setting_submit_fields');
            sales_wizard_app_fields_html($settings_fields[$active_tab]);
            ?>
        </div>
    </form>
</div>