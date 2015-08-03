<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|==========================================================
| Initialize Encryption Class
|==========================================================
|
*/
if ( ! class_exists('_Encrypt'))
{
	require_once(BASEPATH.'libraries/Encrypt'.EXT);
}

if ( ! is_object($CI))
{
	$this->encrypt = new _Encrypt();
	$this->ci_is_loaded[] = 'encrypt';
}
else
{
	$CI->encrypt = new _Encrypt();
	$CI->ci_is_loaded[] = 'encrypt';
}

?>