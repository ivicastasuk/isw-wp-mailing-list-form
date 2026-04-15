<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function isw_ml_form_menu() {
    $new_entries_count = isw_get_new_entries_count();
    $menu_title = 'ISW ML' . ( $new_entries_count > 0 ? " <span class='update-plugins count-$new_entries_count'><span class='plugin-count'>" . intval( $new_entries_count ) . '</span></span>' : '' );

    $dashboard_hook = add_menu_page(
        __( 'ISW Mailing List', 'isw-wp-mailing-list-form' ),
        $menu_title,
        'manage_options',
        'isw-ml-form-dashboard',
        'isw_ml_form_admin_page_dashboard',
        'dashicons-email-alt',
        15
    );

    add_submenu_page(
        'isw-ml-form-dashboard',
        __( 'Dashboard', 'isw-wp-mailing-list-form' ),
        __( 'Dashboard', 'isw-wp-mailing-list-form' ),
        'manage_options',
        'isw-ml-form-dashboard',
        'isw_ml_form_admin_page_dashboard'
    );

    add_submenu_page(
        'isw-ml-form-dashboard',
        __( 'Customization', 'isw-wp-mailing-list-form' ),
        __( 'Customization', 'isw-wp-mailing-list-form' ),
        'manage_options',
        'isw-ml-form-customization',
        'isw_ml_form_admin_page_customization'
    );

    add_action( 'load-' . $dashboard_hook, 'isw_reset_new_entries' );
}

add_action( 'admin_menu', 'isw_ml_form_menu' );

function isw_ml_is_plugin_admin_page() {
    $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

    return in_array( $page, array( 'isw-ml-form-dashboard', 'isw-ml-form-customization' ), true );
}

function isw_ml_admin_render_page_header( $title, $description, $actions = '' ) {
    echo '<div class="isw-ml-admin__hero">';
    echo '<div class="isw-ml-admin__hero-copy">';
    echo '<h1 class="isw-ml-admin__title">' . esc_html( $title ) . '</h1>';
    echo '<p class="isw-ml-admin__description">' . esc_html( $description ) . '</p>';
    echo '</div>';

    if ( $actions ) {
        echo '<div class="isw-ml-admin__hero-actions">' . $actions . '</div>';
    }

    echo '</div>';
}

function isw_ml_admin_render_stat_card( $label, $value, $description = '' ) {
    echo '<div class="isw-ml-admin__stat">';
    echo '<span class="isw-ml-admin__stat-label">' . esc_html( $label ) . '</span>';
    echo '<strong class="isw-ml-admin__stat-value">' . esc_html( $value ) . '</strong>';

    if ( $description ) {
        echo '<span class="isw-ml-admin__stat-description">' . esc_html( $description ) . '</span>';
    }

    echo '</div>';
}

function isw_ml_form_admin_page_dashboard() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have access to this page.', 'isw-wp-mailing-list-form' ) );
    }

    if (
        isset( $_GET['isw_ml_delete'] ) &&
        isset( $_GET['_wpnonce'] ) &&
        wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'isw_ml_delete_' . absint( $_GET['isw_ml_delete'] ) )
    ) {
        global $wpdb;

        $isw_table = isw_ml_get_table_name();
        $delete_id = absint( $_GET['isw_ml_delete'] );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->delete( $isw_table, array( 'id' => $delete_id ), array( '%d' ) );
        wp_cache_delete( 'isw_ml_all_entries_' . $isw_table );
        wp_cache_delete( 'isw_ml_new_entries_count_' . $isw_table );
        wp_safe_redirect( admin_url( 'admin.php?page=isw-ml-form-dashboard' ) );
        exit;
    }

    global $wpdb;
    $isw_table = isw_ml_get_table_name();

    if ( ! isw_ml_ensure_table() ) {
        echo '<div class="notice notice-error"><p>' . esc_html__( 'Unable to access the subscribers table.', 'isw-wp-mailing-list-form' ) . '</p></div>';
        return;
    }

    $cache_key = 'isw_ml_all_entries_' . $isw_table;
    $data = wp_cache_get( $cache_key );
    if ( false === $data ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $data = $wpdb->get_results( "SELECT * FROM $isw_table" );
        wp_cache_set( $cache_key, $data, '', 300 );
    }

    $total_subscribers = is_array( $data ) ? count( $data ) : 0;
    $export_url = wp_nonce_url( plugins_url( 'export-handler.php', ISW_ML_PLUGIN_FILE ), 'isw_ml_export_csv' );
    $hero_actions = '';

    if ( $total_subscribers > 0 ) {
        $hero_actions = '<a href="' . esc_url( $export_url ) . '" class="button button-secondary">' . esc_html__( 'Export CSV', 'isw-wp-mailing-list-form' ) . '</a>';
    }

    echo '<div class="wrap isw-ml-admin">';
    isw_ml_admin_render_page_header(
        __( 'Mailing List Dashboard', 'isw-wp-mailing-list-form' ),
        __( 'Manage subscribers, export your list, and quickly grab the shortcode for the frontend form.', 'isw-wp-mailing-list-form' ),
        $hero_actions
    );

    echo '<div class="isw-ml-admin__stats">';
    isw_ml_admin_render_stat_card(
        __( 'Subscribers', 'isw-wp-mailing-list-form' ),
        (string) $total_subscribers,
        __( 'Total saved contacts in the mailing list table.', 'isw-wp-mailing-list-form' )
    );
    isw_ml_admin_render_stat_card(
        __( 'Shortcode', 'isw-wp-mailing-list-form' ),
        '[add_isw_ml_form]',
        __( 'Paste this into a page, post, or widget area.', 'isw-wp-mailing-list-form' )
    );
    isw_ml_admin_render_stat_card(
        __( 'Status', 'isw-wp-mailing-list-form' ),
        $total_subscribers > 0 ? __( 'Collecting', 'isw-wp-mailing-list-form' ) : __( 'Ready', 'isw-wp-mailing-list-form' ),
        __( 'The plugin is active and ready to capture new submissions.', 'isw-wp-mailing-list-form' )
    );
    echo '</div>';

    echo '<div class="isw-ml-admin__grid">';
    echo '<section class="isw-ml-card isw-ml-card--table">';
    echo '<div class="isw-ml-card__header">';
    echo '<div>';
    echo '<h2>' . esc_html__( 'Subscribers', 'isw-wp-mailing-list-form' ) . '</h2>';
    echo '<p>' . esc_html__( 'Review the collected list and remove records you no longer need.', 'isw-wp-mailing-list-form' ) . '</p>';
    echo '</div>';
    if ( $total_subscribers > 0 ) {
        echo '<span class="isw-ml-card__badge">' . esc_html( sprintf( _n( '%d entry', '%d entries', $total_subscribers, 'isw-wp-mailing-list-form' ), $total_subscribers ) ) . '</span>';
    }
    echo '</div>';

    if ( 0 === $total_subscribers ) {
        echo '<div class="isw-ml-empty-state">';
        echo '<p class="isw-ml-empty-state__title">' . esc_html__( 'No subscribers yet', 'isw-wp-mailing-list-form' ) . '</p>';
        echo '<p>' . esc_html__( 'Once visitors submit the form, their name and email address will appear here.', 'isw-wp-mailing-list-form' ) . '</p>';
        echo '</div>';
    } else {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>' . esc_html__( 'Name', 'isw-wp-mailing-list-form' ) . '</th><th>' . esc_html__( 'Email', 'isw-wp-mailing-list-form' ) . '</th><th class="isw-ml-admin__actions-column">' . esc_html__( 'Actions', 'isw-wp-mailing-list-form' ) . '</th></tr></thead>';
        echo '<tbody>';

        foreach ( $data as $item ) {
            $delete_url = wp_nonce_url(
                add_query_arg(
                    array(
                        'isw_ml_delete' => $item->id,
                    )
                ),
                'isw_ml_delete_' . $item->id
            );
            echo '<tr>';
            echo '<td><strong>' . esc_html( $item->name ) . '</strong></td>';
            echo '<td><a href="mailto:' . esc_attr( $item->email ) . '">' . esc_html( $item->email ) . '</a></td>';
            echo '<td class="isw-ml-admin__actions-column"><a href="' . esc_url( $delete_url ) . '" class="button-link-delete isw-ml-admin__delete-link" onclick="return confirm(\'' . esc_js( __( 'Are you sure you want to delete this entry?', 'isw-wp-mailing-list-form' ) ) . '\')">' . esc_html__( 'Delete', 'isw-wp-mailing-list-form' ) . '</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }
    echo '</section>';

    echo '<aside class="isw-ml-card isw-ml-card--sidebar">';
    echo '<div class="isw-ml-card__header"><div><h2>' . esc_html__( 'Quick Start', 'isw-wp-mailing-list-form' ) . '</h2><p>' . esc_html__( 'Everything you need to place the form and continue configuring it.', 'isw-wp-mailing-list-form' ) . '</p></div></div>';
    echo '<div class="isw-ml-admin__shortcode-box">[add_isw_ml_form]</div>';
    echo '<p class="description">' . esc_html__( 'Use the shortcode in posts, pages, or widget areas. Then head to Customization to adjust fields, button styles, and response email content.', 'isw-wp-mailing-list-form' ) . '</p>';
    echo '<div class="isw-ml-admin__sidebar-actions">';
    echo '<a href="' . esc_url( admin_url( 'admin.php?page=isw-ml-form-customization' ) ) . '" class="button button-primary">' . esc_html__( 'Open Customization', 'isw-wp-mailing-list-form' ) . '</a>';
    if ( $total_subscribers > 0 ) {
        echo '<a href="' . esc_url( $export_url ) . '" class="button button-secondary">' . esc_html__( 'Download CSV', 'isw-wp-mailing-list-form' ) . '</a>';
    }
    echo '</div>';
    echo '</aside>';
    echo '</div>';
    echo '</div>';
}

function isw_ml_form_admin_page_customization() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have access to this page.', 'isw-wp-mailing-list-form' ) );
    }

    $settings_updated = isset( $_GET['settings-updated'] ) ? sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) : '';
    if ( $settings_updated ) {
        add_settings_error( 'isw_ml_messages', 'isw_ml_message', __( 'Settings saved successfully.', 'isw-wp-mailing-list-form' ), 'updated' );
    }

    settings_errors( 'isw_ml_messages' );
    ?>
    <div class="wrap isw-ml-admin isw-ml-admin--settings">
        <?php
        isw_ml_admin_render_page_header(
            __( 'Form Customization', 'isw-wp-mailing-list-form' ),
            __( 'Adjust field styles, button presentation, and the response email so the plugin blends into your site while still feeling native in WordPress admin.', 'isw-wp-mailing-list-form' )
        );
        ?>

        <nav class="nav-tab-wrapper isw-ml-admin__tabs" aria-label="<?php echo esc_attr__( 'Customization sections', 'isw-wp-mailing-list-form' ); ?>" role="tablist">
            <button type="button" class="nav-tab nav-tab-active" id="isw-ml-tab-inputs" data-panel="isw-ml-section-inputs" role="tab" aria-selected="true" aria-controls="isw-ml-section-inputs"><?php echo esc_html__( 'Input Fields', 'isw-wp-mailing-list-form' ); ?></button>
            <button type="button" class="nav-tab" id="isw-ml-tab-button" data-panel="isw-ml-section-button" role="tab" aria-selected="false" aria-controls="isw-ml-section-button"><?php echo esc_html__( 'Button', 'isw-wp-mailing-list-form' ); ?></button>
            <button type="button" class="nav-tab" id="isw-ml-tab-email" data-panel="isw-ml-section-email" role="tab" aria-selected="false" aria-controls="isw-ml-section-email"><?php echo esc_html__( 'Response Email', 'isw-wp-mailing-list-form' ); ?></button>
        </nav>

        <div class="isw-ml-settings-layout">
            <div class="isw-ml-settings-layout__main">
                <section id="isw-ml-section-inputs" class="postbox isw-ml-postbox isw-ml-tab-panel isw-ml-tab-panel-active" role="tabpanel" aria-labelledby="isw-ml-tab-inputs">
                    <div class="postbox-header"><h2 class="hndle"><?php echo esc_html__( 'Input Fields', 'isw-wp-mailing-list-form' ); ?></h2></div>
                    <div class="inside">
                        <form method="post" action="options.php" class="isw-ml-settings-form">
                            <?php
                            settings_fields( 'isw-ml-input-settings-group' );
                            do_settings_sections( 'isw-ml-input-settings' );
                            submit_button( __( 'Save Input Settings', 'isw-wp-mailing-list-form' ) );
                            ?>
                        </form>
                    </div>
                </section>

                <section id="isw-ml-section-button" class="postbox isw-ml-postbox isw-ml-tab-panel" role="tabpanel" aria-labelledby="isw-ml-tab-button" hidden>
                    <div class="postbox-header"><h2 class="hndle"><?php echo esc_html__( 'Button', 'isw-wp-mailing-list-form' ); ?></h2></div>
                    <div class="inside">
                        <form method="post" action="options.php" class="isw-ml-settings-form">
                            <?php
                            settings_fields( 'isw-ml-button-settings-group' );
                            do_settings_sections( 'isw-ml-button-settings' );
                            submit_button( __( 'Save Button Settings', 'isw-wp-mailing-list-form' ) );
                            ?>
                        </form>
                    </div>
                </section>

                <section id="isw-ml-section-email" class="postbox isw-ml-postbox isw-ml-tab-panel" role="tabpanel" aria-labelledby="isw-ml-tab-email" hidden>
                    <div class="postbox-header"><h2 class="hndle"><?php echo esc_html__( 'Response Email', 'isw-wp-mailing-list-form' ); ?></h2></div>
                    <div class="inside">
                        <form method="post" action="options.php" class="isw-ml-settings-form">
                            <?php
                            settings_fields( 'isw-ml-response-mail-settings-group' );
                            do_settings_sections( 'isw-ml-response-mail-settings' );
                            submit_button( __( 'Save Email Settings', 'isw-wp-mailing-list-form' ) );
                            ?>
                        </form>
                    </div>
                </section>
            </div>

            <aside class="isw-ml-settings-layout__sidebar">
                <div class="isw-ml-card isw-ml-card--sidebar">
                    <div class="isw-ml-card__header">
                        <div>
                            <h2><?php echo esc_html__( 'Implementation Notes', 'isw-wp-mailing-list-form' ); ?></h2>
                            <p><?php echo esc_html__( 'A few details worth keeping close while you tune the form.', 'isw-wp-mailing-list-form' ); ?></p>
                        </div>
                    </div>
                    <div class="isw-ml-admin__shortcode-box">[add_isw_ml_form]</div>
                    <p class="description"><?php echo esc_html__( 'Use this shortcode wherever the form should appear on the frontend.', 'isw-wp-mailing-list-form' ); ?></p>
                    <hr />
                    <p><strong><?php echo esc_html__( 'Email template token', 'isw-wp-mailing-list-form' ); ?></strong></p>
                    <div class="isw-ml-admin__shortcode-box">{{name}}</div>
                    <p class="description"><?php echo esc_html__( 'The subscriber name token is replaced automatically in the response email.', 'isw-wp-mailing-list-form' ); ?></p>
                </div>
            </aside>
        </div>
    </div>
    <?php
}

function isw_ml_admin_scripts() {
    if ( ! isw_ml_is_plugin_admin_page() ) {
        return;
    }

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'isw-ml-admin-style', ISW_ML_PLUGIN_URL . 'isw-wp-mailing-list-form-admin.css', array(), ISW_ML_PLUGIN_VERSION );
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'isw-ml-admin-script', ISW_ML_PLUGIN_URL . 'isw-wp-mailing-list-form.js', array( 'wp-color-picker' ), ISW_ML_PLUGIN_VERSION, true );
}

add_action( 'admin_enqueue_scripts', 'isw_ml_admin_scripts' );

function isw_ml_settings_init() {
    if ( isset( $_POST['button_text'] ) ) {
        if ( '' === trim( sanitize_text_field( wp_unslash( $_POST['button_text'] ) ) ) ) {
            $_POST['button_text'] = __( 'Subscribe to our mailing list', 'isw-wp-mailing-list-form' );
        }
    }

    register_setting( 'isw-ml-input-settings-group', 'input_text_color', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_border_color', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_outline_color', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_width_type', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_width_custom', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_align', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_padding_top', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_padding_right', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_padding_bottom', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_padding_left', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_padding_same_all', array( 'sanitize_callback' => 'isw_ml_sanitize_checkbox' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_border_radius', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_name_placeholder', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-input-settings-group', 'input_email_placeholder', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-input-settings-group', 'ml_success_message', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-input-settings-group', 'ml_error_message', array( 'sanitize_callback' => 'sanitize_text_field' ) );

    register_setting( 'isw-ml-button-settings-group', 'button_bg_color', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_text', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_text_color', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_hover_bg_color', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_hover_text_color', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_hover_border_color', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_font_family', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_font_size', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_font_style', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_line_height', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_border_width', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_border_color', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_border_style', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_box_shadow', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_font_weight', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_width_type', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_width_custom', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_align', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_padding_top', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_padding_right', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_padding_bottom', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_padding_left', array( 'sanitize_callback' => 'absint' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_padding_same_all', array( 'sanitize_callback' => 'isw_ml_sanitize_checkbox' ) );
    register_setting( 'isw-ml-button-settings-group', 'button_border_radius', array( 'sanitize_callback' => 'absint' ) );

    register_setting( 'isw-ml-response-mail-settings-group', 'email_from', array( 'sanitize_callback' => 'sanitize_email' ) );
    register_setting( 'isw-ml-response-mail-settings-group', 'email_subject', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'isw-ml-response-mail-settings-group', 'email_template', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );

    add_settings_section( 'isw-ml-settings-input-section', '', 'isw_ml_settings_input_section_callback', 'isw-ml-input-settings' );
    add_settings_field( 'input_text_color', __( 'Input field text color', 'isw-wp-mailing-list-form' ), 'isw_ml_input_text_color_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'input_border_color', __( 'Input field border color', 'isw-wp-mailing-list-form' ), 'isw_ml_input_border_color_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'input_outline_color', __( 'Input field outline color', 'isw-wp-mailing-list-form' ), 'isw_ml_input_outline_color_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'input_width_type', __( 'Input width', 'isw-wp-mailing-list-form' ), 'isw_ml_input_width_type_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'input_width_custom', __( 'Custom input width (px or %)', 'isw-wp-mailing-list-form' ), 'isw_ml_input_width_custom_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'input_align', __( 'Input alignment', 'isw-wp-mailing-list-form' ), 'isw_ml_input_align_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'input_padding_fields', __( 'Input padding (px)', 'isw-wp-mailing-list-form' ), 'isw_ml_input_padding_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'input_border_radius', __( 'Input border radius (px)', 'isw-wp-mailing-list-form' ), 'isw_ml_input_border_radius_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'input_name_placeholder', __( 'Name field placeholder', 'isw-wp-mailing-list-form' ), 'isw_ml_input_name_placeholder_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'input_email_placeholder', __( 'Email field placeholder', 'isw-wp-mailing-list-form' ), 'isw_ml_input_email_placeholder_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'ml_success_message', __( 'Success message', 'isw-wp-mailing-list-form' ), 'isw_ml_success_message_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );
    add_settings_field( 'ml_error_message', __( 'Error message', 'isw-wp-mailing-list-form' ), 'isw_ml_error_message_callback', 'isw-ml-input-settings', 'isw-ml-settings-input-section' );

    add_settings_section( 'isw-ml-settings-button-section', '', 'isw_ml_settings_button_section_callback', 'isw-ml-button-settings' );
    add_settings_field( 'button_bg_color', __( 'Button background color', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_bg_color_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_text_color', __( 'Button text color', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_text_color_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_hover_bg_color', __( 'Button hover background color', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_hover_bg_color_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_hover_text_color', __( 'Button hover text color', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_hover_text_color_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_hover_border_color', __( 'Button hover border color', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_hover_border_color_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_text', __( 'Button text', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_text_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_font_family', __( 'Button font family', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_font_family_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_font_size', __( 'Button font size (px)', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_font_size_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_font_style', __( 'Button font style', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_font_style_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_font_weight', __( 'Button font weight', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_font_weight_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_line_height', __( 'Button line height', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_line_height_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_border_width', __( 'Button border width (px)', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_border_width_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_border_color', __( 'Button border color', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_border_color_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_border_style', __( 'Button border style', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_border_style_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_box_shadow', __( 'Button box shadow', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_box_shadow_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_width_type', __( 'Button width', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_width_type_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_width_custom', __( 'Custom button width (px or %)', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_width_custom_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_align', __( 'Button alignment', 'isw-wp-mailing-list-form' ), 'isw_ml_btn_align_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_padding_fields', __( 'Button padding (px)', 'isw-wp-mailing-list-form' ), 'isw_ml_button_padding_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );
    add_settings_field( 'button_border_radius', __( 'Button border radius (px)', 'isw-wp-mailing-list-form' ), 'isw_ml_button_border_radius_callback', 'isw-ml-button-settings', 'isw-ml-settings-button-section' );

    add_settings_section( 'isw-ml-settings-response-mail-section', '', 'isw_ml_settings_response_mail_section_callback', 'isw-ml-response-mail-settings' );
    add_settings_field( 'response_mail_from', __( 'Response mail from address', 'isw-wp-mailing-list-form' ), 'isw_ml_response_mail_from_callback', 'isw-ml-response-mail-settings', 'isw-ml-settings-response-mail-section' );
    add_settings_field( 'response_mail_subject', __( 'Response mail subject', 'isw-wp-mailing-list-form' ), 'isw_ml_response_mail_subject_callback', 'isw-ml-response-mail-settings', 'isw-ml-settings-response-mail-section' );
    add_settings_field( 'response_mail_template', __( 'Response mail template', 'isw-wp-mailing-list-form' ), 'isw_ml_response_mail_template_callback', 'isw-ml-response-mail-settings', 'isw-ml-settings-response-mail-section' );
}

add_action( 'admin_init', 'isw_ml_settings_init' );

function isw_ml_settings_input_section_callback() {
    echo '<div class="isw-ml-settings-section-intro"><p>' . esc_html__( 'Control colors, widths, spacing, and messages used by the text and email fields.', 'isw-wp-mailing-list-form' ) . '</p></div>';
}

function isw_ml_input_text_color_callback() {
    $input_text_color = get_option( 'input_text_color', '#001f53' );
    echo '<input type="text" id="input_text_color" name="input_text_color" value="' . esc_attr( $input_text_color ) . '" class="isw-ml-color-field" />';
}

function isw_ml_input_border_color_callback() {
    $input_border_color = get_option( 'input_border_color', '#808080' );
    echo '<input type="text" id="input_border_color" name="input_border_color" value="' . esc_attr( $input_border_color ) . '" class="isw-ml-color-field" />';
}

function isw_ml_input_outline_color_callback() {
    $input_outline_color = get_option( 'input_outline_color', '#2684FF' );
    echo '<input type="text" id="input_outline_color" name="input_outline_color" value="' . esc_attr( $input_outline_color ) . '" class="isw-ml-color-field" />';
}

function isw_ml_settings_button_section_callback() {
    echo '<div class="isw-ml-settings-section-intro"><p>' . esc_html__( 'Tune typography, border treatment, spacing, alignment, and shadow for the subscribe button.', 'isw-wp-mailing-list-form' ) . '</p></div>';
}

function isw_ml_btn_bg_color_callback() {
    $button_bg_color = get_option( 'button_bg_color', '#001f53' );
    echo '<input type="text" id="btn_bg_color" name="button_bg_color" value="' . esc_attr( $button_bg_color ) . '" class="isw-ml-color-field" />';
}

function isw_ml_btn_text_color_callback() {
    $button_text_color = get_option( 'button_text_color', '#ffffff' );
    echo '<input type="text" id="btn_text_color" name="button_text_color" value="' . esc_attr( $button_text_color ) . '" class="isw-ml-color-field" />';
}

function isw_ml_btn_hover_bg_color_callback() {
    $button_hover_bg_color = get_option( 'button_hover_bg_color', get_option( 'button_bg_color', '#001f53' ) );
    echo '<input type="text" id="btn_hover_bg_color" name="button_hover_bg_color" value="' . esc_attr( $button_hover_bg_color ) . '" class="isw-ml-color-field" />';
    echo '<p class="description">' . esc_html__( 'Displayed when the visitor hovers over the button.', 'isw-wp-mailing-list-form' ) . '</p>';
}

function isw_ml_btn_hover_text_color_callback() {
    $button_hover_text_color = get_option( 'button_hover_text_color', get_option( 'button_text_color', '#ffffff' ) );
    echo '<input type="text" id="btn_hover_text_color" name="button_hover_text_color" value="' . esc_attr( $button_hover_text_color ) . '" class="isw-ml-color-field" />';
    echo '<p class="description">' . esc_html__( 'Displayed when the visitor hovers over the button text.', 'isw-wp-mailing-list-form' ) . '</p>';
}

function isw_ml_btn_hover_border_color_callback() {
    $button_hover_border_color = get_option( 'button_hover_border_color', get_option( 'button_border_color', '#001f53' ) );
    echo '<input type="text" id="btn_hover_border_color" name="button_hover_border_color" value="' . esc_attr( $button_hover_border_color ) . '" class="isw-ml-color-field" />';
    echo '<p class="description">' . esc_html__( 'Displayed when the visitor hovers over the button border.', 'isw-wp-mailing-list-form' ) . '</p>';
}

function isw_ml_btn_text_callback() {
    $button_text = get_option( 'button_text', __( 'Subscribe to our mailing list', 'isw-wp-mailing-list-form' ) );
    echo '<input type="text" id="btn_text" name="button_text" value="' . esc_attr( $button_text ) . '" class="regular-text isw-ml-control" />';
}

function isw_ml_btn_font_family_callback() {
    $button_font_family = get_option( 'button_font_family', 'inherit' );
    $fonts = array(
        'inherit' => __( 'Inherit (default)', 'isw-wp-mailing-list-form' ),
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
        'Franklin Gothic Medium, Arial Narrow, Arial, sans-serif' => 'Franklin Gothic Medium',
    );

    echo '<select id="btn_font_family" name="button_font_family" class="regular-text isw-ml-control">';
    foreach ( $fonts as $value => $label ) {
        echo '<option value="' . esc_attr( $value ) . '" ' . selected( $button_font_family, $value, false ) . '>' . esc_html( $label ) . '</option>';
    }
    echo '</select>';
}

function isw_ml_btn_font_size_callback() {
    $button_font_size = get_option( 'button_font_size', '16' );
    echo '<div class="isw-ml-unit-field"><input type="number" id="btn_font_size" name="button_font_size" value="' . esc_attr( $button_font_size ) . '" min="8" max="72" class="small-text" /><span class="isw-ml-unit">px</span></div>';
}

function isw_ml_btn_font_style_callback() {
    $button_font_style = get_option( 'button_font_style', 'normal' );
    echo '<select id="btn_font_style" name="button_font_style" class="isw-ml-select"><option value="normal" ' . selected( $button_font_style, 'normal', false ) . '>' . esc_html__( 'Normal', 'isw-wp-mailing-list-form' ) . '</option><option value="italic" ' . selected( $button_font_style, 'italic', false ) . '>' . esc_html__( 'Italic', 'isw-wp-mailing-list-form' ) . '</option><option value="oblique" ' . selected( $button_font_style, 'oblique', false ) . '>' . esc_html__( 'Oblique', 'isw-wp-mailing-list-form' ) . '</option></select>';
}

function isw_ml_btn_font_weight_callback() {
    $button_font_weight = get_option( 'button_font_weight', 'normal' );
    $weights = array(
        'normal' => __( 'Normal', 'isw-wp-mailing-list-form' ),
        'bold' => __( 'Bold', 'isw-wp-mailing-list-form' ),
        '100' => __( '100 (Thin)', 'isw-wp-mailing-list-form' ),
        '200' => __( '200 (Extra Light)', 'isw-wp-mailing-list-form' ),
        '300' => __( '300 (Light)', 'isw-wp-mailing-list-form' ),
        '400' => __( '400 (Normal)', 'isw-wp-mailing-list-form' ),
        '500' => __( '500 (Medium)', 'isw-wp-mailing-list-form' ),
        '600' => __( '600 (Semi Bold)', 'isw-wp-mailing-list-form' ),
        '700' => __( '700 (Bold)', 'isw-wp-mailing-list-form' ),
        '800' => __( '800 (Extra Bold)', 'isw-wp-mailing-list-form' ),
        '900' => __( '900 (Black)', 'isw-wp-mailing-list-form' ),
    );

    echo '<select id="btn_font_weight" name="button_font_weight" class="regular-text isw-ml-control">';
    foreach ( $weights as $value => $label ) {
        echo '<option value="' . esc_attr( $value ) . '" ' . selected( $button_font_weight, $value, false ) . '>' . esc_html( $label ) . '</option>';
    }
    echo '</select>';
}

function isw_ml_btn_line_height_callback() {
    $button_line_height = get_option( 'button_line_height', '1.2' );
    echo '<input type="text" id="btn_line_height" name="button_line_height" value="' . esc_attr( $button_line_height ) . '" class="small-text" placeholder="' . esc_attr__( 'e.g. 1.2', 'isw-wp-mailing-list-form' ) . '" />';
}

function isw_ml_btn_border_width_callback() {
    $button_border_width = get_option( 'button_border_width', '1' );
    echo '<div class="isw-ml-unit-field"><input type="number" id="btn_border_width" name="button_border_width" value="' . esc_attr( $button_border_width ) . '" min="0" max="10" class="small-text" /><span class="isw-ml-unit">px</span></div>';
}

function isw_ml_btn_border_color_callback() {
    $button_border_color = get_option( 'button_border_color', '#001f53' );
    echo '<input type="text" id="btn_border_color" name="button_border_color" value="' . esc_attr( $button_border_color ) . '" class="isw-ml-color-field" />';
}

function isw_ml_btn_border_style_callback() {
    $button_border_style = get_option( 'button_border_style', 'solid' );
    $styles = array(
        'solid' => __( 'Solid', 'isw-wp-mailing-list-form' ),
        'dashed' => __( 'Dashed', 'isw-wp-mailing-list-form' ),
        'dotted' => __( 'Dotted', 'isw-wp-mailing-list-form' ),
        'double' => __( 'Double', 'isw-wp-mailing-list-form' ),
        'groove' => __( 'Groove', 'isw-wp-mailing-list-form' ),
        'ridge' => __( 'Ridge', 'isw-wp-mailing-list-form' ),
        'inset' => __( 'Inset', 'isw-wp-mailing-list-form' ),
        'outset' => __( 'Outset', 'isw-wp-mailing-list-form' ),
        'none' => __( 'None', 'isw-wp-mailing-list-form' ),
    );

    echo '<select id="btn_border_style" name="button_border_style" class="isw-ml-select">';
    foreach ( $styles as $value => $label ) {
        echo '<option value="' . esc_attr( $value ) . '" ' . selected( $button_border_style, $value, false ) . '>' . esc_html( $label ) . '</option>';
    }
    echo '</select>';
}

function isw_ml_btn_box_shadow_callback() {
    $box_shadow = get_option( 'button_box_shadow', '0 2px 6px 0 rgba(0,0,0,0.15)' );
    $inset = '';
    $h_offset = 0;
    $v_offset = 2;
    $blur = 6;
    $spread = 0;
    $color = 'rgba(0,0,0,0.15)';

    if ( preg_match( '/^(inset\s+)?(-?\d+)px\s+(-?\d+)px\s+(\d+)px\s+(-?\d+)px\s+(.+)$/', $box_shadow, $matches ) ) {
        $inset = trim( $matches[1] ) === 'inset' ? 'inset' : '';
        $h_offset = intval( $matches[2] );
        $v_offset = intval( $matches[3] );
        $blur = intval( $matches[4] );
        $spread = intval( $matches[5] );
        $color = trim( $matches[6] );
    } elseif ( preg_match( '/^(inset\s+)?(-?\d+)px\s+(-?\d+)px\s+(\d+)px\s+(.+)$/', $box_shadow, $matches ) ) {
        $inset = trim( $matches[1] ) === 'inset' ? 'inset' : '';
        $h_offset = intval( $matches[2] );
        $v_offset = intval( $matches[3] );
        $blur = intval( $matches[4] );
        $spread = 0;
        $color = trim( $matches[5] );
    }

    $color = preg_replace( '/^(-?\d+px\s*)+/', '', $color );
    ?>
    <div id="isw-box-shadow-controls" class="isw-ml-shadow-editor">
        <div class="isw-ml-shadow-grid">
            <label class="isw-ml-range-field"><?php echo esc_html__( 'H-Offset', 'isw-wp-mailing-list-form' ); ?><input type="range" min="-50" max="50" id="isw_h_offset" value="<?php echo esc_attr( $h_offset ); ?>"><span class="isw-ml-unit-field"><input type="number" min="-50" max="50" id="isw_h_offset_num" value="<?php echo esc_attr( $h_offset ); ?>" class="small-text"><span class="isw-ml-unit">px</span></span></label>
            <label class="isw-ml-range-field"><?php echo esc_html__( 'V-Offset', 'isw-wp-mailing-list-form' ); ?><input type="range" min="-50" max="50" id="isw_v_offset" value="<?php echo esc_attr( $v_offset ); ?>"><span class="isw-ml-unit-field"><input type="number" min="-50" max="50" id="isw_v_offset_num" value="<?php echo esc_attr( $v_offset ); ?>" class="small-text"><span class="isw-ml-unit">px</span></span></label>
            <label class="isw-ml-range-field"><?php echo esc_html__( 'Blur', 'isw-wp-mailing-list-form' ); ?><input type="range" min="0" max="100" id="isw_blur" value="<?php echo esc_attr( $blur ); ?>"><span class="isw-ml-unit-field"><input type="number" min="0" max="100" id="isw_blur_num" value="<?php echo esc_attr( $blur ); ?>" class="small-text"><span class="isw-ml-unit">px</span></span></label>
            <label class="isw-ml-range-field"><?php echo esc_html__( 'Spread', 'isw-wp-mailing-list-form' ); ?><input type="range" min="-50" max="50" id="isw_spread" value="<?php echo esc_attr( $spread ); ?>"><span class="isw-ml-unit-field"><input type="number" min="-50" max="50" id="isw_spread_num" value="<?php echo esc_attr( $spread ); ?>" class="small-text"><span class="isw-ml-unit">px</span></span></label>
        </div>
        <div class="isw-ml-shadow-toolbar">
            <label class="isw-ml-shadow-color"><?php echo esc_html__( 'Color', 'isw-wp-mailing-list-form' ); ?><input type="text" id="isw_box_shadow_color" value="<?php echo esc_attr( $color ); ?>" class="isw-ml-color-field" /></label>
            <label class="isw-ml-checkbox"><input type="checkbox" id="isw_box_shadow_inset" <?php checked( $inset, 'inset' ); ?>> <?php echo esc_html__( 'Inset', 'isw-wp-mailing-list-form' ); ?></label>
        </div>
        <input type="hidden" id="btn_box_shadow" name="button_box_shadow" value="<?php echo esc_attr( $box_shadow ); ?>" />
        <div class="isw-ml-shadow-preview-wrap">
            <span><?php echo esc_html__( 'Preview', 'isw-wp-mailing-list-form' ); ?>:</span>
            <div id="isw_box_shadow_preview" class="isw-ml-shadow-preview"></div>
        </div>
    </div>
    <?php
}

function isw_ml_settings_response_mail_section_callback() {
    echo '<div class="isw-ml-settings-section-intro"><p>' . esc_html__( 'Set the sender identity and the default copy that subscribers receive after signup.', 'isw-wp-mailing-list-form' ) . '</p></div>';
}

function isw_ml_response_mail_from_callback() {
    $email_from = get_option( 'email_from', 'noreply@domain.com' );
    echo '<input type="email" id="email_from" name="email_from" value="' . esc_attr( $email_from ) . '" class="regular-text isw-ml-control" />';
}

function isw_ml_response_mail_subject_callback() {
    $email_subject = get_option( 'email_subject', __( 'Thank you for your subscription!', 'isw-wp-mailing-list-form' ) );
    echo '<input type="text" id="email_subject" name="email_subject" value="' . esc_attr( $email_subject ) . '" class="regular-text isw-ml-control" />';
}

function isw_ml_response_mail_template_callback() {
    $email_template = get_option( 'email_template', __( 'Dear {{name}}, thank you for your subscription!', 'isw-wp-mailing-list-form' ) );
    echo '<textarea id="email_template" name="email_template" rows="6" class="large-text code isw-ml-control">' . esc_textarea( $email_template ) . '</textarea>';
}

function isw_ml_input_name_placeholder_callback() {
    $ph = get_option( 'input_name_placeholder', __( 'Your name...', 'isw-wp-mailing-list-form' ) );
    echo '<input type="text" id="input_name_placeholder" name="input_name_placeholder" value="' . esc_attr( $ph ) . '" class="regular-text isw-ml-control" />';
}

function isw_ml_input_email_placeholder_callback() {
    $ph = get_option( 'input_email_placeholder', __( 'Your E-Mail address...', 'isw-wp-mailing-list-form' ) );
    echo '<input type="text" id="input_email_placeholder" name="input_email_placeholder" value="' . esc_attr( $ph ) . '" class="regular-text isw-ml-control" />';
}

function isw_ml_success_message_callback() {
    $msg = get_option( 'ml_success_message', __( 'Your E-mail address was successfully submitted. Thank you!', 'isw-wp-mailing-list-form' ) );
    echo '<input type="text" id="ml_success_message" name="ml_success_message" value="' . esc_attr( $msg ) . '" class="regular-text isw-ml-control" />';
}

function isw_ml_error_message_callback() {
    $msg = get_option( 'ml_error_message', __( 'There was an error with your submission. Please try again.', 'isw-wp-mailing-list-form' ) );
    echo '<input type="text" id="ml_error_message" name="ml_error_message" value="' . esc_attr( $msg ) . '" class="regular-text isw-ml-control" />';
}

function isw_ml_btn_width_type_callback() {
    $value = get_option( 'button_width_type', 'full' );
    ?>
    <select id="btn_width_type" name="button_width_type" class="isw-ml-select">
        <option value="full" <?php selected( $value, 'full' ); ?>><?php echo esc_html__( 'Full width', 'isw-wp-mailing-list-form' ); ?></option>
        <option value="1/2" <?php selected( $value, '1/2' ); ?>>1/2</option>
        <option value="1/3" <?php selected( $value, '1/3' ); ?>>1/3</option>
        <option value="1/4" <?php selected( $value, '1/4' ); ?>>1/4</option>
        <option value="custom" <?php selected( $value, 'custom' ); ?>><?php echo esc_html__( 'Custom', 'isw-wp-mailing-list-form' ); ?></option>
    </select>
    <?php
}

function isw_ml_btn_width_custom_callback() {
    $value = get_option( 'button_width_custom', '' );
    ?>
    <input type="text" id="btn_width_custom" name="button_width_custom" value="<?php echo esc_attr( $value ); ?>" class="regular-text isw-ml-control" placeholder="<?php echo esc_attr__( 'e.g. 200px or 50%', 'isw-wp-mailing-list-form' ); ?>" />
    <p class="description"><?php echo esc_html__( 'Enter e.g. 200px or 50%.', 'isw-wp-mailing-list-form' ); ?></p>
    <?php
}

function isw_ml_btn_align_callback() {
    $value = get_option( 'button_align', 'center' );
    ?>
    <select id="btn_align" name="button_align" class="isw-ml-select">
        <option value="left" <?php selected( $value, 'left' ); ?>><?php echo esc_html__( 'Left', 'isw-wp-mailing-list-form' ); ?></option>
        <option value="center" <?php selected( $value, 'center' ); ?>><?php echo esc_html__( 'Center', 'isw-wp-mailing-list-form' ); ?></option>
        <option value="right" <?php selected( $value, 'right' ); ?>><?php echo esc_html__( 'Right', 'isw-wp-mailing-list-form' ); ?></option>
    </select>
    <?php
}

function isw_ml_input_width_type_callback() {
    $value = get_option( 'input_width_type', 'full' );
    ?>
    <select id="input_width_type" name="input_width_type" class="isw-ml-select">
        <option value="full" <?php selected( $value, 'full' ); ?>><?php echo esc_html__( 'Full width', 'isw-wp-mailing-list-form' ); ?></option>
        <option value="1/2" <?php selected( $value, '1/2' ); ?>>1/2</option>
        <option value="1/3" <?php selected( $value, '1/3' ); ?>>1/3</option>
        <option value="1/4" <?php selected( $value, '1/4' ); ?>>1/4</option>
        <option value="custom" <?php selected( $value, 'custom' ); ?>><?php echo esc_html__( 'Custom', 'isw-wp-mailing-list-form' ); ?></option>
    </select>
    <?php
}

function isw_ml_input_width_custom_callback() {
    $value = get_option( 'input_width_custom', '' );
    ?>
    <input type="text" id="input_width_custom" name="input_width_custom" value="<?php echo esc_attr( $value ); ?>" class="regular-text isw-ml-control" placeholder="<?php echo esc_attr__( 'e.g. 200px or 50%', 'isw-wp-mailing-list-form' ); ?>" />
    <p class="description"><?php echo esc_html__( 'Enter e.g. 200px or 50%.', 'isw-wp-mailing-list-form' ); ?></p>
    <?php
}

function isw_ml_input_align_callback() {
    $value = get_option( 'input_align', 'center' );
    ?>
    <select id="input_align" name="input_align" class="isw-ml-select">
        <option value="left" <?php selected( $value, 'left' ); ?>><?php echo esc_html__( 'Left', 'isw-wp-mailing-list-form' ); ?></option>
        <option value="center" <?php selected( $value, 'center' ); ?>><?php echo esc_html__( 'Center', 'isw-wp-mailing-list-form' ); ?></option>
        <option value="right" <?php selected( $value, 'right' ); ?>><?php echo esc_html__( 'Right', 'isw-wp-mailing-list-form' ); ?></option>
    </select>
    <?php
}

function isw_ml_input_padding_callback() {
    $same = get_option( 'input_padding_same_all', 1 );
    $top = get_option( 'input_padding_top', 16 );
    $right = get_option( 'input_padding_right', 16 );
    $bottom = get_option( 'input_padding_bottom', 16 );
    $left = get_option( 'input_padding_left', 16 );
    ?>
    <div class="isw-ml-padding-editor" data-sync-group="input_padding">
    <label class="isw-ml-checkbox">
        <input type="checkbox" id="input_padding_same_all" name="input_padding_same_all" value="1" <?php checked( $same, 1 ); ?> />
        <?php echo esc_html__( 'Use the same padding on all four sides', 'isw-wp-mailing-list-form' ); ?>
    </label>
    <div id="input-padding-fields" class="isw-ml-padding-grid">
        <label><?php echo esc_html__( 'Top', 'isw-wp-mailing-list-form' ); ?><span class="isw-ml-unit-field"><input type="number" id="input_padding_top" name="input_padding_top" value="<?php echo esc_attr( $top ); ?>" min="0" class="small-text" /><span class="isw-ml-unit">px</span></span></label>
        <label><?php echo esc_html__( 'Right', 'isw-wp-mailing-list-form' ); ?><span class="isw-ml-unit-field"><input type="number" id="input_padding_right" name="input_padding_right" value="<?php echo esc_attr( $right ); ?>" min="0" class="small-text" /><span class="isw-ml-unit">px</span></span></label>
        <label><?php echo esc_html__( 'Bottom', 'isw-wp-mailing-list-form' ); ?><span class="isw-ml-unit-field"><input type="number" id="input_padding_bottom" name="input_padding_bottom" value="<?php echo esc_attr( $bottom ); ?>" min="0" class="small-text" /><span class="isw-ml-unit">px</span></span></label>
        <label><?php echo esc_html__( 'Left', 'isw-wp-mailing-list-form' ); ?><span class="isw-ml-unit-field"><input type="number" id="input_padding_left" name="input_padding_left" value="<?php echo esc_attr( $left ); ?>" min="0" class="small-text" /><span class="isw-ml-unit">px</span></span></label>
    </div>
    </div>
    <?php
}

function isw_ml_button_padding_callback() {
    $same = get_option( 'button_padding_same_all', 1 );
    $top = get_option( 'button_padding_top', 16 );
    $right = get_option( 'button_padding_right', 16 );
    $bottom = get_option( 'button_padding_bottom', 16 );
    $left = get_option( 'button_padding_left', 16 );
    ?>
    <div class="isw-ml-padding-editor" data-sync-group="button_padding">
    <label class="isw-ml-checkbox">
        <input type="checkbox" id="button_padding_same_all" name="button_padding_same_all" value="1" <?php checked( $same, 1 ); ?> />
        <?php echo esc_html__( 'Use the same padding on all four sides', 'isw-wp-mailing-list-form' ); ?>
    </label>
    <div id="button-padding-fields" class="isw-ml-padding-grid">
        <label><?php echo esc_html__( 'Top', 'isw-wp-mailing-list-form' ); ?><span class="isw-ml-unit-field"><input type="number" id="button_padding_top" name="button_padding_top" value="<?php echo esc_attr( $top ); ?>" min="0" class="small-text" /><span class="isw-ml-unit">px</span></span></label>
        <label><?php echo esc_html__( 'Right', 'isw-wp-mailing-list-form' ); ?><span class="isw-ml-unit-field"><input type="number" id="button_padding_right" name="button_padding_right" value="<?php echo esc_attr( $right ); ?>" min="0" class="small-text" /><span class="isw-ml-unit">px</span></span></label>
        <label><?php echo esc_html__( 'Bottom', 'isw-wp-mailing-list-form' ); ?><span class="isw-ml-unit-field"><input type="number" id="button_padding_bottom" name="button_padding_bottom" value="<?php echo esc_attr( $bottom ); ?>" min="0" class="small-text" /><span class="isw-ml-unit">px</span></span></label>
        <label><?php echo esc_html__( 'Left', 'isw-wp-mailing-list-form' ); ?><span class="isw-ml-unit-field"><input type="number" id="button_padding_left" name="button_padding_left" value="<?php echo esc_attr( $left ); ?>" min="0" class="small-text" /><span class="isw-ml-unit">px</span></span></label>
    </div>
    </div>
    <?php
}

function isw_ml_input_border_radius_callback() {
    $value = get_option( 'input_border_radius', 16 );
    echo '<div class="isw-ml-unit-field"><input type="number" id="input_border_radius" name="input_border_radius" value="' . esc_attr( $value ) . '" min="0" class="small-text" /><span class="isw-ml-unit">px</span></div>';
}

function isw_ml_button_border_radius_callback() {
    $value = get_option( 'button_border_radius', 16 );
    echo '<div class="isw-ml-unit-field"><input type="number" id="button_border_radius" name="button_border_radius" value="' . esc_attr( $value ) . '" min="0" class="small-text" /><span class="isw-ml-unit">px</span></div>';
}

function isw_ml_sanitize_checkbox( $value ) {
    return $value ? 1 : 0;
}