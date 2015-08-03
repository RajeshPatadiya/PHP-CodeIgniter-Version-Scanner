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

$obj =& get_instance();

$obj->calendar = new _Calendar();
$obj->ci_is_loaded[] = 'calendar';

?>