<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|==========================================================
| Initialize Session Class
|==========================================================
|
*/
if ( ! class_exists('_Session'))
{
	require_once(BASEPATH.'libraries/Session'.EXT);
}

$obj =& get_instance();
$obj->session = new _Session();
$obj->ci_is_loaded[] = 'session';

?>