<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function isw_ml_get_table_name() {
    global $wpdb;

    return $wpdb->prefix . 'isw_ml';
}

function isw_ml_get_table_schema() {
    global $wpdb;

    $table_name = isw_ml_get_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    return "CREATE TABLE {$table_name} (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(191) NOT NULL,
        is_new int(11) NOT NULL DEFAULT 1,
        PRIMARY KEY  (id),
        UNIQUE KEY email (email)
    ) {$charset_collate};";
}

function isw_ml_table_exists() {
    global $wpdb;

    $table_name = isw_ml_get_table_name();
    $cache_key = 'isw_ml_table_exists_' . $table_name;
    $table_exists = wp_cache_get( $cache_key );

    if ( false === $table_exists ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;
        wp_cache_set( $cache_key, $table_exists, '', 3600 );
    }

    return (bool) $table_exists;
}

function isw_ml_create_table() {
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta( isw_ml_get_table_schema() );
    wp_cache_set( 'isw_ml_table_exists_' . isw_ml_get_table_name(), true, '', 3600 );
}

function isw_ml_ensure_table() {
    if ( isw_ml_table_exists() ) {
        return true;
    }

    isw_ml_create_table();

    return isw_ml_table_exists();
}

function isw_ml_activate() {
    isw_ml_create_table();
}

register_activation_hook( ISW_ML_PLUGIN_FILE, 'isw_ml_activate' );

function isw_ml_get_status_message( $status ) {
    switch ( $status ) {
        case 'success':
            return array(
                'class' => 'isw-ml-form-message',
                'text'  => get_option( 'ml_success_message', __( 'Your E-mail address was successfully submitted. Thank you!', 'isw-wp-mailing-list-form' ) ),
            );
        case 'duplicate':
            return array(
                'class' => 'isw-ml-form-message isw-ml-error',
                'text'  => __( 'This email address is already subscribed.', 'isw-wp-mailing-list-form' ),
            );
        case 'invalid_email':
            return array(
                'class' => 'isw-ml-form-message isw-ml-error',
                'text'  => __( 'Please enter a valid email address.', 'isw-wp-mailing-list-form' ),
            );
        case 'invalid_nonce':
            return array(
                'class' => 'isw-ml-form-message isw-ml-error',
                'text'  => __( 'Security check failed. Please try again.', 'isw-wp-mailing-list-form' ),
            );
        case 'db':
        case 'table':
        case 'error':
        default:
            return array(
                'class' => 'isw-ml-form-message isw-ml-error',
                'text'  => get_option( 'ml_error_message', __( 'There was an error with your submission. Please try again.', 'isw-wp-mailing-list-form' ) ),
            );
    }
}

function isw_ml_redirect_with_status( $status ) {
    $redirect_url = wp_get_referer();

    if ( ! $redirect_url ) {
        $redirect_url = home_url( '/' );
    }

    $redirect_url = remove_query_arg( array( 'ml_status', 'ml_error', 'ml_submitted' ), $redirect_url );
    $redirect_url = add_query_arg( 'ml_status', sanitize_key( $status ), $redirect_url );

    wp_safe_redirect( $redirect_url );
    exit;
}

function isw_ml_insert_subscriber( $name, $email ) {
    global $wpdb;

    if ( ! is_email( $email ) ) {
        return new WP_Error( 'invalid_email', __( 'Invalid email address.', 'isw-wp-mailing-list-form' ) );
    }

    if ( ! isw_ml_ensure_table() ) {
        return new WP_Error( 'table', __( 'Unable to create the subscribers table.', 'isw-wp-mailing-list-form' ) );
    }

    $table_name = isw_ml_get_table_name();

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $existing_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table_name} WHERE email = %s LIMIT 1", $email ) );
    if ( $existing_id ) {
        return new WP_Error( 'duplicate', __( 'Email already exists.', 'isw-wp-mailing-list-form' ) );
    }

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $result = $wpdb->insert(
        $table_name,
        array(
            'name'   => $name,
            'email'  => $email,
            'is_new' => 1,
        ),
        array( '%s', '%s', '%d' )
    );

    if ( false === $result ) {
        return new WP_Error( 'db', __( 'Failed to store subscriber.', 'isw-wp-mailing-list-form' ) );
    }

    wp_cache_delete( 'isw_ml_all_entries_' . $table_name );
    wp_cache_delete( 'isw_ml_new_entries_count_' . $table_name );

    isw_send_thankyou_email( $email, $name );

    return true;
}

function isw_get_new_entries_count() {
    global $wpdb;

    if ( ! isw_ml_ensure_table() ) {
        return 0;
    }

    $isw_table = isw_ml_get_table_name();
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

    if ( ! isw_ml_table_exists() ) {
        return;
    }

    $isw_table = isw_ml_get_table_name();

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $wpdb->query( $wpdb->prepare( "UPDATE $isw_table SET is_new = %d WHERE is_new = %d", 0, 1 ) );
    wp_cache_delete( 'isw_ml_new_entries_count_' . $isw_table );
}

function isw_send_thankyou_email( $to_email, $subscriber_name ) {
    $subject = get_option( 'email_subject', __( 'Thank you for your subscription!', 'isw-wp-mailing-list-form' ) );
    $template = get_option( 'email_template', __( 'Dear {{name}}, thank you for your subscription!', 'isw-wp-mailing-list-form' ) );
    $message = str_replace( '{{name}}', $subscriber_name, $template );
    $from = get_option( 'email_from', 'noreply@domain.com' );
    $headers = array(
        'From: ' . sanitize_email( $from ),
        'Content-Type: text/plain; charset=UTF-8',
    );

    wp_mail( $to_email, $subject, $message, $headers );
}