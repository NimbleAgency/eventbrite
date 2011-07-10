<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *  Eventbrite Model
 *
 * @package		ExpressionEngine
 * @category	Module
 * @author		Todd Perkins
 * @link		http://www.toddperkins.net/
 *
 */

// ------------------------------------------------------------------------

class Eventbrite_model extends CI_Model
{

/**
 * Construct
 *
 * @access public
 * @param null
 * @return null
 *
 */
 
	function Eventbrite_model()
	{
		parent::__construct();
    }

/**
 * Update settings
 *
 * @access public
 * @param null
 * @return null
 *
 */

    function update_settings($data=false)
	{
		foreach($data as $setting => $value)
		{
			$this->db->set('value', $value);
			$this->db->where('setting', $setting);
			$this->db->update('exp_eventbrite_settings');
		}
	}
	
/**
 * Get settings
 *
 * @access public
 * @param null
 * @return array
 *
 */

	function get_settings()
	{
		$query = $this->db->get('exp_eventbrite_settings');
		$result = $query->result_array();
		
		// build easy to user array of settings
		$return = array();
		foreach($result as $r)
		{
			$return[$r['setting']] = $r['value'];
		}
		return $return;
	}
	
/**
 * Get authorization string
 *
 * @access public
 * @param null
 * @return null
 *
 */

	function get_authorization()
	{
		$settings = $this->get_settings();
		return $authorization 	= "app_key=".$settings['app_key']."&user=".$settings['username']."&password=".$settings['password'];
	}

// ------------------------------------------------------------------------    
}
?>