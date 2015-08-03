<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|==========================================================
| Initialize Calendar Class
|==========================================================
|
*/
if ( ! class_exists('_Calendar'))
{
	require_once(BASEPATH.'libraries/Calendar'.EXT);
}

if ( ! is_object($CI))
{
	$this->calendar = new _Calendar();
	$this->ci_is_loaded[] = 'calendar';
}
else
{
	$CI->calendar = new _Calendar();
	$CI->ci_is_loaded[] = 'calendar';
}

?>