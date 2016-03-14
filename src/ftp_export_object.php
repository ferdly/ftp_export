<?php

class ftpx_config {

  var $instance_array = array();
  var $server_status = 'DEV';
  var $content_status = 'DEV';
  var $config_success = false;
  var $archive_uri;
  var $prune_email_count;
  var $prune_email_array = array();
  var $prune_alert_email_count;
  var $prune_alert_email_array = array();

  public function __construct(){
    /**
     * PREFS
     */
    $client_contact = 'bradlowry+ftp_client@gmail.com';
    $developer_contact = 'brad@qiqgroup.com';
    $archive_uri = 'ftp_exportovernight/';
    $prune_count = 360;// 2 files a day for ~ 6 months
    $prune_alert_count = 720;// 2 files a day for ~ 12 months
    $prune_email_array[] = $client_contact;
    $prune_alert_email_array[] = $client_contact;
    $prune_alert_email_array[] = $developer_contact;

    $this->archive_uri = $archive_uri;
    $this->prune_email_count = $prune_count;
    $this->prune_alert_email_count = $prune_alert_count;
    $this->prune_email_array = $prune_email_array;
    $this->prune_alert_email_array = $prune_alert_email_array;
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
} //END class MD5_ of ftp_export_instance in lieu of Name Spacing
