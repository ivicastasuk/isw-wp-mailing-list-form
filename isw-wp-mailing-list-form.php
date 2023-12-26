<?php
/**
 * Plugin Name: ISW WP Mailing List Form
 * Description: The ISW WP Mailing List Form plugin seamlessly integrates a subscription form into your WordPress site, allowing visitors to enter their email addresses to subscribe to your newsletter.
 * Version: 0.1
 * Author: Ivica Stasuk
 * Author URI: https://www.stasuk.in.rs
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: isw-wp-ml-form
*/

function add_isw_mailinglist_form(){
	$ml_message = '';
	if(isset($_GET['ml_submitted']) && $_GET['ml_submitted'] == '1'){
		$ml_message = '<div class="isw-ml-form-container"><div class="isw-ml-form-message">Your E-mail address was successfully submitted. Thank you!</div>'; 
	}
	$isw_ml_form = $ml_message . '<form action="" method="post">
							<input type="text" name="isw_ml_name" placeholder="Your name..." required>
							<input type="email" name="isw_ml_email" placeholder="Your E-Mail address..." required>
							<input type="submit" name="isw_ml_submit" value="Subscribe to our mailing list">
						</form>
					</div>';
	return $isw_ml_form;
}
add_shortcode('add_isw_ml_form', 'add_isw_mailinglist_form');

function isw_mailing_list_form_styles(){
	$css_url = plugins_url('isw-wp-mailing-list-form.css', __FILE__);

	wp_register_style('isw-wp-ml-form', $css_url);

	wp_enqueue_style('isw-wp-ml-form');
}
add_action('wp_enqueue_scripts', 'isw_mailing_list_form_styles');

function isw_mailing_list_form_admin_styles(){
	$css_url = plugins_url('isw-wp-mailing-list-form-admin.css', __FILE__);

	wp_register_style('isw-wp-ml-form-admin', $css_url);

	wp_enqueue_style('isw-wp-ml-form-admin');
}
add_action('admin_enqueue_scripts', 'isw_mailing_list_form_admin_styles');

function save_ml_form_to_db(){
	if(isset($_POST['isw_ml_submit'])){
		global $wpdb;
		$name = sanitize_text_field($_POST['isw_ml_name']);
		$email = sanitize_email($_POST['isw_ml_email']);
		$isw_table = $wpdb->prefix . 'isw_ml';

		if($wpdb->get_var("SHOW TABLES LIKE '{$isw_table}'") != $isw_table){
			$sql =  "CREATE TABLE $isw_table (
					id int(11) NOT NULL AUTO_INCREMENT,
					name VARCHAR(255) NOT NULL,
					email VARCHAR(255) NOT NULL,
					UNIQUE KEY id (id)
					);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		
		$wpdb->insert($isw_table, array('name' => $name, 'email' => $email));

		$redirect_url = add_query_arg('ml_submitted', '1', wp_get_referer());
        wp_redirect($redirect_url);
        exit;
	}
}
add_action('init', 'save_ml_form_to_db');

function isw_ml_form_menu(){
	add_menu_page(
		'ISW Mailing List',
		'ISW Mailing List',
		'manage_options',
		'isw-ml-form',
		'isw_ml_form_page'
	);
}
add_action('admin_menu', 'isw_ml_form_menu');

function isw_ml_form_page(){
	if (!current_user_can('manage_options')) {
		wp_die('You don\'t have access to this page.');
	}

	if(isset($_POST['export_emails'])){
		isw_ml_form_export_csv();
	}

	global $wpdb;
	$isw_table = $wpdb->prefix . 'isw_ml';

	$data = $wpdb->get_results("SELECT * FROM $isw_table");


	echo '<div class="wrap"><h1>Subscribed emails</h1>';
	echo '<div class="notice"><h3>How to...</h3><p>Add <code>[add_isw_ml_form]</code> shortcode where you want to add your mailing list form.</p></div>';
	echo '<table class="wp-list-table widefat fixed striped">';
	echo '<thead><tr><th>Name</th><th>Email</th></tr></thead>';
	echo '<tbody>';

	foreach($data as $item){
		echo '<tr><td>' . esc_html($item->name) . '</td><td>' . esc_html($item->email) . '</td></tr>';
	}

	$export_url = plugins_url('export-handler.php', __FILE__);

	echo '</tbody></table>';
	echo '<div class="tablenav">';
	// echo '<form method="post">';
	// echo '<input type="submit" name="export_emails" value="Export as CSV" class="alignright" />';
	// echo '</form>';
	echo '<a href="' . esc_url($export_url) . '" class="button alignright">Export as CSV</a>';
	echo '</div>';
	echo '</div>';

}

// function isw_ml_form_export_csv(){

// 	if (!current_user_can('export')) {
// 		wp_die('You don\'t have access to export this data.');
// 	}

// 	ob_start();

// 	global $wpdb;
// 	$isw_table = $wpdb->prefix . 'isw_ml';

// 	$data = $wpdb->get_results("SELECT * FROM $isw_table");

// 	if(count($data) > 0){
// 		$delimiter = ",";
// 		$filename = "isw-ml_" . date('Ymd') . ".csv";

// 		header('Content-Type: text/csv');
// 		header('Content-Disposition: attachment; filename="' . $filename . '";');
// 		$f = fopen('php://output', 'w');
// 		$headers = array('DisplayName', 'PrimaryEmail');
// 		fputcsv($f, $headers, $delimiter);

// 		foreach($data as $row){
// 			$line_data = array($row->name, $row->email);
// 			fputcsv($f, $line_data, $delimiter);
// 		}

// 		fseek($f, 0);

// 		fclose($f);
// 	}

// 	ob_end_flush();
// 	exit;
// }