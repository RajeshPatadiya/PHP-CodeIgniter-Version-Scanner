<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|==========================================================
| Initialize Pagination Class
|==========================================================
|
| It looks to see if a config file exists so that
| parameters can be hard coded
|
*/
if ( ! class_exists('_Pagination'))
{
	$config = array();
	if (file_exists(APPPATH.'config/pagination'.EXT))
	{
		include_once(APPPATH.'config/pagination'.EXT);
	}
	
	require_once(BASEPATH.'libraries/Pagination'.EXT);		
}

if ( ! is_object($CI))
{
	$this->pagination = new _Pagination($config);
	$this->ci_is_loaded[] = 'pagination';
}
else
{
	$CI->pagination = new _Pagination($config);
	$CI->ci_is_loaded[] = 'pagination';
}

?>