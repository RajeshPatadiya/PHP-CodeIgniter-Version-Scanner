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
if ( ! is_object($CI))
{
	$this->parser = new _Parser($this);
	$this->ci_is_loaded[] = 'parser';
}
else
{
	$CI->parser = new _Parser($CI);
	$CI->ci_is_loaded[] = 'parser';
}
?>