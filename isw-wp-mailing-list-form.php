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
        $ml_message .= '<div class="isw-ml-form-message">' . esc_html(get_option('ml_success_message', 'Your E-mail address was successfully submitted. Thank you!')) . '</div>'; 
    }
    if(isset($_GET['ml_error']) && $_GET['ml_error'] == '1'){
        $ml_message .= '<div class="isw-ml-form-message isw-ml-error">' . esc_html(get_option('ml_error_message', 'There was an error with your submission. Please try again.')) . '</div>'; 
    }

    $input_text_color = get_option('input_text_color', '#001f53');
    $input_border_color = get_option('input_border_color', '#808080');
    $button_bg_color = get_option('button_bg_color', '#001f53');
    $button_text_color = get_option('button_text_color', '#ffffff');
    $button_text = get_option('button_text', 'Subscribe to our mailing list');
    $name_placeholder = get_option('input_name_placeholder', 'Your name...');
    $email_placeholder = get_option('input_email_placeholder', 'Your E-Mail address...');
    $button_font_family = get_option('button_font_family', 'inherit');
    $button_font_size = get_option('button_font_size', '16');
    $button_font_style = get_option('button_font_style', 'normal');
    $button_font_weight = get_option('button_font_weight', 'normal');
    $button_line_height = get_option('button_line_height', '1.2');
    $button_border_width = get_option('button_border_width', '1');
    $button_border_color = get_option('button_border_color', '#001f53');
    $button_border_style = get_option('button_border_style', 'solid');
    $button_box_shadow = get_option('button_box_shadow', '0 2px 6px rgba(0,0,0,0.15)');
    $input_outline_color = get_option('input_outline_color', '#2684FF');

    $isw_ml_form = $ml_message . '<form action="" method="post">
                            <input type="text" name="isw_ml_name" placeholder="' . esc_attr($name_placeholder) . '" required style="color:' . esc_attr($input_text_color) . '; border-color:' . esc_attr($input_border_color) . ';" onfocus="this.style.outlineColor=\'' . esc_attr($input_outline_color) . '\';">
                            <input type="email" name="isw_ml_email" placeholder="' . esc_attr($email_placeholder) . '" required style="color:' . esc_attr($input_text_color) . '; border-color:' . esc_attr($input_border_color) . ';" onfocus="this.style.outlineColor=\'' . esc_attr($input_outline_color) . '\';">
                            ' . wp_nonce_field('isw_ml_form_action', 'isw_ml_form_nonce', true, false) . '
                            <input type="submit" name="isw_ml_submit" value="' . esc_attr($button_text) . '" style="
        background-color:' . esc_attr($button_bg_color) . ';
        color:' . esc_attr($button_text_color) . ';
        font-family:' . esc_attr($button_font_family) . ';
        font-size:' . esc_attr($button_font_size) . 'px;
        font-style:' . esc_attr($button_font_style) . ';
        font-weight:' . esc_attr($button_font_weight) . ';
        line-height:' . esc_attr($button_line_height) . ';
        border-width:' . esc_attr($button_border_width) . 'px;
        border-color:' . esc_attr($button_border_color) . ';
        border-style:' . esc_attr($button_border_style) . ';
        box-shadow:' . esc_attr($button_box_shadow) . ';
    ">
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
        if ( ! isset($_POST['isw_ml_form_nonce']) || ! wp_verify_nonce($_POST['isw_ml_form_nonce'], 'isw_ml_form_action') ) {
            $redirect_url = add_query_arg('ml_error', '1', wp_get_referer());
            wp_safe_redirect($redirect_url);
            exit;
        }
        global $wpdb;
        $name = sanitize_text_field($_POST['isw_ml_name']);
        $email = sanitize_email($_POST['isw_ml_email']);
        if ( ! is_email($email) ) {
            $redirect_url = add_query_arg('ml_error', '1', wp_get_referer());
            wp_safe_redirect($redirect_url);
            exit;
        }
        $isw_table = $wpdb->prefix . 'isw_ml';

        if($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $isw_table)) != $isw_table){
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
        wp_safe_redirect($redirect_url);
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

	$export_url = wp_nonce_url(plugins_url('export-handler.php', __FILE__), 'isw_ml_export_csv');

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
		<form method="post" action="options.php">
			<?php
			settings_fields('isw-ml-response-mail-settings-group');
			do_settings_sections('isw-ml-response-mail-settings');
			submit_button();
			?>
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
	register_setting('isw-ml-input-settings-group', 'input_outline_color');

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

	add_settings_field(
    'input_outline_color',
    'Input field outline color',
    'isw_ml_input_outline_color_callback',
    'isw-ml-input-settings',
    'isw-ml-settings-input-section'
);

	register_setting('isw-ml-button-settings-group', 'button_bg_color');
	register_setting('isw-ml-button-settings-group', 'button_text');
	register_setting('isw-ml-button-settings-group', 'button_text_color');
	register_setting('isw-ml-button-settings-group', 'button_font_family');
	register_setting('isw-ml-button-settings-group', 'button_font_size');
	register_setting('isw-ml-button-settings-group', 'button_font_style');
	register_setting('isw-ml-button-settings-group', 'button_line_height');
	register_setting('isw-ml-button-settings-group', 'button_border_width');
	register_setting('isw-ml-button-settings-group', 'button_border_color');
	register_setting('isw-ml-button-settings-group', 'button_border_style');
	register_setting('isw-ml-button-settings-group', 'button_box_shadow');
	register_setting('isw-ml-button-settings-group', 'button_font_weight');

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
	add_settings_field(
    'button_font_family',
    'Button Font Family',
    'isw_ml_btn_font_family_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_font_size',
    'Button Font Size (px)',
    'isw_ml_btn_font_size_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_font_style',
    'Button Font Style',
    'isw_ml_btn_font_style_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_font_weight',
    'Button Font Weight',
    'isw_ml_btn_font_weight_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_line_height',
    'Button Line Height',
    'isw_ml_btn_line_height_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_border_width',
    'Button Border Width (px)',
    'isw_ml_btn_border_width_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_border_color',
    'Button Border Color',
    'isw_ml_btn_border_color_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_border_style',
    'Button Border Style',
    'isw_ml_btn_border_style_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_box_shadow',
    'Button Box Shadow',
    'isw_ml_btn_box_shadow_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);

	register_setting('isw-ml-response-mail-settings-group', 'email_from');
	register_setting('isw-ml-response-mail-settings-group', 'email_subject');
	register_setting('isw-ml-response-mail-settings-group', 'email_template');

	add_settings_section(
		'isw-ml-settings-response-mail-section',
		'',
		'isw_ml_settings_response_mail_section_callback',
		'isw-ml-response-mail-settings'
	);

	add_settings_field(
		'response_mail_from',
		'Response Mail from address',
		'isw_ml_response_mail_from_callback',
		'isw-ml-response-mail-settings',
		'isw-ml-settings-response-mail-section'
	);

	add_settings_field(
		'response_mail_subject',
		'Response Mail subject',
		'isw_ml_response_mail_subject_callback',
		'isw-ml-response-mail-settings',
		'isw-ml-settings-response-mail-section'
	);

	add_settings_field(
		'response_mail_template',
		'Response Mail template',
		'isw_ml_response_mail_template_callback',
		'isw-ml-response-mail-settings',
		'isw-ml-settings-response-mail-section'
	);

	register_setting('isw-ml-input-settings-group', 'input_name_placeholder');
	register_setting('isw-ml-input-settings-group', 'input_email_placeholder');
	register_setting('isw-ml-input-settings-group', 'ml_success_message');
	register_setting('isw-ml-input-settings-group', 'ml_error_message');

	add_settings_field(
		'input_name_placeholder',
		'Name field placeholder',
		'isw_ml_input_name_placeholder_callback',
		'isw-ml-input-settings',
		'isw-ml-settings-input-section'
	);
	add_settings_field(
		'input_email_placeholder',
		'Email field placeholder',
		'isw_ml_input_email_placeholder_callback',
		'isw-ml-input-settings',
		'isw-ml-settings-input-section'
	);
	add_settings_field(
		'ml_success_message',
		'Success message',
		'isw_ml_success_message_callback',
		'isw-ml-input-settings',
		'isw-ml-settings-input-section'
	);
	add_settings_field(
		'ml_error_message',
		'Error message',
		'isw_ml_error_message_callback',
		'isw-ml-input-settings',
		'isw-ml-settings-input-section'
	);
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

function isw_ml_input_outline_color_callback(){
    $input_outline_color = get_option('input_outline_color', '#2684FF');
    echo '<input type="text" id="input_outline_color" name="input_outline_color" value="' . esc_attr($input_outline_color) . '" class="color-field" />';
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
function isw_ml_btn_font_family_callback(){
    $button_font_family = get_option('button_font_family', 'inherit');
    $fonts = [
        'inherit' => 'Inherit (default)',
        'Arial, Helvetica, sans-serif' => 'Arial',
        'Verdana, Geneva, sans-serif' => 'Verdana',
        'Tahoma, Geneva, sans-serif' => 'Tahoma',
        'Trebuchet MS, Helvetica, sans-serif' => 'Trebuchet MS',
        'Times New Roman, Times, serif' => 'Times New Roman',
        'Georgia, serif' => 'Georgia',
        'Garamond, serif' => 'Garamond',
        'Courier New, Courier, monospace' => 'Courier New',
        'Brush Script MT, cursive' => 'Brush Script MT',
        'Lucida Sans Unicode, Lucida Grande, sans-serif' => 'Lucida Sans',
        'Impact, Charcoal, sans-serif' => 'Impact',
        'Palatino Linotype, Book Antiqua, Palatino, serif' => 'Palatino',
        'Comic Sans MS, cursive, sans-serif' => 'Comic Sans MS',
        'Franklin Gothic Medium, Arial Narrow, Arial, sans-serif' => 'Franklin Gothic Medium'
    ];
    echo '<select id="btn_font_family" name="button_font_family" style="width:100%;">';
    foreach($fonts as $value => $label){
        echo '<option value="' . esc_attr($value) . '" ' . selected($button_font_family, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}

function isw_ml_btn_font_size_callback(){
	$button_font_size = get_option('button_font_size', '16');
	echo '<input type="number" id="btn_font_size" name="button_font_size" value="' . esc_attr($button_font_size) . '" min="8" max="72" style="width:100px;" /> px';
}

function isw_ml_btn_font_style_callback(){
	$button_font_style = get_option('button_font_style', 'normal');
	echo '<select id="btn_font_style" name="button_font_style">
        <option value="normal" ' . selected($button_font_style, 'normal', false) . '>Normal</option>
        <option value="italic" ' . selected($button_font_style, 'italic', false) . '>Italic</option>
        <option value="oblique" ' . selected($button_font_style, 'oblique', false) . '>Oblique</option>
    </select>';
}

function isw_ml_btn_font_weight_callback(){
    $button_font_weight = get_option('button_font_weight', 'normal');
    $weights = [
        'normal' => 'Normal',
        'bold' => 'Bold',
        '100' => '100 (Thin)',
        '200' => '200 (Extra Light)',
        '300' => '300 (Light)',
        '400' => '400 (Normal)',
        '500' => '500 (Medium)',
        '600' => '600 (Semi Bold)',
        '700' => '700 (Bold)',
        '800' => '800 (Extra Bold)',
        '900' => '900 (Black)'
    ];
    echo '<select id="btn_font_weight" name="button_font_weight" style="width:100%;">';
    foreach($weights as $value => $label){
        echo '<option value="' . esc_attr($value) . '" ' . selected($button_font_weight, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}

function isw_ml_btn_line_height_callback(){
	$button_line_height = get_option('button_line_height', '1.2');
	echo '<input type="text" id="btn_line_height" name="button_line_height" value="' . esc_attr($button_line_height) . '" style="width:100px;" placeholder="npr: 1.2" />';
}

function isw_ml_btn_border_width_callback(){
	$button_border_width = get_option('button_border_width', '1');
	echo '<input type="number" id="btn_border_width" name="button_border_width" value="' . esc_attr($button_border_width) . '" min="0" max="10" style="width:100px;" /> px';
}

function isw_ml_btn_border_color_callback(){
	$button_border_color = get_option('button_border_color', '#001f53');
	echo '<input type="text" id="btn_border_color" name="button_border_color" value="' . esc_attr($button_border_color) . '" class="color-field" />';
}

function isw_ml_btn_border_style_callback(){
	$button_border_style = get_option('button_border_style', 'solid');
	echo '<select id="btn_border_style" name="button_border_style">
        <option value="solid" ' . selected($button_border_style, 'solid', false) . '>Solid</option>
        <option value="dashed" ' . selected($button_border_style, 'dashed', false) . '>Dashed</option>
        <option value="dotted" ' . selected($button_border_style, 'dotted', false) . '>Dotted</option>
        <option value="double" ' . selected($button_border_style, 'double', false) . '>Double</option>
        <option value="groove" ' . selected($button_border_style, 'groove', false) . '>Groove</option>
        <option value="ridge" ' . selected($button_border_style, 'ridge', false) . '>Ridge</option>
        <option value="inset" ' . selected($button_border_style, 'inset', false) . '>Inset</option>
        <option value="outset" ' . selected($button_border_style, 'outset', false) . '>Outset</option>
        <option value="none" ' . selected($button_border_style, 'none', false) . '>None</option>
    </select>';
}

function isw_ml_btn_box_shadow_callback(){
	$button_box_shadow = get_option('button_box_shadow', '0 2px 6px rgba(0,0,0,0.15)');
	echo '<input type="text" id="btn_box_shadow" name="button_box_shadow" value="' . esc_attr($button_box_shadow) . '" style="width:100%;" placeholder="npr: 0 2px 6px rgba(0,0,0,0.15)" />';
}

/* response mail */
function isw_ml_settings_response_mail_section_callback(){
	echo '<div style="border: 1px solid #404040; border-radius: 0.25rem; background-color: #808080; padding: 0.5rem 1rem;"><h3 style="margin: 0;color: #ffffff;">Edit Response Email template</h3></div>';
}

function isw_ml_response_mail_from_callback(){
	$email_from = get_option('email_from', 'noreply@domain.com');
	echo '<input type="email" id="email_from" name="email_from" value="' . sanitize_email($email_from) . '" style="width: 100%;" />';
}

function isw_ml_response_mail_subject_callback(){
	$email_subject = get_option('email_subject', 'Email Subject');
	echo '<input type="text" id="email_subject" name="email_subject" value="' . sanitize_text_field($email_subject) . '" style="width: 100%;" />';
}

function isw_ml_response_mail_template_callback(){
	$email_template = get_option('email_template', 'Dear {{name}}, thank you for your subscription!');
	echo '<textarea id="email_template" name="email_template" rows="5" style="width: 100%;">' . sanitize_textarea_field($email_template) .'</textarea>';
}

/* input placeholders */
function isw_ml_input_name_placeholder_callback(){
    $ph = get_option('input_name_placeholder', 'Your name...');
    echo '<input type="text" id="input_name_placeholder" name="input_name_placeholder" value="' . esc_attr($ph) . '" style="width:100%;" />';
}
function isw_ml_input_email_placeholder_callback(){
    $ph = get_option('input_email_placeholder', 'Your E-Mail address...');
    echo '<input type="text" id="input_email_placeholder" name="input_email_placeholder" value="' . esc_attr($ph) . '" style="width:100%;" />';
}
function isw_ml_success_message_callback(){
    $msg = get_option('ml_success_message', 'Your E-mail address was successfully submitted. Thank you!');
    echo '<input type="text" id="ml_success_message" name="ml_success_message" value="' . esc_attr($msg) . '" style="width:100%;" />';
}
function isw_ml_error_message_callback(){
    $msg = get_option('ml_error_message', 'There was an error with your submission. Please try again.');
    echo '<input type="text" id="ml_error_message" name="ml_error_message" value="' . esc_attr($msg) . '" style="width:100%;" />';
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
    $subject = get_option('email_subject', 'Thank you for your subscription!');
    $template = get_option('email_template', 'Dear {{name}}, thank you for your subscription!');
    
    $message = str_replace('{{name}}', $subscriber_name, $template);
    
    $from = get_option('email_from', 'noreply@domain.com');
    $headers = array(
        'From: ' . sanitize_text_field($from),
        'Content-Type: text/plain; charset=UTF-8'
    );
    
    wp_mail($to_email, $subject, $message, $headers);
}

add_action('wp_footer', function() use ($input_outline_color) {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var inputs = document.querySelectorAll('.isw-ml-form-container input[type="text"], .isw-ml-form-container input[type="email"]');
        inputs.forEach(function(input){
            input.addEventListener('focus', function(){
                this.style.outlineColor = '<?php echo esc_js($input_outline_color); ?>';
                this.style.outlineStyle = 'auto';
            });
            input.addEventListener('blur', function(){
                this.style.outlineColor = '';
                this.style.outlineStyle = '';
            });
        });
    });
    </script>
    <?php
});