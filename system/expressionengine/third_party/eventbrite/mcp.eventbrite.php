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
class Eventbrite_mcp
{

	var $base;			// the base url for this module			
	var $form_base;		// base url for forms
	var $module_name = "Eventbrite";

/**
 * Construct
 *
 * @access public
 * @param null
 * @return null
 *
 */
	
	Function Eventbrite_mcp()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		
		// load models, libraries and helpers
		$this->EE->load->model('eventbrite_model','eventbrite');
		$this->EE->load->library('form_validation');
		//$this->EE->load->helper('url');
		
		// settings
		$this->EE->cp->set_variable('cp_page_title', lang('eventbrite_module_name') );
		
		$this->base	 	 	= BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;
		$this->form_base 	= 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;
		$this->theme_base 	= $this->EE->config->item('theme_folder_url').'third_party/eventbrite/';
		$this->site_id	 	= $this->EE->config->item('site_id');

        // set the top nav
		$this->EE->cp->set_right_nav(array(
			'Home'		=> $this->base,
			'Settings'	=> $this->base.AMP.'method=settings'
		));
	}

/**
 * Index (main control panel controller)
 *
 * @access public
 * @param null
 * @return null
 *
 */
	 
	function index()
	{
		// not sure what this will have yet.
		// @todo Eventbrite control panel home.
		
		return $this->EE->load->view('index', array(), TRUE);
	}
	
/**
 * Settings
 *
 * @access public
 * @param null
 * @return null
 *
 */
	
	function settings()
	{
		// page title
		$this->EE->cp->set_variable('cp_page_title', lang('eventbrite_settings_title') );
		
		// get existing settings
		$settings = $this->EE->eventbrite->get_settings();
		
		// template data
		$template_data['form_base'] = $this->form_base.AMP.'method=settings';
		$template_data['settings'] = $settings;
		
		// validation rules
		$this->EE->form_validation->set_rules('username', 'Username', 'required');
		$this->EE->form_validation->set_rules('password', 'Password', 'required');
		$this->EE->form_validation->set_rules('app_key', 'Application Key', 'required');
		
		// validate
		if ($this->EE->form_validation->run() == FALSE)
		{
			// error
			// load view
			return $this->EE->load->view('settings', $template_data, TRUE);
		}
		else
		{
			// success
			$data = array(
				'username' => $this->EE->input->post('username'),
				'password' => $this->EE->input->post('password'),
				'app_key' => $this->EE->input->post('app_key')
			);
			
			// update
			$this->EE->eventbrite->update_settings($data);
			
			// message
			$this->EE->session->set_flashdata('message_success', lang('settings_update_success'));
			
			// redirect
			$this->EE->functions->redirect($this->base.AMP.'method=settings');
		}
	}
	
// --------------------------------------------------------------------
}

/* End of file mcp.eventbrite.php */
/* Location: ./system/expressionengine/third_party/modules/eventbrite/mcp.eventbrite.php */