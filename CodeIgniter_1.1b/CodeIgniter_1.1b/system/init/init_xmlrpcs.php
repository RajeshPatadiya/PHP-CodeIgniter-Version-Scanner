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

if ( ! is_object($CI))
{
	$this->xmlrpc  = new _XML_RPC();
	$this->xmlrpcs = new _XML_RPC_Server($config, $this);
	$this->ci_is_loaded[] = 'xmlrpc';
	$this->ci_is_loaded[] = 'xmlrpcs';
}
else
{
	$CI->xmlrpc  = new _XML_RPC();
	$CI->xmlrpcs = new _XML_RPC_Server($config, $CI);
	$CI->ci_is_loaded[] = 'xmlrpc';
	$CI->ci_is_loaded[] = 'xmlrpcs';
}

?>