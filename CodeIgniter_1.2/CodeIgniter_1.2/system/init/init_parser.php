<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|==========================================================
| Initialize Variable Parser Class
|==========================================================
|
*/
if ( ! class_exists('_Parser'))
{
	require_once(BASEPATH.'libraries/Parser'.EXT);
}

$obj =& get_instance();
$obj->parser = new _Parser();
$obj->ci_is_loaded[] = 'parser';

?>