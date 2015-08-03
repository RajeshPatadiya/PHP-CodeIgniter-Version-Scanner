<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|==========================================================
| Initialize XML-RPC Server Class
|==========================================================
*/
$config = array();
if (file_exists(APPPATH.'config/xmlrpcs'.EXT))
{
	include_once(APPPATH.'config/xmlrpcs'.EXT);
}

if ( ! class_exists('_XML_RPC_Server'))
{			
	require_once(BASEPATH.'libraries/Xmlrpc'.EXT);
	require_once(BASEPATH.'libraries/Xmlrpcs'.EXT);
}

$obj =& get_instance();
$obj->xmlrpc  = new _XML_RPC();
$obj->xmlrpcs = new _XML_RPC_Server($config);
$obj->ci_is_loaded[] = 'xmlrpc';
$obj->ci_is_loaded[] = 'xmlrpcs';

?>