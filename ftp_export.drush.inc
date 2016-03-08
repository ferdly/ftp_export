<?php

/**
 * Implements hook_drush_command().
 */
function ftp_export_drush_command() {
  $items['ftp-export'] = array(
    'aliases' => array('ftpx'),
    'options' => array(
      'dev' => 'an option',
      'step' => 'an option',
      ),
    'arguments' => array(
      'custom' => 'an argument',
      'combo' => 'an argument',
      'core' => 'an argument',
      ),
  );

  /**
   * Extend/Overload for Documentation and 'drush help'
   */

  $items['ftp-export']['description'] = 'A Descr of FTP-Export Drush Command';
  $items['ftp-export']['examples']['drush ftpx --dev'] = 'Will prompt for CCC Argument, then run and print_r() any contextual data.';
  $items['ftp-export']['options']['dev'] = 'print_r() any contextual data';
  $items['ftp-export']['options']['step'] = 'print_r() any contextual data as coded at the STEP_NAME (--step=STEP_NAME)';
  $items['ftp-export']['arguments']['custom'] = "Switch to the Custom Expression of the FTP mechanism";
  $items['ftp-export']['arguments']['core'] = "Switch to the Core Expression of the FTP mechanism (via includes/filetranser)";
  $items['ftp-export']['arguments']['combo'] = "Switch to the Combo Expression of the FTP mechanism (a 'custom' call of the 'core' mechanism)";

  return $items;
}

/**
 * Callback for the drush-demo-command command
 */
function drush_ftp_export($ccc = FALSE, $arguments = FALSE) {

  // Check for existence of argument
    $arguments = _config_arguments(); //use custom function to encapsulate
    if (!$ccc ) {
        $ccc = drush_choice($arguments, dt('For which ftp?'));
    }

    if (!$ccc) {
      drush_print("Buh-Bye Custom 'Cancel' logic");
      return;
    }

    $ctype = '';
    if (ctype_alpha($ccc)) {
      $ctype = 'ctype IS alpha';
    }
    elseif (ctype_digit($ccc)) {
      $ctype = 'ctype IS digit';
    }elseif (1 == 2) {
      $to_print = "YIKES!: " . print_r($ccc, TRUE);
      drush_print($to_print);
      return;
    }

  // Check for correct argument
  $to_print = "NOT YET SET Line:" . __LINE__ . "\r\n";
  $correct_args = array_keys($arguments);
  if (1 == 2 && !in_array($ccc, $correct_args)) {
    $string = _config_arguments('string');
    return drush_set_error(dt('"@type" is not a valid config . ',
      array('@type' => $ccc )) . $string);
      $to_print = "Buh-Bye Custom Line:" . __LINE__ . "\r\n";
  }
  switch ($ccc) {
    case 'custom':
      $to_print = '';
      $to_print .= " Switch: {$ccc};";
      $to_print .= ftp_export_drush__custom ($ccc);
      $to_print .= " Argument: '{$ccc}'; Response: Is not working at this time;";
      break;
    case 'combo':
      $to_print = "Argument '{$ccc}': Is not working at this time";
      break;
    case 'core':
      $to_print = "Argument '{$ccc}': Is not working at this time";
      break;

    default:
      $to_print .= "print_r \r\n" . print_r($ccc, TRUE);
      // $to_print = "Argument {$ccc}: Is not supported and is not working at this time";
      break;
  }
  $to_print = trim($to_print);
  if (!empty($ctype)) {
    drush_print($ctype);
  }
  drush_print($ccc);
  drush_print($to_print);
  return;
  // _drush_execute_ftpx($ccc , $action, $additional_option_array);
  // _drush_execute_ftpx($ccc , $action);
} //END function drush_ftp_export($ccc = FALSE)

function _config_arguments($return_type = 'array') {
  /**
   * @circleback - why can't I pass in/acces $items[drush-command][arguments]?
   */
  $arguments = array();
  $arguments['custom'] = "Switch to the Custom Expression of the FTP mechanism";
  $arguments['core'] = "Switch to the Core Expression of the FTP mechanism (via includes/filetranser)";
  $arguments['combo'] = "Switch to the Combo Expression of the FTP mechanism (a 'custom' call of the 'core' mechanism)";

  if ($return_type == 'array') {
    return $arguments;
  }
  if ($return_type == 'string') {
    return 'String for Error Code';
  }
}
