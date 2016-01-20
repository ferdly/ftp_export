<?php

/**
 * Implements hook_drush_command().
 */
function ftp_export_drush_command() {

  $items['ftp-export'] = array(
    'description' => "Description ftp_export: \r\nComposes SQL Code to apply Auto-Entry Label Titles to current titles. Useful when there is lots of data already entered before AEL implemented, or when the configuration of EAL is tweaked.",
    'aliases' => array('ftpx'), //don't want to collide into others
    'arguments' => array(
      'area' => "Entity 'Bundle' (for node read 'Content Type'). If not supplied, list of AEL enabled areas are generated to select from.",
    ),
    'options' => array(
      'hello' => 'The number of items to execute for the command (smart defaults as per action, and validated).',
      'lorem' => 'Applies an Offset of limit times lorem. The expectation is that the user leaves limit constant and increments the page, although there is no way to enforce this use (validated).',
      'echo_options' => 'A Comma Separated Value (csv) list of entity_id values to execute for the command (validated).',
      'nodeplode' => 'In conjunction with limit or with echo_options or on its own. Randomizes the order of the lines of code Validated.',
      'vargetter' => 'Smart Defaults, but if you want to overload use yes or no',
      'action' => 'compose, preview, or mask with compose being the default. This will affect the default of limit.',
      'dev' => "Allow for skipping over the 'clean' output in favor of 'dev' output as the code progresses. This may be removed or made more useful developers not coding this module.",
    ),
    'examples' => array(
      'drush ftpx --action=mask' => 'PENDING Prints the statement once with the error flag.',
      'drush ftp_export --limit=10' => 'PENDING Prints the statement 10 times with the success flag.',
      'drush ftpx --echo_options=10,27,125' => 'PENDING Prints the statement 10 times with the success flag.',
    ),
  );

  return $items;
}

/**
 * Callback for the drush-demo-command command
 */
function drush_ftp_export($area = FALSE) {

  // Check for existence of argument
    $options = _area_options(); //use custom function to encapsulate
    if (!$area) {
        $area = drush_choice($options, dt('For which Bundle would you like to generate \'Auto Enity-Label Back-Log\' SQL Code?'));
    }

  // Check for correct argument
  $correct_args = array_keys($options);
  if (!in_array($area, $correct_args)) {
    $string = _area_options('string');
    /**
     * @circleback = how to escape quotes within dt?
     * it's really wrong to circumvent dt() like this
     */
    return drush_set_error(dt('"@type" is not a valid area. ',
      array('@type' => $area)) . $string);
  }

  // Option
  // loop through array of supported options
  /**
   * @circleback can this be gathered from ftp_export_drush_command()
   */
  // $area = $area;//KLUDGE
  $supported_option_array = array(
    'action',
    'who',
    'vocab',
    'alt_echo',
    'rand',
    'indent',
    'dev',
    );
  $supported_boolean_options_array = array(
    'rand',
    'indent',
    'dev',
    );
  foreach ($supported_option_array as $index => $option) {
    $option = strtolower($option);//maybe enforce case sensitivity
    $$option = drush_get_option($option, 777);
    if ($$option == 777) {
      if (in_array($option, $supported_boolean_options_array)) {
        $$option = 1;
      }
      $$option = $option != 'action'?$$option:NULL;
    }
    if ($$option != 777) {
      $key = $option == 'action'?'':'option_';
      $key .= str_replace('-', '_', $option);
      $additional_option_array[$key] = $$option;
    }
  }
  /**
   * @todo validate, unpack here? I think in object method
   * @circleback remove application of empty $$option to $additional_option_array
   */
  // drush_print($area);
  // return;
  _drush_execute_ftpx($area, $action, $additional_option_array);
  // _drush_execute_ftpx($area, $action);
} //END function drush_ftp_export($area = FALSE)

