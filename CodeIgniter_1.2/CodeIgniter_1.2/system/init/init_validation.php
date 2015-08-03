<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|==========================================================
| Initialize Validation Class
|==========================================================
*/
if ( ! class_exists('_Validation'))
{
	require_once(BASEPATH.'libraries/Validation'.EXT);
}

$obj =& get_instance();
$obj->validation = new _Validation();
$obj->ci_is_loaded[] = 'validation';	

?>