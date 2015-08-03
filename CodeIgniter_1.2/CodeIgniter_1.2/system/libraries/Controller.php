<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|==========================================================
| Code Igniter - by pMachine
|----------------------------------------------------------
| www.codeignitor.com
|----------------------------------------------------------
| Copyright (c) 2006, pMachine, Inc.
|----------------------------------------------------------
| This library is licensed under an open source agreement:
| www.codeignitor.com/docs/license.html
|----------------------------------------------------------
| File: libraries/Controller.php
|----------------------------------------------------------
| Purpose: Application Controller
|==========================================================
*/


class Controller Extends _Loader {

	var $ci_is_loaded	= array();
	var $ci_scaffolding	= FALSE;
	var $ci_scaff_table	= FALSE;
	var $object;
	
	/*
	|==========================================================
	| Constructor
	|==========================================================
	|
	| Loads the base classes needed to run CI, and runs the
	| "autoload" routine which loads the systems specified
	| in the "autoload" config file.
	|
	*/
	function Controller()
	{	
		parent::_Loader();
		
		/*
		|----------------------------------------------
		| Initialize the Core Resources
		|----------------------------------------------
		|
		| These are the base libs needed to run CI
		|
		*/
		$this->ini_core();
	
		/*
		|----------------------------------------------
		| Globalize the controller object
		|----------------------------------------------
		|
		| This lets us overcome some PHP 4 scoping limitations.
		| It's more of a hack, really...
		| Do NOT assign $OBJ by reference.
		|
		*/
		$this->load =& $this;
		global $OBJ; $OBJ = $this->load;
		
		/*
		|----------------------------------------------
		| Auto-initialize
		|----------------------------------------------
		|
		| This initializes the core systems that are
		| specified in the libraries/autoload.php file, as
		| well as the systems specified in the $autoload
		| class array above.
		|
		| It returns the "autoload" array so we can
		| pass it to the Loader class since it needs
		| to autoload plugins and helper files
		|
		*/
	
		$autoload = $this->autoload();
		
		/*
		|----------------------------------------------
		| Run the autoloader
		|----------------------------------------------
		*/
		$this->load->_autoload($autoload);

		log_message('debug', "Controller Class Initialized");
	}
	
	/*
	|==========================================================
	| Initialization Handler
	|==========================================================
	|
	| Looks for the existence of a handler method and calls it
	|
	*/
	function initialize($what, $param = FALSE)
	{		
		$method = 'init_'.strtolower(str_replace(EXT, '', $what));

		if ( ! method_exists($this, $method))
		{
			if ( ! file_exists(BASEPATH.'init/'.$method.EXT))
			{
				if ( ! file_exists(APPPATH.'init/'.$method.EXT))
				{
					log_message('error', "Unable to load the requested class: ".$what);
					show_error("Unable to load the class: ".$what);
				}
				
				include(APPPATH.'init/'.$method.EXT);
			}
			else
			{
				include(BASEPATH.'init/'.$method.EXT);
			}
		}
		else
		{
			if ($param === FALSE)
			{
				$this->$method();
			}
			else
			{
				$this->$method($param);
			}
		}
	}
	
	/*
	|==========================================================
	| Auto-initialize Core Classes
	|==========================================================
	|
	| The config/autoload.php file contains an array that
	| permits sub-systems to be loaded automatically.
	|
	*/
	function autoload()
	{
		include_once(APPPATH.'config/autoload'.EXT);
		
		if ( ! isset($autoload))
		{
			return FALSE;
		}
		
		if (count($autoload['config']) > 0)
		{
			foreach ($autoload['config'] as $key => $val)
			{
				$this->config->load($val);
			}
		}
		unset($autoload['config']);
		
		if ( ! is_array($autoload['core']))
		{
			$autoload['core'] = array($autoload['core']);
		}

		if ( ! is_array($autoload['config']))
		{
			$autoload['config'] = array($autoload['config']);
		}
						
		foreach ($autoload['core'] as $item)
		{
			$this->initialize($item);
		}
		
		return $autoload;
	}
	
	
	/*
	|==========================================================
	| Initialize Core Classes
	|==========================================================
	|
	| These are the base libs needed to run CI
	|
	*/
	function ini_core()
	{
		global $IN, $BM, $CFG, $URI, $OUT;
		
		require_once(BASEPATH.'libraries/Language'.EXT);

		$this->input =& $IN;
		$this->benchmark =& $BM;
		$this->config =& $CFG;		
		$this->uri =& $URI;
		$this->output =& $OUT;	
		$this->lang = new _Language();

		foreach (array('config', 'input', 'benchmark', 'uri', 'lang', 'output', 'load') as $val)
		{
			$this->ci_is_loaded[] = $val;
		}
	}
	
	/*
	|==========================================================
	| Initialize Scaffolding
	|==========================================================
	|
	| This initializing function works a bit different than the
	| others. It doesn't load the class.  Instead, it simply
	| sets a flag indicating that scaffolding is allowed to be
	| used.  The actual scaffolding function below is
	| called by the front controller based on whether the
	| second segment of the URL matches the "secret" scaffolding
	| word stored in the application/config/routes.php
	|
	*/
	function init_scaffolding($table = FALSE)
	{
		if ($table === FALSE)
		{
			show_error('You must include the name of the table you would like access when you initialize scaffolding');
		}
		
		$this->ci_scaffolding = TRUE;
		$this->ci_scaff_table = $table;
	}

	/*
	|==========================================================
	| Initialize Database
	|==========================================================
	|
	| Loads the DB config file, instantiates the DB class
	| and connects to the specified DB.
	*/
	function init_database($params = '', $return = FALSE)
	{
		$dsn_str = FALSE;
		$db_vals = array('hostname' => '', 'username' => '', 'password' => '', 'database' => '', 'pconnect' => FALSE, 'dbdriver' => 'mysql', 'db_debug' => FALSE);
	
		if (is_array($params))
		{		
			foreach ($db_vals as $key => $val)
			{
				if (isset($params[$key]))
				{
					$db_vals[$key] = $params[$key];
				}
			}		
		}
		else
		{
			if (strpos($params,'://') !== FALSE) 
			{
				$dsn_str = TRUE;
			}
			else
			{
				include(APPPATH.'config/database'.EXT);
				$group = ($params == '') ? $active_group : $params;
				
				foreach ($db_vals as $key => $val)
				{
					if (isset($db[$group][$key]))
					{
						$db_vals[$key] = $db[$group][$key];
					}
				}
			}
		}
		
		if ( ! class_exists('_DB'))
		{
			require_once(BASEPATH.'drivers/'.$db_vals['dbdriver'].EXT);
		}

		if ($dsn_str === TRUE)
		{
			$DB = new _DB($params);
		}
		else
		{
			$DB = new _DB(
							$db_vals['hostname'],
							$db_vals['username'],
							$db_vals['password'],
							$db_vals['database']
						);
			
			$DB->set_debug($db_vals['db_debug']);
			$DB->set_persistence($db_vals['pconnect']);			
			$DB->connect();
		}
		
		if ($return === TRUE)
		{
			return $DB;
		}
		
		$obj =& get_instance();
		$obj->ci_is_loaded[] = 'db';
		$obj->db =& $DB;
	}

	/*
	|==========================================================
	| Tests to see if an class is loaded
	|==========================================================
	*/
	function is_loaded($class)
	{
		return ( ! in_array($class, $this->ci_is_loaded)) ? FALSE : TRUE;
	}

	/*
	|==========================================================
	| Scaffolding Class Interface
	|==========================================================
	|
	*/
	function _scaffolding()
	{
		if ($this->ci_scaffolding === FALSE OR $this->ci_scaff_table === FALSE)
		{
			show_404('Scaffolding unavailable');
		}
		
		if (class_exists('Scaffolding')) return;
			
		if ( ! in_array($this->uri->segment(3), array('add', 'insert', 'edit', 'update', 'view', 'delete', 'do_delete')))
		{
			$method = 'view';
		}
		else
		{
			$method = $this->uri->segment(3);
		}
		
		if ( ! in_array('db', $this->ci_is_loaded))
		{
			$this->initialize('database');
		}
		
		$this->initialize('pagination');
		require_once(BASEPATH.'scaffolding/Scaffolding'.EXT);
		$this->scaff = new Scaffolding($this->ci_scaff_table);
		$this->scaff->$method();
	}

}
?>