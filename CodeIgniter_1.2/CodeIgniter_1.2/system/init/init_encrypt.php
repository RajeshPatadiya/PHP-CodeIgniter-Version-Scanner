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

$obj =& get_instance();
$obj->encrypt = new _Encrypt();
$obj->ci_is_loaded[] = 'encrypt';

?>