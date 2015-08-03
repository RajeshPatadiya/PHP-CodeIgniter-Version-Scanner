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

if ( ! is_object($CI))
{
	$this->session = new _Session($this);
	$this->ci_is_loaded[] = 'session';
}
else
{
	$CI->session = new _Session($CI);
	$CI->ci_is_loaded[] = 'session';
}

?>