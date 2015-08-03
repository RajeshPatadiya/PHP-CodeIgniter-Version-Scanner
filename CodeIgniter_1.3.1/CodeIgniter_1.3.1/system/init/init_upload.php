<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loads and instantiates file uploading class
 *
 * @access	private called by the app controller
 */	

if ( ! class_exists('CI_Upload'))
{
	$config = array();
	if (file_exists(APPPATH.'config/upload'.EXT))
	{
		include_once(APPPATH.'config/upload'.EXT);
	}
	
	require_once(BASEPATH.'libraries/Upload'.EXT);
}

$obj =& get_instance();
$obj->upload = new CI_Upload($config);
$obj->ci_is_loaded[] = 'upload';

?>