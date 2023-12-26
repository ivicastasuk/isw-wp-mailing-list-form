<?php

require_once('../../../wp-load.php');

if (!current_user_can('export')) {
    wp_die('You don\'t have access to export this data.');
}

ob_start();

global $wpdb;
$isw_table = $wpdb->prefix . 'isw_ml';

$data = $wpdb->get_results("SELECT * FROM $isw_table");

if(count($data) > 0){
    $delimiter = ",";
    $filename = "isw-ml_" . date('Ymd') . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    $f = fopen('php://output', 'w');
    $headers = array('DisplayName', 'PrimaryEmail');
    fputcsv($f, $headers, $delimiter);

    foreach($data as $row){
        $line_data = array($row->name, $row->email);
        fputcsv($f, $line_data, $delimiter);
    }

    fclose($f);
}

ob_end_flush();

exit;
