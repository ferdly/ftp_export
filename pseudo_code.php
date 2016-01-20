Methods:
echo credentials
\_ stipulate test filename, extension and content overload if desired
ftp_do($instance_param = 0) {
    $instance_array = $this->validate_or_map_instance_array($instance_param)
}

validate_or_map_instance_array($instance_param){
    $instance_which = 'UNKNOWN';
    $instance_which = $instance_param === 0?'ALL':$instance_which;
    $instance_which = is_array($instance_param)?'ARRAY':$instance_which;
    $instance_which = is_int($instance_param)?'INTEGER':$instance_which;

    SW
    if $instance isInt then make array and ConfirValidArray
    if $instance isArray then ConfirmValidArray
    ELSE throw Error
}

<?php
ftp_do($instance_param = 0) {
    $instance_key_array = $this->validate_or_map_instance_array($instance_param)
}

validate_or_map_instance_array($instance_param){
    $instance_which = 'UNKNOWN';
    $instance_which = $instance_param === 0?'ALL':$instance_which;
    $instance_which = is_array($instance_param)?'ARRAY':$instance_which;
    $instance_which = is_int($instance_param)?'INTEGER':$instance_which;
    $current_instance_key_array = array_keys($this->instance_array);

    switch ($instance_which) {
        case 'ALL':
            $instance_key_array = $current_instance_key_array;
            break;
        case 'ARRAY':
            $do = 'NOTHING YET, see below';
            break;
        case 'INTEGER':
            $instance_key_array[] = $instance_param;
            break;

        default:
            $do 'NOTHING YET, catch UNKNOWN below';
            break;
    }

    $i = 0;
    $error_i_array = array();
    foreach ($instance_key_array as $index => $key) {
        if ($index !== $i) {
            $error_i_array[$i] = "key sequence $index";
        }
        if (!is_int($key)) {
            $error_i_array[$i] = "instance key not integer $key";
        }
        if (!in_array($key, $current_instance_key_array)) {
            $error_i_array[$i] = "instance key not integer $key";
        }
        $i++;
    }
    if (count($error_i_array) === 0) {
        return $instance_key_array;
    }else{
        return 'EEROR'; //throw error would be better
    }
}
