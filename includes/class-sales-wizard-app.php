<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://webmonch.com
 * @since      1.0.0
 *
 * @package    Sales_Wizard_App
 * @subpackage Sales_Wizard_App/includes
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
 * @package    Sales_Wizard_App
 * @subpackage Sales_Wizard_App/includes
 * @author     Shafiq <shafiq6171@gmail.com>
 */
class Sales_Wizard_App {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sales_Wizard_App_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SALES_WIZARD_APP_VERSION' ) ) {
			$this->version = SALES_WIZARD_APP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sales-wizard-app';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sales_Wizard_App_Loader. Orchestrates the hooks of the plugin.
	 * - Sales_Wizard_App_i18n. Defines internationalization functionality.
	 * - Sales_Wizard_App_Admin. Defines all hooks for the admin area.
	 * - Sales_Wizard_App_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sales-wizard-app-loader.php';
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sales-wizard-app-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sales-wizard-app-custom-post.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sales-wizard-app-post-metabox.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'addon/includes/autoload.php';;
		

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sales-wizard-app-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sales-wizard-app-register-post-column.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sales-wizard-app-manage-post-column.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sales-wizard-app-public.php';

		$this->loader = new Sales_Wizard_App_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sales_Wizard_App_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sales_Wizard_App_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Sales_Wizard_App_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'sales_wizard_app_admin_menu' );
		$this->loader->add_action( 'init', $plugin_admin, 'sales_wizard_new_menu_redirect', 10 );
		// All admin actions and filters after License Validation goes here.
		$this->loader->add_filter( 'sales_wizard_settings_tabs', $plugin_admin, 'sales_wizard_settings_tabs', 15 );
		$this->loader->add_filter( 'sales_wizard_settings_fields', $plugin_admin, 'sales_wizard_settings_fields', 10 );
		
		// Saving tab settings.
		
		$this->loader->add_action( 'admin_init', $plugin_admin, 'swa_admin_save_tab_settings' );
		$this->loader->add_action('admin_init', $plugin_admin, 'sfw_admin_send_not_sent_contacts');
		$this->loader->add_action('admin_init', $plugin_admin, 'sfw_admin_export_contacts');
		 add_action('init', array('Sales_Wizard_App_Post_Metabox', 'register_custom_post_metabox'));
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Sales_Wizard_App_Public( $this->get_plugin_name(), $this->get_version() );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		//$this->loader->add_action( 'wpcf7_mail_sent', $plugin_public, 'swa_sent_contact_form7_data_to_crm' );
		$this->loader->add_action( 'wpcf7_before_send_mail', $plugin_public, 'swa_sent_contact_form7_data_to_crm' );
		$this->loader->add_action( 'wpforms_process_complete', $plugin_public, 'swa_sent_wpform_data_to_crm',10, 4  );
		$this->loader->add_action( 'init', $plugin_public, 'trigger_schedule_event' );
		$this->loader->add_action( 'sales_wizard_report_unsent_event_hook', $plugin_public, 'sent_unsent_sales_wizard_report' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sales_Wizard_App_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
	
		/**
	 * Locate and load appropriate tempate.
	 *
	 * @since   1.0.0
	 */
	public function swa_plug_load_template( $content_path ) {

		if ( file_exists( $content_path ) ) {
			include $content_path;
		} else {
			/* translators: %s: file path */
			$sfw_notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in this plugin. Please contact us!', 'subscriptions-for-woocommerce' ), $content_path );
			$this->swa_plug_admin_notice( $sfw_notice, 'error' );
		}
	}
	/**
	 * Show admin notices.
	 *
	 * @param  string $swa_message    Message to display.
	 * @param  string $type       notice type, accepted values - error/update/update-nag.
	 * @since  1.0.0
	 */
	public static function swa_plug_admin_notice( $sfw_message, $type = 'error' ) {

		$sfw_classes = 'notice ';

		switch ( $type ) {

			case 'update':
				$sfw_classes .= 'updated is-dismissible';
				break;

			case 'update-nag':
				$sfw_classes .= 'update-nag is-dismissible';
				break;

			case 'success':
				$sfw_classes .= 'notice-success is-dismissible';
				break;

			default:
				$sfw_classes .= 'notice-error is-dismissible';
		}

		$sfw_notice  = '<div class="' . esc_attr( $sfw_classes ) . ' wps-errorr-8">';
		$sfw_notice .= '<p>' . esc_html( $sfw_message ) . '</p>';
		$sfw_notice .= '</div>';

		echo wp_kses_post( $sfw_notice );
	}

}
