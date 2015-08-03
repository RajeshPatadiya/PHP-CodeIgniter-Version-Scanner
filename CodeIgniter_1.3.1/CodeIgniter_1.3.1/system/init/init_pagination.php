<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates pagination class
 *
 * @access	private called by the app controller
 */	

if ( ! class_exists('CI_Pagination'))
{
	$config = array();
	if (file_exists(APPPATH.'config/pagination'.EXT))
	{
		include_once(APPPATH.'config/pagination'.EXT);
	}
	
	require_once(BASEPATH.'libraries/Pagination'.EXT);		
}

$obj =& get_instance();
$obj->pagination = new CI_Pagination($config);
$obj->ci_is_loaded[] = 'pagination';

?>