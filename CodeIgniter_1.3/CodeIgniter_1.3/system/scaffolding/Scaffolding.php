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
 * Scaffolding Class
 * 
 * Provides the Scaffolding framework
 *
 * @package		CodeIgniter
 * @subpackage	Scaffolding
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/general/scaffolding.html
 */
class Scaffolding {

	var $current_table;
	var $base_url = '';

	function Scaffolding($db_table)
	{
		$obj =& get_instance();
		foreach ($obj->ci_is_loaded as $val)
		{
			$this->$val =& $obj->$val;
		}
				
		/**
		 * Set the current table name
		 * This is done when initializing scaffolding:
		 * $this->_ci_init_scaffolding('table_name')
		 *
		 */
		$this->current_table = $db_table;
		
		/**
		 * Set the path to the "view" files
		 * We'll manually override the "view" path so that
		 * the load->view function knows where to look.
		 */
		$this->load->_ci_set_view_path(BASEPATH.'scaffolding/views/');

		// Set the base URL
		$this->base_url = $this->config->site_url().'/'.$this->uri->segment(1).$this->uri->slash_segment(2, 'both');
		$this->base_uri = $this->uri->segment(1).$this->uri->slash_segment(2, 'leading');

		// Set a few globals
		$data = array(
						'image_url'	=> $this->config->system_url().'scaffolding/images/',
						'base_uri'  => $this->base_uri,
						'base_url'	=> $this->base_url
					);
		
		$this->load->vars($data);
		
		//  Load the helper files we plan to use
		$this->load->helper(array('url', 'form'));
		
				
		log_message('debug', 'Scaffolding Class Initialized');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * "Add" Page
	 *
	 * Shows a form representing the currently selected DB
	 * so that data can be inserted
	 *
	 * @access	public
	 * @return	string	the HTML "add" page
	 */
	function add()
	{
		$this->db->limit(1);
		$query = $this->db->get($this->current_table);
	
		$data = array(
						'title'	=> 'Add Data',
						'fields' => $query->field_data(),
						'action' => $this->base_uri.'/insert'
					);
	
		$this->load->view('add', $data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Insert the data
	 *
	 * @access	public
	 * @return	void	redirects to the view page
	 */
	function insert()
	{
		$this->db->set($_POST);
		
		if ($this->db->insert($this->current_table) === FALSE)
		{
			$this->add();
		}
		else
		{
			redirect($this->base_uri.'/view/');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * "View" Page
	 *
	 * Shows a table containing the data in the currently
	 * selected DB
	 *
	 * @access	public
	 * @return	string	the HTML "view" page
	 */
	function view()
	{
		// Fetch the total number of DB rows
		$query = $this->db->query("SELECT COUNT(*) AS count FROM ".$this->current_table);
		$row = $query->row(); 
		$total_rows = $row->count;

		// Set the query limit/offset
		$per_page = 20;
		$offset = $this->uri->segment(4, 0);
		$this->db->limit($per_page, $offset);
		
		// Run the query
		$query = $this->db->get($this->current_table);

		// Now let's get the field names				
		$fields = $this->db->field_names($this->current_table);
		$primary = current($fields);

		// Pagination!
		$this->pagination->initialize(
							array(
									'base_url'		 => $this->base_url.'/view',
									'total_rows'	 => $total_rows,
									'per_page'		 => $per_page,
									'uri_segment'	 => 4,
									'full_tag_open'	 => '<p>',
									'full_tag_close' => '</p>'
									)
								);	

		$data = array(
						'title'		=> 'View Data',
						'query'		=> $query,
						'fields'	=> $fields,
						'primary'	=> $primary,
						'paginate'	=> $this->pagination->create_links()
					);
						
		$this->load->view('view', $data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * "Edit" Page
	 *
	 * Shows a form representing the currently selected DB
	 * so that data can be edited
	 *
	 * @access	public
	 * @return	string	the HTML "edit" page
	 */
	function edit()
	{
		if (FALSE === ($id = $this->uri->segment(4)))
		{
			return $this->view();
		}

		// Fetch the primary field name
		$fields = $this->db->field_names($this->current_table);				
		$primary = current($fields);

		// Run the query
		$this->db->where($primary, $id);
		$query = $this->db->get($this->current_table);

		$data = array(
						'title'		=> 'Add Data',
						'fields'	=> $query->field_data(),
						'query'		=> $query->row(),
						'action'	=> $this->base_uri.'/update/'.$this->uri->segment(4)
					);
	
		$this->load->view('edit', $data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update
	 *
	 * @access	public
	 * @return	void	redirects to the view page
	 */
	function update()
	{	
		// Fetch the field names
		$fields = $this->db->field_names($this->current_table);				
		$primary = current($fields);

		// Now do the query
		$this->db->set($_POST);
		$this->db->where($primary, $this->uri->segment(4));
		$this->db->update($this->current_table);
		
		redirect($this->base_uri.'/view/');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete Confirmation
	 *
	 * @access	public
	 * @return	string	the HTML "delete confirm" page
	 */
	function delete()
	{
		$data = array(
						'title'		=> 'Delete Data',
						'message'	=> 'Are you sure you want to delete entry ID '.$this->uri->segment(4).'?',
						'no'		=> anchor(array($this->base_uri, 'view'), 'No'),
						'yes'		=> anchor(array($this->base_uri, 'do_delete', $this->uri->segment(4)), 'Yes')
					);
	
		$this->load->view('delete', $data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete
	 *
	 * @access	public
	 * @return	void	redirects to the view page
	 */
	function do_delete()
	{		
		// Fetch the field names
		$fields = $this->db->field_names($this->current_table);				
		$primary = current($fields);

		// Now do the query
		$this->db->where($primary, $this->uri->segment(4));
		$this->db->delete($this->current_table);

		header("Refresh:0;url=".site_url(array($this->base_uri, 'view')));
		exit;
	}

}
?>