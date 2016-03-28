<?php

class ftpx_config {

  var $instance_array = array();
  var $server_status = 'DEV';
  var $content_status = 'DEV';
  var $config_success = false;

  var $time_start;
  var $stamp_start;
  var $stamp_end;
  var $log_filename_full;
  var $ftp_execute_key;
  var $response = 'RESPONSE PENDING';// for $watchdog will be 'SSUCCESS' or 'EERROR Count: X; Warning Count: Y;'
  var $save_archive_uri;
  var $prune_email_count;
  var $prune_email_array = array();
  var $prune_alert_email_count;
  var $prune_alert_email_array = array();
  var $watchdog_array = array();

  public function __construct($time_now = 'NNULL') {
    $time_now = $time_now == 'NNULL' ? time() : $time_now;
    /**
     * PREFS
     */

    // $ftp_execute_key = 'YYYYMMDD180000';
    $ftp_execute_key = 'YYYYMMDD100000';
    $client_contact = 'bradlowry+ftp_client@gmail.com';
    $developer_contact = 'brad@qiqgroup.com';
    $save_archive_uri = 'ftp_archive';
    $prune_count = 540;// 3 files a day for ~ 6 months
    $prune_alert_count = 1080;// 3 files a day for ~ 12 months
    $prune_email_array[] = $client_contact;
    $prune_alert_email_array[] = $client_contact;
    $prune_alert_email_array[] = $developer_contact;

    $this->ftp_execute_key = $ftp_execute_key;
    $this->save_archive_uri = $save_archive_uri;
    $this->prune_email_count = $prune_count;
    $this->prune_alert_email_count = $prune_alert_count;
    $this->prune_email_array = $prune_email_array;
    $this->prune_alert_email_array = $prune_alert_email_array;

    // $time_now = time();
    $this->time_start = $time_now;
    $this->stamp_start = date('YmdHis', $time_now);
    $this->stamp_end = date('YmdHis', strtotime('+5 minutes',$time_now));
    $this->log_file_destination = 'public://' . $this->save_archive_uri . '/' . 'ftp_log_file_' . substr($this->stamp_start, 0, 8) . '.txt';
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
  public function log_file_exists()
  {
    $log_file_exists = file_exists($this->log_file_destination);
    // if ($log_file_exists) {
    //   $message = 'ftp will be skipped as log file was found (%destination)';
    // }else{
    //   $message = 'ftp will continue as log file was not found (%destination)';
    // }
    // watchdog($type, $message, $variables = array(), $severity = WATCHDOG_NOTICE, $link = NULL)
    // watchdog('ftp_export', $message, array('%destination' => $this->log_file_destination));
    // $this->response .= $message . '|';
    $variables = array('%destination' => $this->log_file_destination);
    load_watchdog(__FUNCTION__, $log_file_exists, $variables);

    return $log_file_exists;
  }

  public function log_file_create()
  {
    $option_array['STAMP'] = $this->stamp_start;
    $option_array['NOW'] = $this->time_start;
    $data = 'Log File to test FTP set on {DATE_TIME_FULL}';
    $data = ftp_export_smarty_string($data, $option_array);
    $replace = FILE_EXISTS_REPLACE;
    $this->log_file_object = file_save_data($data, $this->log_file_destination, $replace);
    $message = 'ftp log file created (%destination)';
    watchdog('ftp_export', $message, array('%destination' => $this->log_file_destination));
    $this->response .= $message . '|';

  }

  public function execute_ftp($ftp_execute_key_override = NULL)
  {
    if (strlen($ftp_execute_key_override) == 14 && ctype_alnum($ftp_execute_key_override)) {
      $this->ftp_execute_key = $ftp_execute_key_override;
    }
    $do_execute_full = ftp_export_key_evaluate_execution($this->stamp_start, $this->ftp_execute_key);
    $do_execute = substr($do_execute_full, -5);
    if ($do_execute != 'TTRUE') {
      $message = 'SKIPPED: ftp_export_upload()';
      $message .= ' [' . $do_execute_full . ' != ' . 'TTRUE' . ']';
      watchdog('ftp_export', $message);
      drupal_set_message($message);
      $this->response = $message;
      return;
    }
    $message = 'CONTINUE: ftp_export_upload()';
    $message .= ' [' . $do_execute_full . ' == ' . 'TTRUE' . ']';
    watchdog('ftp_export', $message);
    drupal_set_message($message);

    $this->response = '';
    $this->response .= $message . '|';
    $log_file_exists = $this->log_file_exists();
    if ($log_file_exists) {
      return;
    }
    foreach ($this->instance_array as $index => $ftpx_instance) {
      $ftpx_instance->execute_ftp_instance();
      $this->response .= $ftpx_instance->response . '|';
    }
    $this->log_file_create();
    $this->response = str_replace('|', '; ', $this->response);
    $this->response = empty($this->response) ? 'RESPONSE ERROR: ' . __FUNCTION__ : $this->response;
    $message = 'execute_ftp() complete';
    watchdog('ftp_export', $message);
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
  var $ftp_passive_mode = TRUE;
  var $ftp_directory;//aka path, maybe change later and if so do globally
  var $ftp_filename;
  var $ftp_fileextension;
  var $save_filename;
  var $save_fileextension;
  var $save_file_object;
  var $save_archive_url;
  var $save_archive_uri;
  var $save_callback_view_name;
  var $save_callback_view_display_id;
  var $time_start;
  var $stamp_start;
  var $stamp_end;
  var $watchdog_array = array();
  var $response = 'RRESPONSE_PENDING';// for $watchdog will be 'SSUCCESS' or 'EERROR Count: X;

  public function __construct($time_now = 'NNULL')
  {
    $time_now = $time_now == 'NNULL' ? time() : $time_now;
    $this->time_start = $time_now;
    $this->stamp_start = date('YmdGis', $time_now);
    $this->stamp_end = date('YmdGis', strtotime('+1 minute',$time_now));

  }

  public function unpack_watchdog()
  {

  }

  public function execute_ftp_instance()
  {
    $this->generate_file_to_transfer();
    $this->ftp_put_instance();
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
    // $option_array['NOW'] = $this->time_start;
    // $data = 'DEV Content to FTP set on {DATE_TIME_FULL}';
    // $data = ftp_export_smarty_string($data, $option_array);
    $data = $this->gather_data_to_ftp();
    $option_array['NO_SPACE'] = 'UNDERSCORE';
    $this->save_filename = ftp_export_smarty_string($this->save_filename, $option_array);
    $this->ftp_filename = ftp_export_smarty_string($this->ftp_filename, $option_array);
    $destination = 'public://' . $this->save_archive_uri . '/' . $this->save_filename . '.' . $this->save_fileextension;
    $this->save_archive_uri = $destination;

    //file_save_data($data, $destination = NULL, $replace = FILE_EXISTS_RENAME{FILE_EXISTS_REPLACE|FILE_EXISTS_ERROR})
    $replace = FILE_EXISTS_REPLACE;
    $replace_string = $replace == 1 ? 'FILE_EXISTS_REPLACE'  : 'FILE_EXISTS_UNSUPPORTED';
    // $this->save_file_object = $data . '__' . $destination . '__' . $replace_string;
    $this->save_file_object = file_save_data($data, $destination, $replace);
    $this->save_archive_url = 'retun to this if necessary';
    // $this->save_archive_url = file_create_url($this->save_file_object->uri);
    $message = 'ftp instance save file created (%destination)';
    watchdog('ftp_export', $message, array('%destination' => $destination));

    // $this->response = "Result of '" . __FUNCTION__ . "':" . $this->save_filename . '.' . $this->save_fileextension . ' TO: ' . $data;
    // $this->response = "Result of '" . __FUNCTION__ . "':" . $this->ftp_filename . '.';
    // $this->response = __FUNCTION__;
  }

  public function gather_data_to_ftp()
  {
    $view_name = $this->save_callback_view_name;
    $view_display_id = $this->save_callback_view_display_id;

    $data = views_embed_view($view_name, $view_display_id);
    if (empty($data)) {
      $option_array = array();
      $option_array['STAMP'] = $this->stamp_start;
      $option_array['NOW'] = $this->time_start;
      $data = "views_embed_view('ttc_first', 'views_data_export_1') FAILED! Content set within method '" . __FUNCTION__ . "'to FTP set on {DATE_TIME_FULL}";
      $data = ftp_export_smarty_string($data, $option_array);
    }
    return $data;
  }

  public function ftp_put_instance()
  {
    switch ($this->content_status) {
      case 'PROD':
        $this->ftp_put_instance_dev();
        break;
      case 'STAGING':
        $this->ftp_put_instance_dev();
        break;
      case 'DEV':
        $this->ftp_put_instance_dev();
        break;

      default:
        $this->response = "DDEFAULT of SWITCH on Line " . __LINE__ . "of Function: '" . __FUNCTION__ . "'";
        break;
    }
  }

  public function ftp_put_instance_dev()
  {
  // $destination_file = 'DestFileThis' . '.' . 'txt';
  $destination_file = $this->ftp_filename . '.' . $this->ftp_fileextension;
  $source_file = $this->save_archive_uri;
  // $source_file = $this->save_file_object->uri;
  // $message = "Holder For! FTP upload %source_file to %ftp_host as %destination_file";
  // watchdog('ftp_export', $message, array(
  //   '%ftp_host' => $this->ftp_host,
  //   '%source_file' => $source_file,
  //   '%destination_file' => $destination_file)
  // );
  // return;

  $error_message = '';
  $connection_handle = ftp_connect($this->ftp_host);
  // check connection
  if (!$connection_handle) {
      $error_message = "FTP connection failed to connect to $this->ftp_host";
      watchdog('ftp_export', $error_message, array('%ftp_host' => $this->ftp_host), WATCHDOG_ERROR);
      return;
  }
  // login with username and password
  $login_result = ftp_login($connection_handle, $this->ftp_username, $this->ftp_password);

  // check connection
  if (!$login_result) {
      $error_message = "FTP connection to %ftp_host could not resolve credentials for %ftp_username";
      // watchdog($type, $message, $variables = array(), $severity = WATCHDOG_NOTICE{WATCHDOG_ERROR}, $link = NULL)
    watchdog('ftp_export', $error_message, array(
      '%ftp_host' => $this->ftp_host,
      '%ftp_username' => $this->ftp_username), WATCHDOG_ERROR);

      return;
  } else {
      $message = "Connected to %ftp_host for user %ftp_username";
      watchdog('ftp_export', $message, array('%ftp_host' => $this->ftp_host, '%ftp_username' => $this->ftp_username));
  }

  if (!empty($this->ftp_directory)) {
    $chdir_result = ftp_chdir ( $connection_handle , $this->ftp_directory );
    // check chdir
    if (!$chdir_result) {
        $error_message = "FTP connection  $this->ftp_host could not chdir to $this->ftp_directory";
        ftp_close($connection_handle);
        return;
    }else {
      $message = "FTP connection  %ftp_host chdir to %ftp_directory";
      watchdog('ftp_export', $message, array('%ftp_host' => $this->ftp_host, '%ftp_directory' => $this->ftp_directory));
    }
  }

  if (empty($error_message)) {
    // upload the file
    $destination_file = $this->ftp_filename . '.' . $this->ftp_fileextension;
    $source_file = $this->save_file_object->uri;
    if ($this->ftp_passive_mode !== FALSE) {
      ftp_pasv($connection_handle, TRUE);
    }
    $upload = ftp_put($connection_handle, $destination_file, $source_file, FTP_BINARY);

    // check upload status
    if (!$upload) {
        $error_message = "Failed! FTP upload %source_file to %ftp_host as %destination_file";
        watchdog('ftp_export', $error_message, array(
          '%ftp_host' => $this->ftp_host,
          '%source_file' => $source_file,
          '%destination_file' => $destination_file),
        WATCHDOG_ERROR);
    } else {
        $message = "Succeeded! FTP upload %source_file to %ftp_host as %destination_file";
        watchdog('ftp_export', $message, array(
          '%ftp_host' => $this->ftp_host,
          '%source_file' => $source_file,
          '%destination_file' => $destination_file)
        );
    }
  }

  // close the FTP stream
  ftp_close($connection_handle);
  return;
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
    $now = @$option_array['NOW'] + 0 > 0 ? $option_array['NOW'] + 0 : strtotime('now');
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

  $string_returned = @$option_array['NO_TRIM'] === TRUE ? $string_returned : trim($string_returned);
  $string_returned = @$option_array['NO_SPACE'] == 'UNDERSCORE' ? str_replace(' ', '_', $string_returned) : $string_returned;
  #\_ add camelCase, CamelCase, strtolower, strtoupper, ucwords as needed/desired

  return $string_returned;
}

function ftp_export_key_evaluate_execution($stamp, $execution_key) {
$key_array = ftp_stamp_parse('YYYYMMDDHHIISS');
$stamp_array = ftp_stamp_parse($stamp);
$execution_key_array = ftp_stamp_parse($execution_key);
$response = $stamp . '|' . $execution_key . '|';
if (count($stamp_array) + count($execution_key_array) != 12) {
  $response .= count($stamp_array). ' + ' . count($execution_key_array) . '|';
  $response .= 'EERROR|';
  $response .= 'FFALSE';
  // $response = 'FFALSE';
  return $response;
}

$execution_pending = 'EQ';
  foreach ($stamp_array as $key => $value) {
    $lask_key = $key;//DEV $response only
    $last_compare = $value . ':' . $execution_key_array[$key];//DEV $response only
    $execution_value = $execution_key_array[$key] + 0;
    $stamp_value = $value + 0;
    if ($key == $execution_key_array[$key]) {
      $execution_pending = 'EQ';
    }elseif(!ctype_digit($execution_key_array[$key])) {
      $execution_pending = 'NON_DIGIT';
      break;
    }elseif($stamp_value == $execution_value) {
      $execution_pending = 'EQ';
    }elseif($stamp_value > $execution_value) {
      $execution_pending = 'GT';
      break;
    }else{
      $execution_pending = 'LT';
      break;
    }
  }

  $response .= $lask_key . '=' . $last_compare . '|' . $execution_pending . '|';
  if (in_array($execution_pending, array('EQ', 'GT'))) {
    $response .= 'TTRUE';
    // $response = 'TTRUE';
    return $response;
  }
  $response .= 'FFALSE';
  // $response = 'FFALSE';
  return $response;
}
function ftp_stamp_parse($stamp) {
  $stamp = trim($stamp);
  $stamp_array = array();
  if (strlen($stamp) != 14 || !ctype_alnum($stamp)) {
    return $stamp_array;
  }
  $stamp_array['YYYY'] = substr($stamp, 0, 4);
  $stamp_array['MM'] = substr($stamp, 4, 2);
  $stamp_array['DD'] = substr($stamp, 6, 2);
  $stamp_array['HH'] = substr($stamp, 8, 2);
  $stamp_array['II'] = substr($stamp, 10, 2);
  $stamp_array['SS'] = substr($stamp, 12, 2);
  return $stamp_array;
}

function load_watchdog($calling_function = 'ZXZ', $status = 'UNKNOWN', $variables = array()) {
  $type = 'ftp_export';
  $status = $status === TRUE ? 'AFFIRMATIVE' : $status;
  $status = $status == 'AFFIRMATIVE' ? $status : 'NEGATIVE';
  $severity = WATCHDOG_NOTICE;
      /**
     *
     * WATCHDOG_EMERGENCY: Emergency, system is unusable.
     * WATCHDOG_ALERT: Alert, action must be taken immediately.
     * WATCHDOG_CRITICAL: Critical conditions.
     * WATCHDOG_ERROR: Error conditions.
     * WATCHDOG_WARNING: Warning conditions.
     * WATCHDOG_NOTICE: (default) Normal but significant conditions.
     * WATCHDOG_INFO: Informational messages.
     * WATCHDOG_DEBUG: Debug-level messages
     */
  $link = NULL;

  switch ($calling_function) {
    case 'log_file_exists':
      if ($status == 'AFFIRMATIVE') {
        $message = 'SKIP: ftp will be skipped as log file was found (%destination)';
      }else{
        $message = 'CONTINUE: ftp will continue as log file was not found (%destination)';
      }
      break;

    default:
      $severity = WATCHDOG_ERROR;
      $message = 'UNSUPPORTED FUNCTION: ' . $calling_function;
      break;
  }
  if ($severity !== 'WATCHDOG_SKIP') {
    watchdog($type, $message, $variables, $severity, $link);
  }
}
