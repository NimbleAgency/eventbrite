<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *  Eventbrite Class
 *
 * @package		ExpressionEngine
 * @category	Module
 * @author		Todd Perkins
 * @link		http://www.toddperkins.net/
 *
 */
class Eventbrite_upd
{
	var $version 		= '0.1a';
	var $module_name 	= 'Eventbrite';
	var $class_name 	= 'Eventbrite';
	
	function Eventbrite_upd()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}

/**
 * Module Installer
 *
 * @access	public
 * @return	bool
 */	

	function install()
	{
		$this->EE->load->dbforge();

		$data = array(
			'module_name' => $this->module_name,
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);

		$this->EE->db->insert('modules', $data);

		$data = array(
			'class'		=> $this->class_name,
			'method'	=> 'index'
		);
		
		$this->EE->db->insert('actions', $data);
		
		// create settings table
		$fields = array(
			'id' 		=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'setting'	=> array('type' => 'varchar', 'constraint' => '250'),
			'value'		=> array('type' => 'varchar', 'constraint' => '250')
		);

		// add fields / set key
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', true);
		
		// add form table to database
		$this->EE->dbforge->create_table('eventbrite_settings', true);
		
		// insert default data
		$initial_data = array(
			'username' => '',
			'password' => '',
			'app_key' => ''
		);
		
		foreach($initial_data as $s => $v)
		{
			$this->EE->db->set('setting',$s);
			$this->EE->db->set('value',$v);
			$this->EE->db->insert('exp_eventbrite_settings');
		}

		// return
		return TRUE;
	}
	
/**
 * Module Uninstaller
 *
 * @access	public
 * @return	bool
 *
 */

	function uninstall()
	{
		$this->EE->load->dbforge();

		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => $this->module_name));

		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');

		$this->EE->db->where('module_name', $this->module_name);
		$this->EE->db->delete('modules');

		$this->EE->db->where('class', $this->class_name);
		$this->EE->db->delete('actions');
		
		// drop settings
		$this->EE->dbforge->drop_table('eventbrite_settings');
	
		return TRUE;
	}

/**
 * Module Updater
 *
 * @access	public
 * @return	bool
 *
 */	
	
	function update($current='')
	{
		return TRUE;
	}
	
}
/* END Class */

/* End of file upd.eventbrite.php */
/* Location: ./system/expressionengine/third_party/modules/eventbrite/upd.eventbrite.php */