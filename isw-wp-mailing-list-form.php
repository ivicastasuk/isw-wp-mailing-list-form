<?php
/**
 * Plugin Name: 	ISW WP Mailing List Form
 * Description: 	The ISW WP Mailing List Form plugin integrates a subscription form into your WordPress site, allowing visitors to enter their email address to subscribe to your newsletter.
 * Version: 		1.0.3
 * Author: 			Ivica Stasuk
 * Author URI: 		https://www.stasuk.in.rs
 * License: 		GPL v3 or later
 * License URI: 	https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: 	isw-wp-mailing-list-form
 * Domain Path: 	/languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'ISW_ML_PLUGIN_FILE' ) ) {
    define( 'ISW_ML_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'ISW_ML_PLUGIN_DIR' ) ) {
    define( 'ISW_ML_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ISW_ML_PLUGIN_URL' ) ) {
    define( 'ISW_ML_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'ISW_ML_PLUGIN_VERSION' ) ) {
    define( 'ISW_ML_PLUGIN_VERSION', '1.0.3' );
}

function isw_ml_load_textdomain() {
    load_plugin_textdomain(
        'isw-wp-mailing-list-form',
        false,
        dirname( plugin_basename( ISW_ML_PLUGIN_FILE ) ) . '/languages'
    );
}

add_action( 'plugins_loaded', 'isw_ml_load_textdomain' );

require_once ISW_ML_PLUGIN_DIR . 'includes/isw-ml-core.php';
require_once ISW_ML_PLUGIN_DIR . 'includes/isw-ml-frontend.php';
require_once ISW_ML_PLUGIN_DIR . 'includes/isw-ml-admin.php';
