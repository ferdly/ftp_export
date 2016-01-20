<?php

class ftpx_config {

  var $instance_array = array();
  var $server_status = 'DEV';
  var $content_status = 'DEV';
  var $config_success = false;
  public function __construct(){
    return;
  }
  public function status_normalize()
  {
    $status = $this->server_status;
    $supported_status_array = array('EERROR','DEV','STAGING','PROD',);
    $status = $this->status;
    $status = $status == 'DEVELOPMENT'?'DEV':$status;
    $status = $status == 'STAGE'?'STAGING':$status;
    $status = $status == 'STAGE'?'STAGING':$status;
    $status = $status == 'PRODUCTION'?'PROD':$status;
    $status = in_array($status, $supported_status_array)?$status:'EERROR';
    $this->server_status = $status;
    $status = $this->content_status;
    $supported_status_array = array('EERROR','DEV','STAGING','PROD',);
    $status = $this->status;
    $status = $status == 'DEVELOPMENT'?'DEV':$status;
    $status = $status == 'STAGE'?'STAGING':$status;
    $status = $status == 'STAGE'?'STAGING':$status;
    $status = $status == 'PRODUCTION'?'PROD':$status;
    $status = in_array($status, $supported_status_array)?$status:'EERROR';
    $this->content_status = $status;
  }

} //END class MD5_ of ftp_export_config in lieu of Name Spacing

class ftpx_instance {

  var $server_status = 'DEV';//or 'STAGING'/'TEST' or 'PROD'/'LIVE' is overloaded by parent
  var $content_status = 'DEV';//or 'STAGING'/'TEST' or 'PROD'/'LIVE' is overloaded by parent
  var $ftp_commonname;
  var $ftp_host;
  var $ftp_username;
  var $ftp_password;
  var $ftp_port;
  var $ftp_directory;//aka path, maybe change later and if so do globally
  var $ftp_filename;
  var $ftp_fileextension;
} //END class MD5_ of ftp_export_instance in lieu of Name Spacing
