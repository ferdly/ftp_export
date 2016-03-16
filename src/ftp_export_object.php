<?php

class ftpx_config {

  var $instance_array = array();
  var $server_status = 'DEV';
  var $content_status = 'DEV';
  var $config_success = false;

  var $time_start;
  var $stamp_start;
  var $stamp_end;
  var $response = 'RESPONSE PENDING';// for $watchdog will be 'SSUCCESS' or 'EERROR Count: X; Warning Count: Y;'
  var $save_archive_uri;
  var $prune_email_count;
  var $prune_email_array = array();
  var $prune_alert_email_count;
  var $prune_alert_email_array = array();

  public function __construct($time_now = 'NNULL') {
    $time_now = $time_now == 'NNULL' ? time() : $time_now;
    /**
     * PREFS
     */

    $client_contact = 'bradlowry+ftp_client@gmail.com';
    $developer_contact = 'brad@qiqgroup.com';
    $save_archive_uri = 'ftp_archive';
    $prune_count = 360;// 2 files a day for ~ 6 months
    $prune_alert_count = 720;// 2 files a day for ~ 12 months
    $prune_email_array[] = $client_contact;
    $prune_alert_email_array[] = $client_contact;
    $prune_alert_email_array[] = $developer_contact;

    $this->save_archive_uri = $save_archive_uri;
    $this->prune_email_count = $prune_count;
    $this->prune_alert_email_count = $prune_alert_count;
    $this->prune_email_array = $prune_email_array;
    $this->prune_alert_email_array = $prune_alert_email_array;

    // $time_now = time();
    $this->time_start = $time_now;
    $this->stamp_start = date('YmdGis', $time_now);
    $this->stamp_end = date('YmdGis', strtotime('+5 minutes',$time_now));
    return;
  }
  public function status_normalize()
  {
    $supported_status_array = array(
      'EERROR' => array('EERROR'),
      'DEV' => array(
        'DEV',
        'DEVEL',
        'DEVELOPMENT',
        ),
      'STAGING' => array(
        'STAGING',
        'STAGE',
        'TEST',
        'TESTING',
        ),
      'PROD' => array(
        'PROD',
        'PRODUCTION',
        'LIVE',
        ),
      );

    $status = $this->server_status;
    foreach ($supported_status_array as $final_status => $option_array ) {
      foreach ($option_array as $key => $option) {
        if ($status == $option) {
          $status = $final_status;
        }
      }
    }
    $this->server_status = $status;

    $status = $this->content_status;
    foreach ($supported_status_array as $final_status => $option_array ) {
      foreach ($option_array as $key => $option) {
        if ($status == $option) {
          $status = $final_status;
        }
      }
    }
    $this->content_status = $status;
  }

  public function execute_ftp()
  {
    $this->response = '';
    foreach ($this->instance_array as $index => $ftpx_instance) {
      $ftpx_instance->generate_file_to_transfer();
      $this->response .= $ftpx_instance->response . '|';
    }
    $this->response = str_replace('|', '; ', $this->response);
    $this->response = empty($this->response) ? 'RESPONSE ERROR: ' . __FUNCTION__ : $this->response;
  }

} //END class MD5_ of ftp_export_config in lieu of Name Spacing

class ftpx_instance {

  var $server_status = 'DEV';//or 'STAGING'/'TEST' or 'PROD'/'LIVE' is overloaded by parent
  var $content_status = 'DEV';//or 'STAGING'/'TEST' or 'PROD'/'LIVE' is overloaded by parent
  var $ftp_commonname;
  var $ftp_combo_key_array = array();
  var $ftp_to_from;
  var $ftp_host;
  var $ftp_username;
  var $ftp_password;
  var $ftp_port;
  var $ftp_directory;//aka path, maybe change later and if so do globally
  var $ftp_filename;
  var $ftp_fileextension;
  var $save_filename;
  var $save_fileextension;
  var $save_file_object;
  var $save_archive_url;
  var $save_archive_uri;
  var $file_generation_callback;
  var $time_start;
  var $stamp_start;
  var $stamp_end;
  var $response = 'RRESPONSE_PENDING';// for $watchdog will be 'SSUCCESS' or 'EERROR Count: X;

  public function __construct($time_now = 'NNULL') {
    $time_now = $time_now == 'NNULL' ? time() : $time_now;
    $this->time_start = $time_now;
    $this->stamp_start = date('YmdGis', $time_now);
    $this->stamp_end = date('YmdGis', strtotime('+1 minute',$time_now));

  }

  public function generate_file_to_transfer()
  {
    switch ($this->content_status) {
      case 'PROD':
        $this->generate_file_to_transfer_dev();
        break;
      case 'STAGING':
        $this->generate_file_to_transfer_dev();
        break;
      case 'DEV':
        $this->generate_file_to_transfer_dev();
        break;

      default:
        $this->response = "DDEFAULT of SWITCH on Line " . __LINE__ . "of Function: '" . __FUNCTION__ . "'";
        break;
    }
  }

  public function generate_file_to_transfer_dev()
  {
    $option_array = array();
    $option_array['STAMP'] = $this->stamp_start;
    $option_array['NOW'] = $this->time_start;
    $data = 'DEV Content to FTP set on {DATE_TIME_FULL}';
    $data = ftp_export_smarty_string($data, $option_array);
    $option_array['NO_SPACE'] = 'UNDERSCORE';
    $this->save_filename = ftp_export_smarty_string($this->save_filename, $option_array);
    $destination = 'public://' . $this->save_archive_uri . '/' . $this->save_filename . '.' . $this->save_fileextension;
    $this->save_archive_uri = $destination;
    $this->save_archive_url = file_create_url($this->save_file_object->uri);

    //file_save_data($data, $destination = NULL, $replace = FILE_EXISTS_RENAME{FILE_EXISTS_REPLACE|FILE_EXISTS_ERROR})
    $replace = FILE_EXISTS_REPLACE;
    $replace_string = $replace == 1 ? 'FILE_EXISTS_REPLACE'  : 'FILE_EXISTS_UNSUPPORTED';
    // $this->save_file_object = $data . '__' . $destination . '__' . $replace_string;
    $this->save_file_object = file_save_data($data, $destination, $replace);
    // $this->save_file_object = file_unmanaged_save_data($data, $destination, $replace);

    $this->response = "Result of '" . __FUNCTION__ . "':" . $this->save_filename . '.' . $this->save_fileextension . ' TO: ' . $data;
    // $this->response = "Result of '" . __FUNCTION__ . "':" . $this->ftp_filename . '.';
    // $this->response = __FUNCTION__;
  }

  public function ftp_watchdog()
  {
   // $stamp = date('YmdGis');
   // watchdog('commerce_ordr_ftp_export', 'Uploaded order xml (%orderid)', array('%orderid' => $order->order_id), WATCHDOG_NOTICE);
   $this->response = 'Test of WatchDog for ' . $this->ftp_commonname;
  }

} //END class ftpx_instance

function ftp_export_smarty_string($string, $option_array = array()){
  $string_returned = $string;

  $smarty_key = '{STAMP}';
  if (strpos($string, $smarty_key) !== FALSE) {
    $smarty_value = strlen($option_array['STAMP']) > 0 ? $option_array['STAMP'] : date('YmdGis');
    $string_returned = str_replace($smarty_key, $smarty_value, $string_returned);
  }
  $smarty_key = '{STAMP_DASH_T}';
  if (strpos($string, $smarty_key) !== FALSE) {
    $now = $option_array['NOW'] + 0 > 0 ? $option_array['NOW'] + 0 : strtotime('now');
    $smarty_value = date('Y-m-d\TG-i-s', $now);
    $string_returned = str_replace($smarty_key, $smarty_value, $string_returned);
  }
  $smarty_key = '{DATE_TIME_FULL}';
  if (strpos($string, $smarty_key) !== FALSE) {
    $now = $option_array['NOW'] + 0 > 0 ? $option_array['NOW'] + 0 : strtotime('now');
    $smarty_value = date('D F j, Y \a\t g:ia', $now);
    $string_returned = str_replace($smarty_key, $smarty_value, $string_returned);
  }
  #\_ add blocks for {OTHER}s as needed/desired

  $string_returned = $option_array['NO_TRIM'] === TRUE ? $string_returned : trim($string_returned);
  $string_returned = $option_array['NO_SPACE'] == 'UNDERSCORE' ? str_replace(' ', '_', $string_returned) : $string_returned;
  #\_ add camelCase, CamelCase, strtolower, strtoupper, ucwords as needed/desired

  return $string_returned;
}

