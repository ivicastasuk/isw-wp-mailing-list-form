<?php

require_once(dirname(__FILE__, 3) . '/wp-load.php');

$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
if ( ! wp_verify_nonce($nonce, 'isw_ml_export_csv') ) {
    wp_die( esc_html__( 'Security check failed.', 'isw-wp-mailing-list-form' ) );
}

if (!current_user_can('manage_options')) {
    wp_die( esc_html__( 'You do not have access to export this data.', 'isw-wp-mailing-list-form' ) );
}

global $wpdb;
$isw_table = function_exists( 'isw_ml_get_table_name' ) ? isw_ml_get_table_name() : $wpdb->prefix . 'isw_ml';

if ( function_exists( 'isw_ml_table_exists' ) ? isw_ml_table_exists() : ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $isw_table ) ) === $isw_table ) ) {
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
    $data = $wpdb->get_results( "SELECT * FROM `$isw_table`" );
} else {
    wp_die( esc_html__( 'Table does not exist.', 'isw-wp-mailing-list-form' ) );
}

if($data && count($data) > 0){
    $delimiter = ",";
    $filename = "isw-ml_" . gmdate('Ymd') . ".csv";

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    $f = fopen('php://output', 'w');
    $headers = array(
        __( 'Display Name', 'isw-wp-mailing-list-form' ),
        __( 'Primary Email', 'isw-wp-mailing-list-form' ),
    );
    fputcsv($f, $headers, $delimiter);

    foreach($data as $row){
        // Za CSV je bolje koristiti sanitize_text_field (ne esc_html)
        $line_data = array(sanitize_text_field($row->name), sanitize_email($row->email));
        fputcsv($f, $line_data, $delimiter);
    }

}

exit;