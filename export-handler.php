<?php

require_once(dirname(__FILE__, 2) . '/wp-load.php');

// Provera nonce-a (dodajte _wpnonce parametar u admin link za eksport)
if ( ! isset($_GET['_wpnonce']) || ! wp_verify_nonce($_GET['_wpnonce'], 'isw_ml_export_csv') ) {
    wp_die('Security check failed.');
}

// Provera privilegija
if (!current_user_can('manage_options')) {
    wp_die('You don\'t have access to export this data.');
}

global $wpdb;
$isw_table = $wpdb->prefix . 'isw_ml';

$data = $wpdb->get_results("SELECT * FROM $isw_table");

if($data && count($data) > 0){
    $delimiter = ",";
    $filename = "isw-ml_" . date('Ymd') . ".csv";

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    $f = fopen('php://output', 'w');
    $headers = array('Display Name', 'Primary Email');
    fputcsv($f, $headers, $delimiter);

    foreach($data as $row){
        $line_data = array(esc_html($row->name), esc_html($row->email));
        fputcsv($f, $line_data, $delimiter);
    }

    fclose($f);
}

exit;