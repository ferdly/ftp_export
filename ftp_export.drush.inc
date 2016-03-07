<?php

/**
 * Implements hook_drush_command().
 */
function ftp_export_drush_command() {
  $items['ftp-export'] = array(
    'aliases' => array('ftpx'),
    'options' => array(
      'dep_custom' => 'an option',
      'dep_drupal' => 'an option',
      ),
    'arguments' => array(
      'custom' => 'an argument',
      'drupal' => 'an argument',
      ),
  );

  /**
   * Overload for Documentation and 'drush help'
   */

  $items['ftp-export']['description'] = 'A Descr of FTP-Export Drush Command';
  $items['ftp-export']['options']['custom'] = 'A Descr of Option custom';
  $items['ftp-export']['examples']['drush ftpx --config'] = 'PENDING Prints the statement once with the error flag.';
  // $items['ftp-export']['arguments']['config'] => "PRIOR EXAMPLE Entity 'Bundle' (for node read 'Content Type'). If not supplied, list of AEL enabled configs are generated to select from.";

  return $items;
}

/**
 * Callback for the drush-demo-command command
 */
function drush_ftp_export($config = FALSE) {

  // Check for existence of argument
    $arguments = _config_arguments(); //use custom function to encapsulate
    if (!$config ) {
        $config = drush_choice($arguments, dt('For which ftp?'));
    }

    if (!$config) {
      drush_print("Buh-Bye Custom 'Cancel' logic");
      return;
    }

    $ctype = '';
    if (ctype_alpha($config)) {
      $ctype = 'ctype IS alpha';
    }
    elseif (ctype_digit($config)) {
      $ctype = 'ctype IS digit';
    }elseif (1 == 2) {
      $to_print = "YIKES!: " . print_r($config, TRUE);
      drush_print($to_print);
      return;
    }

  // Check for correct argument
  $to_print = "NOT YET SET Line:" . __LINE__ . "\r\n";
  $correct_args = array_keys($arguments);
  if (!in_array($config, $correct_args)) {
    $string = _config_arguments('string');
    return drush_set_error(dt('"@type" is not a valid config . ',
      array('@type' => $config )) . $string);
      $to_print = "Buh-Bye Custom Line:" . __LINE__ . "\r\n";
  }
  switch ($config) {
    case 'custom':
      require_once 'src/ftp_export_config.inc';
      $to_print = print_r($ftp_export_config_these, TRUE);
      break;
    case 'drupal':
      // require_once 'src/ftp_export_config.inc';
      // $to_print = print_r($ftp_export_config_these, TRUE);
      $to_print = "Option {$config}: Is not working at this time";
      break;

    default:
      $to_print .= "print_r \r\n" . print_r($config, TRUE);
      // $to_print = "Option {$config}: Is not supported and is not working at this time";
      break;
  }

  if (!empty($ctype)) {
    drush_print($ctype);
  }
  drush_print($config);
  drush_print($to_print);
  return;
  // _drush_execute_ftpx($config , $action, $additional_option_array);
  // _drush_execute_ftpx($config , $action);
} //END function drush_ftp_export($config = FALSE)

function _config_arguments($return_type = 'array') {
  $arguments = array(
    'custom' => 'arg: custom',
    'drupal' => 'arg: drupal',
    );
  if ($return_type == 'array') {
    return $arguments;
  }
  if ($return_type == 'string') {
    return 'String for Error Code';
  }
}
