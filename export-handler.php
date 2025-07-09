<?php

require_once(dirname(__FILE__, 3) . '/wp-load.php');

// Provera nonce-a (dodajte _wpnonce parametar u admin link za eksport)
$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
if ( ! wp_verify_nonce($nonce, 'isw_ml_export_csv') ) {
    wp_die('Security check failed.');
}

// Provera privilegija
if (!current_user_can('manage_options')) {
    wp_die('You don\'t have access to export this data.');
}

global $wpdb;
$isw_table = $wpdb->prefix . 'isw_ml';

// Proverite da je ime tabele validno (opciono, dodatna bezbednost)
if ( $wpdb->is_table( $isw_table ) ) {
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
    $data = $wpdb->get_results( "SELECT * FROM `$isw_table`" );
} else {
    wp_die('Table does not exist.');
}

if($data && count($data) > 0){
    $delimiter = ",";
    $filename = "isw-ml_" . gmdate('Ymd') . ".csv";

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    $f = fopen('php://output', 'w');
    $headers = array('Display Name', 'Primary Email');
    fputcsv($f, $headers, $delimiter);

    foreach($data as $row){
        // Za CSV je bolje koristiti sanitize_text_field (ne esc_html)
        $line_data = array(sanitize_text_field($row->name), sanitize_email($row->email));
        fputcsv($f, $line_data, $delimiter);
    }

}

exit;