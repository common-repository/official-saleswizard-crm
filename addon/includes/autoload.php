<?php 
/*
check contact form 7 plugin is active.
*/
if(class_exists('WPCF7')){
	require SALES_WIZARD_APP_DIR_PATH. 'addon/includes/settings.php';
	require SALES_WIZARD_APP_DIR_PATH. 'addon/includes/country-text.php';
	require SALES_WIZARD_APP_DIR_PATH. 'addon/includes/phone-text.php';
	require SALES_WIZARD_APP_DIR_PATH. 'addon/includes/include-js-css.php';
}