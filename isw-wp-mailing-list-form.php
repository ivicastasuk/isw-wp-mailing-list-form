<?php
/**
 * Plugin Name: 	ISW WP Mailing List Form
 * Description: 	The ISW WP Mailing List Form plugin integrates a subscription form into your WordPress site, allowing visitors to enter their email address to subscribe to your newsletter.
 * Version: 		1.0.0
 * Author: 			Ivica Stasuk
 * Author URI: 		https://www.stasuk.in.rs
 * License: 		GPL v3 or later
 * License URI: 	https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: 	isw-wp-mailing-list-form
 * Domain Path: 	/languages
*/

function add_isw_mailinglist_form(){
    $ml_message = '';
    $ml_message .= '<div class="isw-ml-form-container">';
    // if(isset($_GET['ml_submitted']) && $_GET['ml_submitted'] == '1'){
    //     $ml_message .= '<div class="isw-ml-form-message">' . esc_html(get_option('ml_success_message', 'Your E-mail address was successfully submitted. Thank you!')) . '</div>'; 
    // }
    // if(isset($_GET['ml_error']) && $_GET['ml_error'] == '1'){
    //     $ml_message .= '<div class="isw-ml-form-message isw-ml-error">' . esc_html(get_option('ml_error_message', 'There was an error with your submission. Please try again.')) . '</div>'; 
    // }
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

    $button_width_type = get_option('button_width_type', 'full');
    $button_width_custom = get_option('button_width_custom', '');
    $button_align = get_option('button_align', 'center');

    switch ($button_width_type) {
        case 'full':
            $btn_width = '100%';
            break;
        case '1/2':
            $btn_width = '50%';
            break;
        case '1/3':
            $btn_width = '33.3333%';
            break;
        case '1/4':
            $btn_width = '25%';
            break;
        case 'custom':
            $btn_width = $button_width_custom !== '' ? $button_width_custom : 'auto';
            break;
        default:
            $btn_width = '100%';
    }

    switch ($button_align) {
        case 'left':
            $btn_align_css = 'margin-left:0;margin-right:auto;';
            break;
        case 'center':
            $btn_align_css = 'margin-left:auto;margin-right:auto;display:block;';
            break;
        case 'right':
            $btn_align_css = 'margin-left:auto;margin-right:0;';
            break;
        default:
            $btn_align_css = '';
    }

    $input_width_type = get_option('input_width_type', 'full');
    $input_width_custom = get_option('input_width_custom', '');
    $input_align = get_option('input_align', 'center');

    switch ($input_width_type) {
        case 'full':
            $inp_width = '100%';
            break;
        case '1/2':
            $inp_width = '50%';
            break;
        case '1/3':
            $inp_width = '33.3333%';
            break;
        case '1/4':
            $inp_width = '25%';
            break;
        case 'custom':
            $inp_width = $input_width_custom !== '' ? $input_width_custom : 'auto';
            break;
        default:
            $inp_width = '100%';
    }

    switch ($input_align) {
        case 'left':
            $inp_align_css = 'margin-left:0;margin-right:auto;';
            break;
        case 'center':
            $inp_align_css = 'margin-left:auto;margin-right:auto;display:block;';
            break;
        case 'right':
            $inp_align_css = 'margin-left:auto;margin-right:0;';
            break;
        default:
            $inp_align_css = '';
    }

    $input_padding_top = get_option('input_padding_top', 16);
    $input_padding_right = get_option('input_padding_right', 16);
    $input_padding_bottom = get_option('input_padding_bottom', 16);
    $input_padding_left = get_option('input_padding_left', 16);
    $input_padding_same_all = get_option('input_padding_same_all', 1);

    if ($input_padding_same_all) {
        $input_padding = "{$input_padding_top}px";
    } else {
        $input_padding = "{$input_padding_top}px {$input_padding_right}px {$input_padding_bottom}px {$input_padding_left}px";
    }

    $button_padding_top = get_option('button_padding_top', 16);
    $button_padding_right = get_option('button_padding_right', 16);
    $button_padding_bottom = get_option('button_padding_bottom', 16);
    $button_padding_left = get_option('button_padding_left', 16);
    $button_padding_same_all = get_option('button_padding_same_all', 1);

    if ($button_padding_same_all) {
        $button_padding = "{$button_padding_top}px";
    } else {
        $button_padding = "{$button_padding_top}px {$button_padding_right}px {$button_padding_bottom}px {$button_padding_left}px";
    }

    $input_border_radius = get_option('input_border_radius', 16);
    $button_border_radius = get_option('button_border_radius', 16);

    $isw_ml_form = $ml_message . '<form id="isw-ml-form" action="" method="post">
    <input type="text" name="isw_ml_name" placeholder="' . esc_attr($name_placeholder) . '" required style="color:' . esc_attr($input_text_color) . '; border-color:' . esc_attr($input_border_color) . ';width:' . esc_attr($inp_width) . '; padding:' . esc_attr($input_padding) . '; border-radius: ' . esc_attr($input_border_radius) . 'px;' . $inp_align_css . '" onfocus="this.style.outlineColor=\'' . esc_attr($input_outline_color) . '\';">
    <input type="email" name="isw_ml_email" placeholder="' . esc_attr($email_placeholder) . '" required style="color:' . esc_attr($input_text_color) . '; border-color:' . esc_attr($input_border_color) . ';width:' . esc_attr($inp_width) . '; padding:' . esc_attr($input_padding) . '; border-radius: ' . esc_attr($input_border_radius) . 'px;' . $inp_align_css . '" onfocus="this.style.outlineColor=\'' . esc_attr($input_outline_color) . '\';">
    <input type="hidden" name="isw_ml_submit" value="1" />
    ' . wp_nonce_field('isw_ml_form_action', 'isw_ml_form_nonce', true, false) . '
    <input type="submit" name="isw_ml_submit_btn" value="' . esc_attr($button_text) . '" style="
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
        border-radius:' . esc_attr($button_border_radius) . 'px;
        box-shadow:' . esc_attr($button_box_shadow) . ';
        min-width:' . esc_attr($btn_width) . ';
        padding:' . esc_attr($button_padding) . ';
        ' . $btn_align_css . '
        ">
        </form>';
    $isw_ml_form .= '<div id="isw-ml-form-message"></div></div>';

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
    // Ako je AJAX zahtev, ne radi ništa!
    if ( defined('DOING_AJAX') && DOING_AJAX ) {
        return;
    }
    if ( isset( $_POST['isw_ml_submit'] ) ) {
        // Nonce provera i sanitizacija
        $nonce = isset( $_POST['isw_ml_form_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['isw_ml_form_nonce'] ) ) : '';
        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'isw_ml_form_action' ) ) {
            $redirect_url = add_query_arg( 'ml_error', '1', wp_get_referer() );
            wp_safe_redirect( $redirect_url );
            exit;
        }
        global $wpdb;

        // Provera i unslash/sanitize inputa
        $name  = isset( $_POST['isw_ml_name'] ) ? sanitize_text_field( wp_unslash( $_POST['isw_ml_name'] ) ) : '';
        $email = isset( $_POST['isw_ml_email'] ) ? sanitize_email( wp_unslash( $_POST['isw_ml_email'] ) ) : '';

        if ( ! is_email( $email ) ) {
            $redirect_url = add_query_arg( 'ml_error', '1', wp_get_referer() );
            wp_safe_redirect( $redirect_url );
            exit;
        }
        $isw_table = $wpdb->prefix . 'isw_ml';

        // Provera da li tabela postoji - koristi wp_cache_get za keširanje rezultata
        $cache_key = 'isw_ml_table_exists_' . $isw_table;
        $table_exists = wp_cache_get( $cache_key );
        if ( false === $table_exists ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $isw_table ) ) === $isw_table;
            wp_cache_set( $cache_key, $table_exists, '', 3600 );
        }

        // Priprema SQL upita za kreiranje tabele (dozvoljeno je koristiti dbDelta)
        if ( ! $table_exists ) {
            $sql =  "CREATE TABLE $isw_table (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    is_new int(11) NOT NULL,
                    UNIQUE KEY id (id)
                    );";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
            wp_cache_set( $cache_key, true, '', 3600 );
        }

        // Upis podataka (insert je dozvoljen)
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->insert( $isw_table, array( 'name' => $name, 'email' => $email, 'is_new' => 1 ) );

        isw_send_thankyou_email( $email, $name );

        // $redirect_url = add_query_arg( 'ml_submitted', '1', wp_get_referer() );
        // wp_safe_redirect( $redirect_url );
        exit;
    }
}
add_action( 'init', 'save_ml_form_to_db' );

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

function isw_ml_form_admin_page_dashboard() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'You don\'t have access to this page.' );
    }

    // Brisanje unosa po ID-u
    if (
        isset($_GET['isw_ml_delete']) &&
        isset($_GET['_wpnonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'isw_ml_delete_' . absint($_GET['isw_ml_delete']))
    ) {
        global $wpdb;
        $isw_table = $wpdb->prefix . 'isw_ml';
        $delete_id = absint($_GET['isw_ml_delete']);
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->delete($isw_table, array('id' => $delete_id), array('%d'));
        // Očisti keš
        wp_cache_delete('isw_ml_all_entries_' . $isw_table);
        wp_cache_delete('isw_ml_new_entries_count_' . $isw_table);
        // Redirektuj bez parametara
        $redirect_url = admin_url('admin.php?page=isw-ml-form-dashboard');
        wp_safe_redirect( $redirect_url );
        exit;
    }

    // Dodajte nonce proveru za export_emails ako je potrebno
    if ( isset( $_POST['export_emails'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'isw_ml_export_emails' ) ) {
        isw_ml_form_export_csv();
    }

    global $wpdb;
    $isw_table = $wpdb->prefix . 'isw_ml';

    // Keširanje rezultata tabele
    $cache_key = 'isw_ml_all_entries_' . $isw_table;
    $data = wp_cache_get( $cache_key );
    if ( false === $data ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $data = $wpdb->get_results( "SELECT * FROM $isw_table" );
        wp_cache_set( $cache_key, $data, '', 300 );
    }

    echo '<div class="wrap"><h1>Subscribed emails</h1>';
    echo '<div class="notice"><h3>How to...</h3><p>Add <code>[add_isw_ml_form]</code> shortcode where you want to add your mailing list form.</p></div>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Name</th><th>Email</th><th>Delete</th></tr></thead>';
    echo '<tbody>';

    foreach($data as $item){
        $delete_url = wp_nonce_url(
            add_query_arg(
                array(
                    'isw_ml_delete' => $item->id
                )
            ),
            'isw_ml_delete_' . $item->id
        );
        echo '<tr>
            <td>' . esc_html($item->name) . '</td>
            <td>' . esc_html($item->email) . '</td>
            <td><a href="' . esc_url($delete_url) . '" onclick="return confirm(\'Are you sure you want to delete this entry?\')" style="color:red;font-weight:bold;text-decoration:none;">&#10006;</a></td>
        </tr>';
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

    // Prikaz notifikacije o uspešnom snimanju
    $settings_updated = isset($_GET['settings-updated']) ? sanitize_text_field( wp_unslash($_GET['settings-updated']) ) : '';
    if ($settings_updated) {
        add_settings_error('isw_ml_messages', 'isw_ml_message', 'Settings saved successfully.', 'updated');
    }
    settings_errors('isw_ml_messages');
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
    wp_enqueue_script('isw-ml-admin-script', plugins_url('isw-wp-mailing-list-form.js', __FILE__), array('wp-color-picker'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'isw_ml_admin_scripts');

function isw_ml_settings_init(){

	// Provera i sanitizacija $_POST['button_text']
    if ( isset( $_POST['button_text'] ) ) {
        // $_POST['button_text'] = sanitize_text_field( wp_unslash( $_POST['button_text'] ) );
        if ( trim( sanitize_text_field( wp_unslash($_POST['button_text'] )) ) == '' ) {
            $_POST['button_text'] = 'Subscribe to our mailing list';
        }
    }

	register_setting('isw-ml-input-settings-group', 'input_text_color', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-input-settings-group', 'input_border_color', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-input-settings-group', 'input_outline_color', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-input-settings-group', 'input_width_type', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-input-settings-group', 'input_width_custom', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-input-settings-group', 'input_align', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-input-settings-group', 'input_padding_top', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-input-settings-group', 'input_padding_right', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-input-settings-group', 'input_padding_bottom', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-input-settings-group', 'input_padding_left', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-input-settings-group', 'input_padding_same_all', ['sanitize_callback' => 'isw_ml_sanitize_checkbox']);
	register_setting('isw-ml-input-settings-group', 'input_border_radius', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-input-settings-group', 'input_name_placeholder', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-input-settings-group', 'input_email_placeholder', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-input-settings-group', 'ml_success_message', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-input-settings-group', 'ml_error_message', ['sanitize_callback' => 'sanitize_text_field']);

	register_setting('isw-ml-button-settings-group', 'button_bg_color', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_text', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_text_color', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_font_family', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_font_size', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-button-settings-group', 'button_font_style', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_line_height', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_border_width', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-button-settings-group', 'button_border_color', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_border_style', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_box_shadow', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_font_weight', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_width_type', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_width_custom', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_align', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-button-settings-group', 'button_padding_top', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-button-settings-group', 'button_padding_right', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-button-settings-group', 'button_padding_bottom', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-button-settings-group', 'button_padding_left', ['sanitize_callback' => 'absint']);
	register_setting('isw-ml-button-settings-group', 'button_padding_same_all', ['sanitize_callback' => 'isw_ml_sanitize_checkbox']);
	register_setting('isw-ml-button-settings-group', 'button_border_radius', ['sanitize_callback' => 'absint']);

	register_setting('isw-ml-response-mail-settings-group', 'email_from', ['sanitize_callback' => 'sanitize_email']);
	register_setting('isw-ml-response-mail-settings-group', 'email_subject', ['sanitize_callback' => 'sanitize_text_field']);
	register_setting('isw-ml-response-mail-settings-group', 'email_template', ['sanitize_callback' => 'sanitize_textarea_field']);

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

	add_settings_field(
    'input_width_type',
    'Input Width',
    'isw_ml_input_width_type_callback',
    'isw-ml-input-settings',
    'isw-ml-settings-input-section'
);
add_settings_field(
    'input_width_custom',
    'Custom Input Width (px or %)',
    'isw_ml_input_width_custom_callback',
    'isw-ml-input-settings',
    'isw-ml-settings-input-section'
);
add_settings_field(
    'input_align',
    'Input Alignment',
    'isw_ml_input_align_callback',
    'isw-ml-input-settings',
    'isw-ml-settings-input-section'
);
add_settings_field(
    'input_padding_fields',
    'Input Padding (px)',
    'isw_ml_input_padding_callback',
    'isw-ml-input-settings',
    'isw-ml-settings-input-section'
);
add_settings_field(
    'input_border_radius',
    'Input Border Radius (px)',
    'isw_ml_input_border_radius_callback',
    'isw-ml-input-settings',
    'isw-ml-settings-input-section'
);

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
add_settings_field(
    'button_width_type',
    'Button Width',
    'isw_ml_btn_width_type_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_width_custom',
    'Custom Button Width (px or %)',
    'isw_ml_btn_width_custom_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_align',
    'Button Alignment',
    'isw_ml_btn_align_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_padding_fields',
    'Button Padding (px)',
    'isw_ml_button_padding_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);
add_settings_field(
    'button_border_radius',
    'Button Border Radius (px)',
    'isw_ml_button_border_radius_callback',
    'isw-ml-button-settings',
    'isw-ml-settings-button-section'
);

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
add_action( 'admin_init', 'isw_ml_settings_init' );

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
	echo '<input type="text" id="btn_text" name="button_text" value="' . esc_attr($button_text) . '" style="width: 100%;" />';
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

function isw_ml_btn_box_shadow_callback() {
    // Podrazumevane vrednosti
    $box_shadow = get_option('button_box_shadow', '0 2px 6px 0 rgba(0,0,0,0.15)');
    $inset = '';
    $h_offset = 0;
    $v_offset = 2;
    $blur = 6;
    $spread = 0;
    $color = 'rgba(0,0,0,0.15)';

    // Parsiranje vrednosti iz baze
    if (preg_match('/^(inset\s+)?(-?\d+)px\s+(-?\d+)px\s+(\d+)px\s+(-?\d+)px\s+(.+)$/', $box_shadow, $matches)) {
        $inset = trim($matches[1]) === 'inset' ? 'inset' : '';
        $h_offset = intval($matches[2]);
        $v_offset = intval($matches[3]);
        $blur = intval($matches[4]);
        $spread = intval($matches[5]);
        $color = trim($matches[6]);
    } elseif (preg_match('/^(inset\s+)?(-?\d+)px\s+(-?\d+)px\s+(\d+)px\s+(.+)$/', $box_shadow, $matches)) {
        // fallback za 4 vrednosti (bez spread)
        $inset = trim($matches[1]) === 'inset' ? 'inset' : '';
        $h_offset = intval($matches[2]);
        $v_offset = intval($matches[3]);
        $blur = intval($matches[4]);
        $spread = 0;
        $color = trim($matches[5]);
    }

    // Očisti boju od px viška na početku (ako je greškom ostalo)
    $color = preg_replace('/^(-?\d+px\s*)+/', '', $color);
    ?>
    <div id="isw-box-shadow-controls">
        <label>H-Offset: <input type="range" min="-50" max="50" id="isw_h_offset" value="<?php echo esc_attr($h_offset); ?>"> <input type="number" min="-50" max="50" id="isw_h_offset_num" value="<?php echo esc_attr($h_offset); ?>"> px</label><br>
        <label>V-Offset: <input type="range" min="-50" max="50" id="isw_v_offset" value="<?php echo esc_attr($v_offset); ?>"> <input type="number" min="-50" max="50" id="isw_v_offset_num" value="<?php echo esc_attr($v_offset); ?>"> px</label><br>
        <label>Blur: <input type="range" min="0" max="100" id="isw_blur" value="<?php echo esc_attr($blur); ?>"> <input type="number" min="0" max="100" id="isw_blur_num" value="<?php echo esc_attr($blur); ?>"> px</label><br>
        <label>Spread: <input type="range" min="-50" max="50" id="isw_spread" value="<?php echo esc_attr($spread); ?>"> <input type="number" min="-50" max="50" id="isw_spread_num" value="<?php echo esc_attr($spread); ?>"> px</label><br>
        <label>Color: <input type="text" id="isw_box_shadow_color" value="<?php echo esc_attr($color); ?>" class="color-field" /></label><br>
        <label><input type="checkbox" id="isw_box_shadow_inset" <?php checked($inset, 'inset'); ?>> Inset</label>
        <input type="hidden" id="btn_box_shadow" name="button_box_shadow" value="<?php echo esc_attr($box_shadow); ?>" />
        <div style="margin-top:10px;">
            <span>Preview:</span>
            <div id="isw_box_shadow_preview" style="display:inline-block;width:60px;height:30px;background:#fff;border:1px solid #ccc;vertical-align:middle;"></div>
        </div>
    </div>
    <?php
}

/* response mail */
function isw_ml_settings_response_mail_section_callback(){
	echo '<div style="border: 1px solid #404040; border-radius: 0.25rem; background-color: #808080; padding: 0.5rem 1rem;"><h3 style="margin: 0;color: #ffffff;">Edit Response Email template</h3></div>';
}

function isw_ml_response_mail_from_callback(){
    $email_from = get_option('email_from', 'noreply@domain.com');
    echo '<input type="email" id="email_from" name="email_from" value="' . esc_attr($email_from) . '" style="width: 100%;" />';
}

function isw_ml_response_mail_subject_callback(){
	$email_subject = get_option('email_subject', 'Email Subject');
	echo '<input type="text" id="email_subject" name="email_subject" value="' . esc_attr($email_subject) . '" style="width: 100%;" />';
}

function isw_ml_response_mail_template_callback(){
	$email_template = get_option('email_template', 'Dear {{name}}, thank you for your subscription!');
	echo '<textarea id="email_template" name="email_template" rows="5" style="width: 100%;">' . esc_textarea($email_template) .'</textarea>';
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
function isw_get_new_entries_count() {
    global $wpdb;
    $isw_table = $wpdb->prefix . 'isw_ml';

    // Keširanje broja novih unosa
    $cache_key = 'isw_ml_new_entries_count_' . $isw_table;
    $new_entries_count = wp_cache_get( $cache_key );
    if ( false === $new_entries_count ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $new_entries_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $isw_table WHERE is_new = %d", 1 ) );
        wp_cache_set( $cache_key, $new_entries_count, '', 300 );
    }
    return $new_entries_count;
}

function isw_reset_new_entries() {
    global $wpdb;
    $isw_table = $wpdb->prefix . 'isw_ml';

    // Resetuj is_new i očisti keš
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $wpdb->query( $wpdb->prepare( "UPDATE $isw_table SET is_new = %d WHERE is_new = %d", 0, 1 ) );
    $cache_key = 'isw_ml_new_entries_count_' . $isw_table;
    wp_cache_delete( $cache_key );
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

add_action('wp_footer', function() {
    $input_outline_color = get_option('input_outline_color', '#2684FF');
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var inputs = document.querySelectorAll('.isw-ml-form-container input[type="text"], .isw-ml-form-container input[type="email"]');
        inputs.forEach(function(input){
            input.addEventListener('focus', function(){
                this.style.outlineColor = '<?php echo esc_js($input_outline_color); ?>';
                this.style.outlineStyle = 'solid';
                this.style.outlineWidth = '2px';
            });
            input.addEventListener('blur', function(){
                this.style.outlineColor = '';
                this.style.outlineStyle = '';
                this.style.outlineWidth = '';
            });
        });
    });
    </script>
    <?php
});

function isw_ml_btn_width_type_callback() {
    $value = get_option('button_width_type', 'full');
    ?>
    <select id="btn_width_type" name="button_width_type">
        <option value="full" <?php selected($value, 'full'); ?>>Full width</option>
        <option value="1/2" <?php selected($value, '1/2'); ?>>1/2</option>
        <option value="1/3" <?php selected($value, '1/3'); ?>>1/3</option>
        <option value="1/4" <?php selected($value, '1/4'); ?>>1/4</option>
        <option value="custom" <?php selected($value, 'custom'); ?>>Custom</option>
    </select>
    <?php
}

function isw_ml_btn_width_custom_callback() {
    $value = get_option('button_width_custom', '');
    ?>
    <input type="text" id="btn_width_custom" name="button_width_custom" value="<?php echo esc_attr($value); ?>" placeholder="npr: 200px ili 50%" />
    <span style="color:#888;">(Unesite npr: 200px ili 50%)</span>
    <?php
}

function isw_ml_btn_align_callback() {
    $value = get_option('button_align', 'center');
    ?>
    <select id="btn_align" name="button_align">
        <option value="left" <?php selected($value, 'left'); ?>>Left</option>
        <option value="center" <?php selected($value, 'center'); ?>>Center</option>
        <option value="right" <?php selected($value, 'right'); ?>>Right</option>
    </select>
    <?php
}

function isw_ml_input_width_type_callback() {
    $value = get_option('input_width_type', 'full');
    ?>
    <select id="input_width_type" name="input_width_type">
        <option value="full" <?php selected($value, 'full'); ?>>Full width</option>
        <option value="1/2" <?php selected($value, '1/2'); ?>>1/2</option>
        <option value="1/3" <?php selected($value, '1/3'); ?>>1/3</option>
        <option value="1/4" <?php selected($value, '1/4'); ?>>1/4</option>
        <option value="custom" <?php selected($value, 'custom'); ?>>Custom</option>
    </select>
    <?php
}

function isw_ml_input_width_custom_callback() {
    $value = get_option('input_width_custom', '');
    ?>
    <input type="text" id="input_width_custom" name="input_width_custom" value="<?php echo esc_attr($value); ?>" placeholder="npr: 200px ili 50%" />
    <span style="color:#888;">(Unesite npr: 200px ili 50%)</span>
    <?php
}

function isw_ml_input_align_callback() {
    $value = get_option('input_align', 'center');
    ?>
    <select id="input_align" name="input_align">
        <option value="left" <?php selected($value, 'left'); ?>>Left</option>
        <option value="center" <?php selected($value, 'center'); ?>>Center</option>
        <option value="right" <?php selected($value, 'right'); ?>>Right</option>
    </select>
    <?php
}

function isw_ml_input_padding_callback() {
    $same = get_option('input_padding_same_all', 1);
    $top = get_option('input_padding_top', 16);
    $right = get_option('input_padding_right', 16);
    $bottom = get_option('input_padding_bottom', 16);
    $left = get_option('input_padding_left', 16);
    ?>
    <label>
        <input type="checkbox" id="input_padding_same_all" name="input_padding_same_all" value="1" <?php checked($same, 1); ?> />
        Isti padding za sva 4 pravca
    </label>
    <div id="input-padding-fields" style="margin-top:8px;">
        <label>Gore: <input type="number" id="input_padding_top" name="input_padding_top" value="<?php echo esc_attr($top); ?>" min="0" style="width:60px;" /> px</label>
        <label style="margin-left:10px;">Desno: <input type="number" id="input_padding_right" name="input_padding_right" value="<?php echo esc_attr($right); ?>" min="0" style="width:60px;" /> px</label>
        <label style="margin-left:10px;">Dole: <input type="number" id="input_padding_bottom" name="input_padding_bottom" value="<?php echo esc_attr($bottom); ?>" min="0" style="width:60px;" /> px</label>
        <label style="margin-left:10px;">Levo: <input type="number" id="input_padding_left" name="input_padding_left" value="<?php echo esc_attr($left); ?>" min="0" style="width:60px;" /> px</label>
    </div>
    <script>
    jQuery(function($){
        function syncPaddingFields() {
            if ($('#input_padding_same_all').is(':checked')) {
                var val = $('#input_padding_top').val();
                $('#input_padding_right, #input_padding_bottom, #input_padding_left').val(val).prop('readonly', true);
            } else {
                $('#input_padding_right, #input_padding_bottom, #input_padding_left').prop('readonly', false);
            }
        }
        $('#input_padding_same_all').on('change', syncPaddingFields);
        $('#input_padding_top').on('input', function(){
            if ($('#input_padding_same_all').is(':checked')) {
                $('#input_padding_right, #input_padding_bottom, #input_padding_left').val(this.value);
            }
        });
        syncPaddingFields();
    });
    </script>
    <?php
}

function isw_ml_button_padding_callback() {
    $same = get_option('button_padding_same_all', 1);
    $top = get_option('button_padding_top', 16);
    $right = get_option('button_padding_right', 16);
    $bottom = get_option('button_padding_bottom', 16);
    $left = get_option('button_padding_left', 16);
    ?>
    <label>
        <input type="checkbox" id="button_padding_same_all" name="button_padding_same_all" value="1" <?php checked($same, 1); ?> />
        Isti padding za sva 4 pravca
    </label>
    <div id="button-padding-fields" style="margin-top:8px;">
        <label>Gore: <input type="number" id="button_padding_top" name="button_padding_top" value="<?php echo esc_attr($top); ?>" min="0" style="width:60px;" /> px</label>
        <label style="margin-left:10px;">Desno: <input type="number" id="button_padding_right" name="button_padding_right" value="<?php echo esc_attr($right); ?>" min="0" style="width:60px;" /> px</label>
        <label style="margin-left:10px;">Dole: <input type="number" id="button_padding_bottom" name="button_padding_bottom" value="<?php echo esc_attr($bottom); ?>" min="0" style="width:60px;" /> px</label>
        <label style="margin-left:10px;">Levo: <input type="number" id="button_padding_left" name="button_padding_left" value="<?php echo esc_attr($left); ?>" min="0" style="width:60px;" /> px</label>
    </div>
    <script>
    jQuery(function($){
        function syncBtnPaddingFields() {
            if ($('#button_padding_same_all').is(':checked')) {
                var val = $('#button_padding_top').val();
                $('#button_padding_right, #button_padding_bottom, #button_padding_left').val(val).prop('readonly', true);
            } else {
                $('#button_padding_right, #button_padding_bottom, #button_padding_left').prop('readonly', false);
            }
        }
        $('#button_padding_same_all').on('change', syncBtnPaddingFields);
        $('#button_padding_top').on('input', function(){
            if ($('#button_padding_same_all').is(':checked')) {
                $('#button_padding_right, #button_padding_bottom, #button_padding_left').val(this.value);
            }
        });
        syncBtnPaddingFields();
    });
    </script>
    <?php
}

function isw_ml_input_border_radius_callback(){
    $value = get_option('input_border_radius', 16);
    echo '<input type="number" id="input_border_radius" name="input_border_radius" value="' . esc_attr($value) . '" min="0" style="width:80px;" /> px';
}

function isw_ml_button_border_radius_callback(){
    $value = get_option('button_border_radius', 16);
    echo '<input type="number" id="button_border_radius" name="button_border_radius" value="' . esc_attr($value) . '" min="0" style="width:80px;" /> px';
}

function isw_ml_sanitize_checkbox($value) {
    return $value ? 1 : 0;
}

function isw_ml_enqueue_frontend_js() {
    if ( ! is_admin() ) {
        wp_enqueue_script(
            'isw-ml-frontend',
            plugins_url('isw-wp-mailing-list-form-frontend.js', __FILE__),
            array('jquery'),
            '1.0.0',
            true
        );
        wp_localize_script('isw-ml-frontend', 'isw_ml_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'success_msg' => esc_html(get_option('ml_success_message', 'Your E-mail address was successfully submitted. Thank you!')),
            'error_msg' => esc_html(get_option('ml_error_message', 'There was an error with your submission. Please try again.'))
        ));
    }
}
add_action('wp_enqueue_scripts', 'isw_ml_enqueue_frontend_js');


function isw_ml_ajax_submit() {
    if ( isset( $_POST['isw_ml_submit'] ) ) {
        $nonce = isset( $_POST['isw_ml_form_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['isw_ml_form_nonce'] ) ) : '';
        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'isw_ml_form_action' ) ) {
            wp_send_json_error(['reason' => 'nonce']);
        }
        $name  = isset( $_POST['isw_ml_name'] ) ? sanitize_text_field( wp_unslash( $_POST['isw_ml_name'] ) ) : '';
        $email = isset( $_POST['isw_ml_email'] ) ? sanitize_email( wp_unslash( $_POST['isw_ml_email'] ) ) : '';
        if ( ! is_email( $email ) ) {
            wp_send_json_error(['reason' => 'email']);
        }
        global $wpdb;
        $isw_table = $wpdb->prefix . 'isw_ml';
        $cache_key = 'isw_ml_table_exists_' . $isw_table;
        $table_exists = wp_cache_get( $cache_key );
        if ( false === $table_exists ) {
            $table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $isw_table ) ) === $isw_table;
            wp_cache_set( $cache_key, $table_exists, '', 3600 );
        }
        if ( ! $table_exists ) {
            $sql =  "CREATE TABLE $isw_table (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    is_new int(11) NOT NULL,
                    UNIQUE KEY id (id)
                    );";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
            wp_cache_set( $cache_key, true, '', 3600 );
        }
        $result = $wpdb->insert( $isw_table, array( 'name' => $name, 'email' => $email, 'is_new' => 1 ) );
        if ( ! $result ) {
            wp_send_json_error(['reason' => 'db']);
        }
        isw_send_thankyou_email( $email, $name );
        wp_send_json_success();
        wp_die();
    }
    wp_send_json_error(['reason' => 'other']);
    wp_die();
}
add_action('wp_ajax_isw_ml_submit', 'isw_ml_ajax_submit');
add_action('wp_ajax_nopriv_isw_ml_submit', 'isw_ml_ajax_submit');
