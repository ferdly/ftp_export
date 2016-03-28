<?php

/**
 * Will be Included
 * * so below will check for pre-set params, else dev
 * * values below should mirror Class: ftpx_config, Method: status_normalize(), Line: ~ 35
 */

$server_status = !isset($_ENV['PANTHEON_ENVIRONMENT']) ? 'DEV' : strtoupper($_ENV['PANTHEON_ENVIRONMENT']);
$server_status = $server_status == 'LIVE' ? 'LIVE' : 'DEV';
$content_status = $server_status == 'LIVE' ? 'LIVE' : 'DEV';

$supported_status_array = array(
    'DEV',
    'DEVEL',
    'DEVELOPMENT',
    'STAGING',
    'STAGE',
    'TEST',
    'TESTING',
    'PROD',
    'PRODUCTION',
    'LIVE',
);
$server_status = in_array(@$server_status, $supported_status_array) ? $server_status : 'DEVELOPMENT';
$content_status = in_array(@$content_status, $supported_status_array) ? $content_status : 'DEVEL';


require_once 'ftp_export_object.php';

$time_now_or_other = time();
// $time_now_or_other = strtotime('+1 month');//For Testing
$ftp_export_config_these = new ftpx_config($time_now_or_other);
// $server_status = 'DEVELOPMENT';
// $content_status = 'DEVEL';

$ftp_export_config_these->server_status = $server_status;
$ftp_export_config_these->content_status = $content_status;
$ftp_export_config_these->status_normalize();

// $time_now_or_other = strtotime('+1 minute',$time_now_or_other);
$ftp_export_instance_this =  new ftpx_instance($time_now_or_other);
$commonname = "Carsoup";
$ftp_export_instance_this->ftp_commonname = $commonname;
$ftp_export_instance_this->save_archive_uri = $ftp_export_config_these->save_archive_uri;
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

// $time_now_or_other = strtotime('+2 minute',$time_now_or_other);
$ftp_export_instance_this =  new ftpx_instance($time_now_or_other);
$commonname = "FindCars";
$ftp_export_instance_this->ftp_commonname = $commonname;
$ftp_export_instance_this->save_archive_uri = $ftp_export_config_these->save_archive_uri;
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
            $ftp_export_instance_this->ftp_passive_mode = TRUE;
            $ftp_export_instance_this->ftp_combo_key_array[] = $combo_key;
            $ftp_export_instance_this->ftp_host = "skerbitz.org";
            $ftp_export_instance_this->ftp_username = "brad";
            $ftp_export_instance_this->ftp_password = "netGhaj8";
            $ftp_export_instance_this->ftp_port = 21;
            $ftp_export_instance_this->ftp_directory = "files";
            $ftp_export_instance_this->save_uri = "ftp_archive";
            $ftp_export_instance_this->save_filename = "CS_HTMotors_{STAMP_DASH_T}";
            $ftp_export_instance_this->save_fileextension = "txt";
            $ftp_export_instance_this->ftp_filename = "CS_HTMotors_{STAMP_DASH_T}";
            $ftp_export_instance_this->ftp_fileextension = "txt";
            $ftp_export_instance_this->save_callback_view_name = 'ttc_first';
            $ftp_export_instance_this->save_callback_view_display_id = 'views_data_export_1';
            break;

        case 'findcars_to_dev':
            $ftp_export_instance_this->ftp_to_from = $which;
            $ftp_export_instance_this->ftp_passive_mode = TRUE;
            $ftp_export_instance_this->ftp_combo_key_array[] = $combo_key;
            $ftp_export_instance_this->ftp_host = "skerbitz.org"; //
            $ftp_export_instance_this->ftp_username = "brad";
            $ftp_export_instance_this->ftp_password = "netGhaj8";
            $ftp_export_instance_this->ftp_port = 21;
            $ftp_export_instance_this->ftp_directory = "files";
            $ftp_export_instance_this->save_uri = "ftp_archive";
            $ftp_export_instance_this->save_filename = "FC_HTMotors_{STAMP_DASH_T}";
            $ftp_export_instance_this->save_fileextension = "txt";
            $ftp_export_instance_this->ftp_filename = "qiq_inventory";
            $ftp_export_instance_this->ftp_fileextension = "txt";
            $ftp_export_instance_this->save_callback_view_name = 'ttc_second';
            $ftp_export_instance_this->save_callback_view_display_id = 'views_data_export_1';
            break;
        case 'carsoup_to_live':
            $ftp_export_instance_this->ftp_to_from = $which;
            $ftp_export_instance_this->ftp_passive_mode = TRUE;
            $ftp_export_instance_this->ftp_combo_key_array[] = $combo_key;
            $ftp_export_instance_this->ftp_host = "upload.carsoup.com";
            $ftp_export_instance_this->ftp_username = "QIQgroup";
            $ftp_export_instance_this->ftp_password = "qIq1627*";
            $ftp_export_instance_this->ftp_port = 21;
            $ftp_export_instance_this->ftp_directory = "";
            $ftp_export_instance_this->save_uri = "ftp_archive";
            $ftp_export_instance_this->save_filename = "CS_HTMotors_{STAMP_DASH_T}";
            $ftp_export_instance_this->save_fileextension = "txt";
            $ftp_export_instance_this->ftp_filename = "CS_HTMotors_{STAMP_DASH_T}";
            $ftp_export_instance_this->ftp_fileextension = "txt";
            $ftp_export_instance_this->save_callback_view_name = 'ttc_first';
            $ftp_export_instance_this->save_callback_view_display_id = 'views_data_export_1';
            break;

        case 'findcars_to_live':
            $ftp_export_instance_this->ftp_to_from = $which;
            $ftp_export_instance_this->ftp_passive_mode = TRUE;
            $ftp_export_instance_this->ftp_combo_key_array[] = $combo_key;
            $ftp_export_instance_this->ftp_host = "ftp.findcars.com"; //
            $ftp_export_instance_this->ftp_username = "qiqfeed";
            $ftp_export_instance_this->ftp_password = "q723iq22";
            $ftp_export_instance_this->ftp_port = 21;
            $ftp_export_instance_this->ftp_directory = "";
            $ftp_export_instance_this->save_uri = "ftp_archive";
            $ftp_export_instance_this->save_filename = "FC_HTMotors_{STAMP_DASH_T}";
            $ftp_export_instance_this->save_fileextension = "txt";
            $ftp_export_instance_this->ftp_filename = "qiq_inventory";
            $ftp_export_instance_this->ftp_fileextension = "txt";
            $ftp_export_instance_this->save_callback_view_name = 'ttc_second';
            $ftp_export_instance_this->save_callback_view_display_id = 'views_data_export_1';
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
