<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Session Class
 * 
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session {

	var $now;
	var $encryption		= TRUE;
	var $use_database	= FALSE;
	var $session_table	= FALSE;
    var $sess_length	= 7200;
    var $sess_cookie	= 'ci_session';
	var $userdata		= array();
    var $gc_probability	= 5;
    var $object;
    

	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */		
	function CI_Session()
	{
		$this->object =& get_instance();

		log_message('debug', "Session Class Initialized");
		$this->sess_run();
	}
	// END display_errors()
	
	// --------------------------------------------------------------------
	
	/**
	 * Run the session routines
	 *
	 * @access	public
	 * @return	void
	 */		
	function sess_run()
	{
		/*
		 *  Set the "now" time
		 *
		 * It can either set to GMT or time(). The pref
		 * is set in the config file.  If the developer
		 * is doing any sort of time localization they 
		 * might want to set the session time to GMT so
		 * they can offset the "last_activity" and
		 * "last_visit" times based on each user's locale.
		 *
		 */
		if (strtolower($this->object->config->item('time_reference')) == 'gmt')
		{
			$now = time();
			$this->now = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));   
	
			if (strlen($this->now) < 10)
			{
				$this->now = time();
				log_message('error', 'The session class could not set a proper GMT timestamp so the local time() value was used.');
			}
		}
		else
		{
			$this->now = time();
		}
		
		/*
		 *  Set the session length
		 *
		 * If the session expiration is set to zero in
		 * the config file we'll set the expiration 
		 * two years from now.
		 *
		 */
		$expiration = $this->object->config->item('sess_expiration');
		
		if (is_numeric($expiration))
		{
			if ($expiration > 0)
			{
				$this->sess_length = $this->object->config->item('sess_expiration');
			}
			else
			{
				$this->sess_length = (60*60*24*365*2);
			}
		}
		
		// Do we need encryption?
		$this->encryption = $this->object->config->item('sess_encrypt_cookie');
		
		if ($this->encryption == TRUE)	
		{
			$this->object->load->library('encrypt');
		}		

		// Are we using a database?
		if ($this->object->config->item('sess_use_database') === TRUE AND $this->object->config->item('sess_table_name') != '')
		{
			$this->use_database = TRUE;
			$this->session_table = $this->object->config->item('sess_table_name');
			$this->object->load->database();
		}
		
		// Set the cookie name
		if ($this->object->config->item('sess_cookie_name') != FALSE)
		{
			$this->sess_cookie = $this->object->config->item('cookie_prefix').$this->object->config->item('sess_cookie_name');
		}
	
		/*
		 *  Fetch the current session
		 *
		 * If a session doesn't exist we'll create
		 * a new one.  If it does, we'll update it.
		 *
		 */
		if ( ! $this->sess_read())
		{
			$this->sess_create();
		}
		else
		{	
			// We only update the session every five minutes
			if (($this->userdata['last_activity'] + 300) < $this->now)
			{
				$this->sess_update();
			}
		}
		
		// Delete expired sessions if necessary
		if ($this->use_database === TRUE)
		{		
			$this->sess_gc();
		}	
	}
	// END sess_run()
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch the current session data if it exists
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_read()
	{	
		// Fetch the cookie
		$session = $this->object->input->cookie($this->sess_cookie);
		
		if ($session === FALSE)
		{
			log_message('debug', 'A session cookie was not found.');
			return FALSE;
		}
		
		// Decrypt and unserialize the data
		if ($this->encryption == TRUE)
		{
			$session = $this->object->encrypt->decode($session);
		}

		$session = @unserialize($this->strip_slashes($session));
		
		if ( ! is_array($session) OR ! isset($session['last_activity']))
		{
			log_message('error', 'The session cookie data did not contain a valid array. This could be a possible hacking attempt.');
			return FALSE;
		}
		
		// Is the session current?
		if (($session['last_activity'] + $this->sess_length) < $this->now) 
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Does the IP Match?
		if ($this->object->config->item('sess_match_ip') == TRUE AND $session['ip_address'] != $this->object->input->ip_address())
		{
			$this->sess_destroy();
			return FALSE;
		}
		
		// Does the User Agent Match?
		if ($this->object->config->item('sess_match_useragent') == TRUE AND $session['user_agent'] != substr($this->object->input->user_agent(), 0, 50))
		{
			$this->sess_destroy();
			return FALSE;
		}
		
		// Is there a corresponding session in the DB?
		if ($this->use_database === TRUE)
		{
			$sql = "SELECT * FROM `".$this->session_table."` WHERE session_id = ? ";
			$match[] = $session['session_id'];
					
			if ($this->object->config->item('sess_match_ip') == TRUE)
			{
				$sql .= "AND ip_address = ? ";
				$match[] = $session['ip_address'];
			}

			if ($this->object->config->item('sess_match_useragent') == TRUE)
			{
				$sql .= "AND user_agent = ?";
				$match[] = $session['user_agent'];
			}
			
			$query = $this->object->db->query($sql, $match);

			if ($query->num_rows() == 0)
			{
				$this->sess_destroy();
				return FALSE;
			}
			else
			{
				$row = $query->row();
				if (($row->last_activity + $this->sess_length) < $this->now) 
				{
					$this->object->db->query("DELETE FROM `".$this->session_table."` WHERE session_id = ?", array($session['session_id']));
					$this->sess_destroy();
					return FALSE;
				}
			}
		}
	
		// Session is valid!
		$this->userdata = $session;
		unset($session);
		
		return TRUE;
	}
	// END sess_read()
	
	// --------------------------------------------------------------------
	
	/**
	 * Write the session cookie
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_write()
	{								
		$cookie_data = serialize($this->userdata);
		
		if ($this->encryption == TRUE)
		{
			$cookie_data = $this->object->encrypt->encode($cookie_data);
		}

		setcookie(
					$this->sess_cookie, 
					$cookie_data, 
					$this->sess_length + $this->now, 
					$this->object->config->item('cookie_path'), 
					$this->object->config->item('cookie_domain'), 
					0
				);
	}
	// END sess_read()
	
	// --------------------------------------------------------------------
	
	/**
	 * Create a new session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_create()
	{	
		$sessid = '';
		while (strlen($sessid) < 32) 
		{    
			$sessid .= mt_rand(0, mt_getrandmax());
		}
	
		$this->userdata = array(
							'session_id' 	=> md5(uniqid($sessid, TRUE)),
							'ip_address' 	=> $this->object->input->ip_address(),
							'user_agent' 	=> substr($this->object->input->user_agent(), 0, 50),
							'last_activity'	=> $this->now
							);
		
		
		// Save the session in the DB if needed
		if ($this->use_database === TRUE)
		{
			$this->object->db->query($this->object->db->insert_string($this->session_table, $this->userdata));
		}
			
		// Write the cookie
		$this->userdata['last_visit'] = 0;		
		$this->sess_write();
	}
	// END sess_read()
	
	// --------------------------------------------------------------------
	
	/**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_update()
	{	
        if (($this->userdata['last_activity'] + $this->sess_length) < $this->now) 
        {
			$this->userdata['last_visit'] = $this->userdata['last_activity'];
        }
	
		$this->userdata['last_activity'] = $this->now;
		
		// Update the session in the DB if needed
		if ($this->use_database === TRUE)
		{		
			$this->object->db->query($this->object->db->update_string($this->session_table, array('last_activity' => $this->now), array('session_id' => $this->userdata['session_id'])));
		}
		
		// Write the cookie
		$this->sess_write();
	}
	// END sess_update()
	
	// --------------------------------------------------------------------
	
	/**
	 * Destroy the current session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_destroy()
	{
		setcookie(
					$this->sess_cookie, 
					addslashes(serialize(array())), 
					($this->now - 31500000), 
					$this->object->config->item('cookie_path'), 
					$this->object->config->item('cookie_domain'), 
					0
				);
	}
	// END sess_destroy()
	
	// --------------------------------------------------------------------
	
	/**
	 * Garbage collection
	 *
	 * This deletes expired session rows from database
	 * if the probability percentage is met
	 *
	 * @access	public
	 * @return	void
	 */
    function sess_gc()
    {  
		srand(time());
		if ((rand() % 100) < $this->gc_probability) 
		{  
			$expire = $this->now - $this->sess_length;
			$this->object->db->query("DELETE FROM `".$this->session_table."` WHERE last_activity < $expire");    
			log_message('debug', 'Session garbage collection performed.');
		}    
    }
	// END sess_destroy()
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch a specific item form  the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */		
	function userdata($item)
	{
    	return ( ! isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}
	// END sess_destroy()
	
	// --------------------------------------------------------------------
	
	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */		
	function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}
	
		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$this->userdata[$key] = $val;
			}
		}
	
    	$this->sess_write();
	}
	// END set_userdata()
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */		
	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}
	
		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				unset($this->userdata[$key]);
			}
		}
	
    	$this->sess_write();
	}
	// END set_userdata()
	
	// --------------------------------------------------------------------
	
	/**
	 * Strip slashes
	 *
	 * @access	public
	 * @param	mixed
	 * @return	mixed
	 */
     function strip_slashes($vals)
     {
     	if (is_array($vals))
     	{	
     		foreach ($vals as $key=>$val)
     		{
     			$vals[$key] = $this->strip_slashes($val);
     		}
     	}
     	else
     	{
     		$vals = stripslashes($vals);
     	}
     	
     	return $vals;
	}
	// END strip_slashes()
}

?>