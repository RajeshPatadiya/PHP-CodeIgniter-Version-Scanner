<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|==========================================================
| Initialize XML-RPC Request Class
|==========================================================
*/
$config = array();
if (file_exists(APPPATH.'config/xmlrpc'.EXT))
{
	include_once(APPPATH.'config/xmlrpc'.EXT);
}

if ( ! class_exists('_XML_RPC'))
{		
	require_once(BASEPATH.'libraries/Xmlrpc'.EXT);		
}

if ( ! is_object($CI))
{
	$this->xmlrpc = new _XML_RPC($config);
	$this->ci_is_loaded[] = 'xmlrpc';
}
else
{
	$CI->xmlrpc = new _XML_RPC($config);
	$CI->ci_is_loaded[] = 'xmlrpc';
}

?>