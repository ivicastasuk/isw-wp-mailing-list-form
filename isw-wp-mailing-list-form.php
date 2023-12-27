<?php
/**
 * Plugin Name: 	ISW WP Mailing List Form
 * Description: 	The ISW WP Mailing List Form plugin integrates a subscription form into your WordPress site, allowing visitors to enter their email address to subscribe to your newsletter.
 * Version: 		1.0.0
 * Author: 			Ivica Stasuk
 * Author URI: 		https://www.stasuk.in.rs
 * License: 		GPL v3 or later
 * License URI: 	https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: 	isw-wp-ml-form
 * Domain Path: 	/languages
*/

function add_isw_mailinglist_form(){
	$ml_message = '';
	$ml_message .= '<div class="isw-ml-form-container">';
	if(isset($_GET['ml_submitted']) && $_GET['ml_submitted'] == '1'){
		$ml_message .= '<div class="isw-ml-form-message">Your E-mail address was successfully submitted. Thank you!</div>'; 
	}

	$input_text_color = get_option('input_text_color', '#001f53');
	$input_border_color = get_option('input_border_color', '#808080');
	$button_bg_color = get_option('button_bg_color', '#001f53');
	$button_text_color = get_option('button_text_color', '#ffffff');
	$button_text = get_option('button_text', 'Subscribe to our mailing list');
	
	$isw_ml_form = $ml_message . '<form action="" method="post">
							<input type="text" name="isw_ml_name" placeholder="Your name..." required style="color:' . esc_attr($input_text_color) . '; border-color:' . esc_attr($input_border_color) . ';">
							<input type="email" name="isw_ml_email" placeholder="Your E-Mail address..." required style="color:' . esc_attr($input_text_color) . '; border-color:' . esc_attr($input_border_color) . ';">
							<input type="submit" name="isw_ml_submit" value="' . sanitize_text_field($button_text) . '" style="background-color:' . esc_attr($button_bg_color) . '; color:' . esc_attr($button_text_color) . ';">
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
					is_new int(11) NOT NULL,
					UNIQUE KEY id (id)
					);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		
		$wpdb->insert($isw_table, array('name' => $name, 'email' => $email, 'is_new' => 1));

		isw_send_thankyou_email($email, $name);

		$redirect_url = add_query_arg('ml_submitted', '1', wp_get_referer());
        wp_redirect($redirect_url);
        exit;
	}
}
add_action('init', 'save_ml_form_to_db');

function isw_ml_form_menu(){

	$new_entries_count = isw_get_new_entries_count();
	$menu_title = 'ISW ML' . ($new_entries_count > 0 ? " <span class='update-plugins count-$new_entries_count'><span class='plugin-count'>" . intval($new_entries_count) . "</span></span>" : '');

	$dashboard_hook = add_menu_page(
		'ISW Mailing List',
		$menu_title,
		'manage_options',
		'isw-ml-form-dashboard',
		'isw_ml_form_admin_page_dashboard',
		'dashicons-email-alt',
		15
	);

	add_submenu_page(
		'isw-ml-form-dashboard',
		'Dashboard',
		'Dashboard',
		'manage_options',
		'isw-ml-form-dashboard',
		'isw_ml_form_admin_page_dashboard'
	);

	add_submenu_page(
		'isw-ml-form-dashboard',
		'Customization',
		'Customization',
		'manage_options',
		'isw-ml-form-customization',
		'isw_ml_form_admin_page_customization'
	);

	add_action('load-' . $dashboard_hook, 'isw_reset_new_entries');

}
add_action('admin_menu', 'isw_ml_form_menu');

function isw_ml_form_admin_page_dashboard(){
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
	echo '<a href="' . esc_url($export_url) . '" class="button alignright">Export as CSV</a>';
	echo '</div>';
	echo '</div>';

}

function isw_ml_form_admin_page_customization(){

	if (!current_user_can('manage_options')) {
		wp_die('You don\'t have access to this page.');
	}
		
	?>
	<div class="wrap">
		<h2>Form Customization</h2>
		<form method="post" action="options.php">
			<?php
			settings_fields('isw-ml-input-settings-group');
			do_settings_sections('isw-ml-input-settings');
			submit_button();
			?>
		</form>
		<form method="post" action="options.php">
			<?php
			settings_fields('isw-ml-button-settings-group');
			do_settings_sections('isw-ml-button-settings');
			submit_button();
			?>
		</form>
	<?php $email_template = get_option('isw_ml_email_template', 'Dear {{name}}, thank you for your subscription!'); ?>
		<h2>Response email</h2>
		<p><code>{{name}}</code> - Subscribed user name</p>
		<form method="post" action="options.php">
			<?php
			settings_fields('isw-ml-settings-group');
			do_settings_sections('isw-ml-settings');
			?>
			<textarea id="isw_ml_email_template" name="isw_ml_email_template" rows="5" cols="70"><?php echo esc_textarea($email_template); ?></textarea>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}


function isw_ml_admin_scripts() {
	wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('isw-ml-admin-script', plugins_url('isw-wp-mailing-list-form.js', __FILE__), array('wp-color-picker'), false, true);
}
add_action('admin_enqueue_scripts', 'isw_ml_admin_scripts');

function isw_ml_settings_init(){

	if (isset($_POST['button_text']) && trim($_POST['button_text']) == '') {
        $_POST['button_text'] = 'Subscribe to our mailing list';
    }

	if (isset($_POST['button_text'])) {
		$_POST['button_text'] = sanitize_text_field($_POST['button_text']);
	}

	register_setting('isw-ml-input-settings-group', 'input_text_color');
	register_setting('isw-ml-input-settings-group', 'input_border_color');

	add_settings_section(
		'isw-ml-settings-input-section',
		'',
		'isw_ml_settings_input_section_callback',
		'isw-ml-input-settings'
	);

	add_settings_field(
		'input_text_color',
		'Input field text color',
		'isw_ml_input_text_color_callback',
		'isw-ml-input-settings',
		'isw-ml-settings-input-section'
	);

	add_settings_field(
		'input_border_color',
		'Input field border color',
		'isw_ml_input_border_color_callback',
		'isw-ml-input-settings',
		'isw-ml-settings-input-section'
	);

	register_setting('isw-ml-button-settings-group', 'button_bg_color');
	register_setting('isw-ml-button-settings-group', 'button_text');
	register_setting('isw-ml-button-settings-group', 'button_text_color');

	add_settings_section(
		'isw-ml-settings-button-section',
		'',
		'isw_ml_settings_button_section_callback',
		'isw-ml-button-settings'
	);

	add_settings_field(
		'button_bg_color',
		'Button background color',
		'isw_ml_btn_bg_color_callback',
		'isw-ml-button-settings',
		'isw-ml-settings-button-section'
	);
	add_settings_field(
		'button_text_color',
		'Button text color',
		'isw_ml_btn_text_color_callback',
		'isw-ml-button-settings',
		'isw-ml-settings-button-section'
	);
	add_settings_field(
		'button_text',
		'Button text',
		'isw_ml_btn_text_callback',
		'isw-ml-button-settings',
		'isw-ml-settings-button-section'
	);

	register_setting('isw-ml-settings-group', 'isw_ml_email_template');
}
add_action('admin_init', 'isw_ml_settings_init');

/* input fields */
function isw_ml_settings_input_section_callback(){
	echo '<div style="border: 1px solid #404040; border-radius: 0.25rem; background-color: #808080; padding: 0.5rem 1rem;"><h3 style="margin: 0; color: #ffffff;">Style your input fields...</h3></div>';
}

function isw_ml_input_text_color_callback(){
	$input_text_color = get_option('input_text_color', '#001f53');
	echo '<input type="text" id="input_text_color" name="input_text_color" value="' . esc_attr($input_text_color) . '" />';
}

function isw_ml_input_border_color_callback(){
	$input_border_color = get_option('input_border_color', '#808080');
	echo '<input type="text" id="input_border_color" name="input_border_color" value="' . esc_attr($input_border_color) . '" />';
}

/* button */
function isw_ml_settings_button_section_callback(){
	echo '<div style="border: 1px solid #404040; border-radius: 0.25rem; background-color: #808080; padding: 0.5rem 1rem;"><h3 style="margin: 0; color: #ffffff;">Style your button...</h3></div>';
}

function isw_ml_btn_bg_color_callback(){
	$button_bg_color = get_option('button_bg_color', '#001f53');
	echo '<input type="text" id="btn_bg_color" name="button_bg_color" value="' . esc_attr($button_bg_color) . '" />';
}

function isw_ml_btn_text_color_callback(){
	$button_text_color = get_option('button_text_color', '#ffffff');
	echo '<input type="text" id="btn_text_color" name="button_text_color" value="' . esc_attr($button_text_color) . '" />';
}

function isw_ml_btn_text_callback(){
	$button_text = get_option('button_text', 'Subscribe to our mailing list');
	echo '<input type="text" id="btn_text" name="button_text" value="' . sanitize_text_field($button_text) . '" style="width: 100%;" />';
}

/* funkcije za proveru broja novih unosa i njihov pregled i prikazivanje */
function isw_get_new_entries_count(){
	global $wpdb;

	$isw_table = $wpdb->prefix . 'isw_ml';

	$new_entries_count = $wpdb->get_var("SELECT COUNT(*) FROM $isw_table WHERE is_new = 1");

	return $new_entries_count;
}

function isw_reset_new_entries() {
    global $wpdb;
    $isw_table = $wpdb->prefix . 'isw_ml';

    $wpdb->query("UPDATE $isw_table SET is_new = 0 WHERE is_new = 1");
}

/* funkcija za slanje povratnog emaila */
function isw_send_thankyou_email($to_email, $subscriber_name) {
    $subject = 'Thank you for your subscription!';
    $template = get_option('isw_ml_email_template', 'Dear {{name}}, thank you for your subscription!');
    
    $message = str_replace('{{name}}', $subscriber_name, $template);
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    wp_mail($to_email, $subject, $message, $headers);
}
