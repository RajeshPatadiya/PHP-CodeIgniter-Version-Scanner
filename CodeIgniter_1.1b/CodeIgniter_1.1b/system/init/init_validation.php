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

if ( ! is_object($CI))
{
	$this->validation = new _Validation($this);
	$this->ci_is_loaded[] = 'validation';	
}
else
{
	$CI->validation = new _Validation($this);
	$CI->ci_is_loaded[] = 'validation';	
}

?>