<?php

require_once 'ftp_export_object.php';

$ftp_export_config_these = new ftpx_config();
$ftp_export_config_these->server_status = 'DEV';//or 'STAGING' or 'PROD'
$ftp_export_config_these->content_status = 'DEV';//or 'STAGING' or 'PROD'
$ftp_export_config_these->status_normalize();


$ftp_export_instance_this =  new ftpx_instance();
$ftp_export_instance_this->ftp_commonname = "Carsoup";
$ftp_export_instance_this->ftp_host = "upload.carsoup.com";
$ftp_export_instance_this->ftp_username = "QIQgroup";
$ftp_export_instance_this->ftp_password = "S0m3P@ssw0rd";
$ftp_export_instance_this->ftp_port = 21;
$ftp_export_instance_this->ftp_directory = "";
$ftp_export_instance_this->ftp_filename = "htmotors";
$ftp_export_instance_this->ftp_fileextension = "csv";
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
$ftp_export_instance_this->ftp_commonname = "FindCars";
$ftp_export_instance_this->ftp_host = "FindCars";
$ftp_export_instance_this->ftp_username = "QIQsomething";
$ftp_export_instance_this->ftp_password = "S0m3P@ssw0rd";
$ftp_export_instance_this->ftp_port = 21;
$ftp_export_instance_this->ftp_directory = "vehicles";
$ftp_export_instance_this->ftp_filename = "htmotors";
$ftp_export_instance_this->ftp_fileextension = "csv";
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