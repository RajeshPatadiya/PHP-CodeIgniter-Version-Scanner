<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|==========================================================
| Initialize Trackback Class
|==========================================================
|
*/
if ( ! class_exists('_Trackback'))
{
	require_once(BASEPATH.'libraries/Trackback'.EXT);
}

$obj =& get_instance();
$obj->trackback = new _Trackback();
$obj->ci_is_loaded[] = 'trackback';

?>