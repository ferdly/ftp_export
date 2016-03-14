<?php

require_once 'ftp_export_object.php';

$ftp_export_config_these = new ftpx_config();
$server_status = 'DEVELOPMENT';
$content_status = 'DEVEL';

$ftp_export_config_these->server_status = $server_status;
$ftp_export_config_these->content_status = $content_status;
$ftp_export_config_these->status_normalize();


$ftp_export_instance_this =  new ftpx_instance();
$commonname = "Carsoup";
$ftp_export_instance_this->ftp_commonname = $commonname;
// $which_array = array('from', 'to');
$which_array = array('SSKIP', 'to');
// $status_array = $ftp_export_config->supported_status_array;
$status_array = array('dev'); //manual overload

foreach ($which_array as $which_index => $which) {
    foreach ($status_array as $status_index => $status) {
        _server_status_config($ftp_export_instance_this, $commonname, $which, $status);
    }
}

$ftp_export_instance_this->server_status = $ftp_export_config_these->server_status;
if ($ftp_export_config_these->server_status !== $ftp_export_instance_this->server_status) {
  $ftp_export_config_these->server_status = 'EERROR';
}
$ftp_export_instance_this->content_status = $ftp_export_config_these->content_status;
if ($ftp_export_config_these->content_status !== $ftp_export_instance_this->content_status) {
  $ftp_export_config_these->content_status = 'EERROR';
}
$ftp_export_config_these->instance_array[] = $ftp_export_instance_this;
unset($ftp_export_instance_this);

$ftp_export_instance_this =  new ftpx_instance();
$commonname = "FindCars";
$ftp_export_instance_this->ftp_commonname = $commonname;
// $which_array = array('from', 'to');
$which_array = array('SSKIP', 'to');
// $status_array = $ftp_export_config->supported_status_array;
$status_array = array('dev'); //manual overload

foreach ($which_array as $which_index => $which) {
    foreach ($status_array as $status_index => $status) {
        _server_status_config($ftp_export_instance_this, $commonname, $which, $status);
    }
}

$ftp_export_instance_this->server_status = $ftp_export_config_these->server_status;
if ($ftp_export_config_these->server_status !== $ftp_export_instance_this->server_status) {
  $ftp_export_config_these->server_status = 'EERROR';
}
$ftp_export_instance_this->content_status = $ftp_export_config_these->content_status;
if ($ftp_export_config_these->content_status !== $ftp_export_instance_this->content_status) {
  $ftp_export_config_these->content_status = 'EERROR';
}
$ftp_export_config_these->instance_array[] = $ftp_export_instance_this;
unset($ftp_export_instance_this);

$config_success = $ftp_export_config_these->server_status !== 'EERROR'?true:false;
$config_success = $ftp_export_config_these->server_status !== 'EERROR'?$config_success:false;
$ftp_export_config_these->config_success = $config_success;
#\_ commerce_order has a Validate functino, could make this a method here, but overkill for now


function _server_status_config(ftpx_instance $ftp_export_instance_this, $commonname, $which, $status) {
    if ($which == 'SSKIP') {
        $combo_key = $commonname . '_' . $which . '_' . $status;
        $ftp_export_instance_this->ftp_combo_key_array[] = 'SSKIP_' . $combo_key;
        return;
    }
    $suppported_commonname_array = array('CarSoup','FindCars');
    $suppported_which_array = array('to','from');
    $supported_status_array = array('prod','staging','prod');
    $is_valid = TRUE;
    // in_array(needle, haystack)
    // $is_valid = in_array($commonname, $suppported_commonname_array) ? $is_valid : FALSE;
    // $is_valid = in_array($which, $suppported_which_array) ? $is_valid : FALSE;
    // $is_valid = in_array($status, $supported_status_array) ? $is_valid : FALSE;

    $commonname = strtolower(str_replace(' ', '_', $commonname));

    $combo_key = $commonname . '_' . $which . '_' . $status;
    switch ($combo_key) {
        #\_ add $combo_key case-block as needed, case-default catches any not entered
        case 'carsoup_to_dev':
            $ftp_export_instance_this->ftp_to_from = $which;
            $ftp_export_instance_this->ftp_combo_key_array[] = $combo_key;
            $ftp_export_instance_this->ftp_host = "dev_upload.carsoup.com";
            $ftp_export_instance_this->ftp_username = "QIQgroup";
            $ftp_export_instance_this->ftp_password = "S0m3P@ssw0rd";
            $ftp_export_instance_this->ftp_port = 21;
            $ftp_export_instance_this->ftp_directory = "";
            $ftp_export_instance_this->ftp_filename = "htmotors";
            $ftp_export_instance_this->ftp_filename = "htmotors";
            $ftp_export_instance_this->ftp_fileextension = "csv";
            $ftp_export_instance_this->file_generation_callback = "carsoup-export-csv";//path to Views Export
            break;

        case 'findcars_to_dev':
            $ftp_export_instance_this->ftp_to_from = $which;
            $ftp_export_instance_this->ftp_combo_key_array[] = $combo_key;
            $ftp_export_instance_this->ftp_host = "car"; //
            $ftp_export_instance_this->ftp_username = "QIQgroup";
            $ftp_export_instance_this->ftp_password = "S0m3P@ssw0rd";
            $ftp_export_instance_this->ftp_port = 21;
            $ftp_export_instance_this->ftp_directory = "";
            $ftp_export_instance_this->ftp_filename = "htmotors";
            $ftp_export_instance_this->ftp_filename = "htmotors";
            $ftp_export_instance_this->ftp_fileextension = "csv";
            $ftp_export_instance_this->file_generation_callback = "findcars-export-csv";//path to Views Export
            break;

        default:
            $ftp_export_instance_this->ftp_combo_key_array[] = 'DDEFAULT_' . $combo_key;
            $is_valid = FALSE;
            break;
    }

    $default_value = 'EERROR'; // or empty string, or FALSE, or Null...
    if ($is_valid === FALSE) {
        $ftp_export_instance_this->ftp_host = $default_value; //$default_value; //
        $ftp_export_instance_this->ftp_username = $default_value; //"QIQgroup";
        $ftp_export_instance_this->ftp_password = $default_value; //"S0m3P@ssw0rd";
        $ftp_export_instance_this->ftp_port = $default_value; //21;
        $ftp_export_instance_this->ftp_directory = $default_value; //"";'IINVALID' .
        $ftp_export_instance_this->ftp_filename = $default_value; //"htmotors";
        $ftp_export_instance_this->ftp_filename = $default_value; //"htmotors";
        $ftp_export_instance_this->ftp_fileextension = $default_value; //"csv";
        return;
    }

}
