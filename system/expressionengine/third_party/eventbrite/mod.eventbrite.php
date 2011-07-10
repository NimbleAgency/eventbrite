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
class Eventbrite
{

/**
 * Construct
 *
 * @access public
 * @param null
 * @return null
 *
 */

	function Eventbrite()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();

		// load models, etc
		$this->EE->load->model('eventbrite_model','eventbrite');
		$this->EE->load->helper('eventbrite_helper');
		
		// set UTF-8
		header('Content-type: text/html; charset=utf-8');
	}
	
/**
 * Get
 *
 * @access public
 * @param id
 * @return null
 *
 */

	function get()
	{
		// get event id
		$event_id = $this->EE->TMPL->fetch_param('event_id');
		
		// check event id
		if($event_id != '' && is_numeric($event_id))
		{
			// api call
			$event = convert_xml_to_array( curl_eventbrite("http://www.eventbrite.com/xml/event_get?".$this->EE->eventbrite->get_authorization()."&id=".$event_id) );
	
			// get the tagdata
			$tagdata = $this->EE->TMPL->tagdata;
			$tagdata = $this->EE->functions->prep_conditionals($tagdata, $this->EE->TMPL->var_single);
			
			// tagdata good?
			if($tagdata)
			{
				// setup array
				$variables = array();
				
				// build ticket array to parse
				$tickets = array();
				$ticket_count = 0;
				
				if(array_key_exists('currency', $event['event']['tickets']['ticket']))
				{
					$tickets[0]['name'] = $event['event']['tickets']['ticket']['name'];
					$tickets[0]['currency'] = $event['event']['tickets']['ticket']['currency'];
					$tickets[0]['price'] = $event['event']['tickets']['ticket']['price'];
					$tickets[0]['quantity_available'] = $event['event']['tickets']['ticket']['quantity_available'];
					$tickets[0]['quantity_sold'] = $event['event']['tickets']['ticket']['quantity_sold'];
					$tickets[0]['visible'] = $event['event']['tickets']['ticket']['visible'];
				}
				else
				{
					foreach($event['event']['tickets']['ticket'] as $t)
					{
						$tickets[$ticket_count]['name'] = $t['name'];
						$tickets[$ticket_count]['currency'] = $t['currency'];
						$tickets[$ticket_count]['price'] = $t['price'];
						$tickets[$ticket_count]['quantity_available'] = $t['quantity_available'];
						$tickets[$ticket_count]['quantity_sold'] = $t['quantity_sold'];
						$tickets[$ticket_count]['visible'] = $t['visible'];
				
						$ticket_count++;
					}
				}
				
				// stuff we want to replace
				$variables[] = array(
					'id' => $event['event']['id'],
					'title' => $event['event']['title'],
					'capacity' => $event['event']['capacity'],
					'created' => $event['event']['created'],
					'end_date' => strtotime($event['event']['end_date']),
					'start_date' => strtotime($event['event']['start_date']),
					'timezone' => $event['event']['timezone'],
					'description' => clean_html($event['event']['description']),
					'tickets' => $tickets,
					'url' => $event['event']['url'],
					'venue_name' => $event['event']['venue']['name'],
					'venue_address' => $event['event']['venue']['address'],
					'venue_address_2' => $event['event']['venue']['address_2'],
					'venue_city' => $event['event']['venue']['city'],
					'venue_country' => $event['event']['venue']['country'],
					'venue_country_code' => $event['event']['venue']['country_code'],
					'venue_id' => $event['event']['venue']['id'],
					'venue_latitude' => $event['event']['venue']['latitude'],
					'venue_longitude' => $event['event']['venue']['longitude'],
					'venue_zip' => $event['event']['venue']['postal_code'],
					'venue_state' => $event['event']['venue']['region']
				);

				// parse the variables
				$parsed_data = $this->EE->TMPL->parse_variables($tagdata, $variables); 

				// return
				return $parsed_data;
			}
			else
			{
				$this->return_data = "There was an error in your template.";
			}
			
		}
		else
		{
			return "Invalid Event ID.";
		}
	}
	
/**
 * Search
 *
 * @access public
 * @param null
 * @return null
 *
 */
	
	function search()
	{
		$keywords = $this->EE->TMPL->fetch_param('keywords');
		
		$search_url = "http://www.eventbrite.com/xml/event_search?".$this->EE->eventbrite->get_authorization()."&keywords=".$keywords."";
		$events = convert_xml_to_array(curl_eventbrite($search_url));
		$event = $events['events']['event'];

		// if we only have 1 result, we still need it to be an array
		if(!isset($event[0]))
		{
			$event = array();
			$event[0] = $events['events']['event'];
		}
	
		// get the tagdata
		$tagdata = $this->EE->TMPL->tagdata;
		$tagdata = $this->EE->functions->prep_conditionals($tagdata, $this->EE->TMPL->var_pair);
		
		$output = '';

		foreach($event as $e)
		{
			// build ticket array to parse
			$tickets = array();
			$ticket_count = 0;
			
			
			if(isset($e['tickets']['ticket']))
			{
				foreach($e['tickets']['ticket'] as $t)
				{
					$tickets[$ticket_count]['name'] = (isset($t['name']) ? $t['name'] : "");
					$tickets[$ticket_count]['currency'] = (isset($t['currency']) ? $t['currency'] : "");
					$tickets[$ticket_count]['price'] = (isset($t['price']) ? $t['price'] : "");
					$tickets[$ticket_count]['quantity_available'] = (isset($t['quantity_available']) ? $t['quantity_available'] : "");
					$tickets[$ticket_count]['quantity_sold'] = (isset($t['quantity_sold']) ? $t['quantity_sold'] : "");
					$tickets[$ticket_count]['visible'] = (isset($t['visible']) ? $t['visible'] : "");

					$ticket_count++;
				}
			}
			
			
			$variable_row = array(
				'id' => (isset($e['id']) ? $e['id'] : ""),
				'title' => (isset($e['title']) ? $e['title'] : ""),
				'capacity' => (isset($e['capacity']) ? $e['capacity'] : ""),
				'created' => (isset($e['created']) ? $e['created'] : ""),
				'end_date' => (isset($e['end_date']) ? strtotime($e['end_date']) : ""),
				'start_date' => (isset($e['start_date']) ? strtotime($e['start_date']) : ""),
				'timezone' => (isset($e['timezone']) ? $e['timezone'] : ""),
				'description' => (isset($e['description']) && !is_array($e['description']) ? clean_html($e['description']) : ""),
				'tickets' => $tickets,
				'url' => (isset($e['url']) ? $e['url'] : ""),
				'venue_name' => (isset($e['venue']['name']) ? $e['venue']['name'] : ""),
				'venue_address' => (isset($e['venue']['address']) ? $e['venue']['address'] : ""),
				'venue_address_2' => (isset($e['venue']['address_2']) ? $e['venue']['address_2'] : ""),
				'venue_city' => (isset($e['venue']['city']) ? $e['venue']['city'] : ""),
				'venue_country' => (isset($e['venue']['country']) ? $e['venue']['country'] : ""),
				'venue_country_code' => (isset($e['venue']['country_code']) ? $e['venue']['country_code'] : ""),
				'venue_id' => (isset($e['venue']['id']) ? $e['venue']['id'] : ""),
				'venue_latitude' => (isset($e['venue']['latitude']) ? $e['venue']['latitude'] : ""),
				'venue_longitude' => (isset($e['venue']['longitude']) ? $e['venue']['longitude'] : ""),
				'venue_zip' => (isset($e['venue']['postal_code']) ? $e['venue']['postal_code'] : ""),
				'venue_state' => (isset($e['venue']['region']) ? $e['venue']['region'] : "")
			);
			
			$variables[] = $variable_row;
			
			$output .= $this->EE->TMPL->parse_variables_row($tagdata, $variable_row);
		}
		
		// return
		return $output;
	}
	
/**
 * Attendees
 *
 * @access public
 * @param null
 * @return null
 *
 */

	function attendees()
	{
		// get event id
		$event_id = $this->EE->TMPL->fetch_param('event_id');
		
		// check event id
		if($event_id != '' && is_numeric($event_id))
		{
			// api call
			$attendees = convert_xml_to_array( curl_eventbrite("http://www.eventbrite.com/xml/event_list_attendees?".$this->EE->eventbrite->get_authorization()."&id=".$event_id) );
			p($attendees);
	
			// get the tagdata
			$tagdata = $this->EE->TMPL->tagdata;
			$tagdata = $this->EE->functions->prep_conditionals($tagdata, $this->EE->TMPL->var_single);
			
			// tagdata good?
			if($tagdata)
			{
				// setup array
				$variables = array();
				
				/*
				// build ticket array to parse
				$tickets = array();
				$ticket_count = 0;
				
				foreach($event['event']['tickets']['ticket'] as $t)
				{
					$tickets[$ticket_count]['name'] = $t['name'];
					$tickets[$ticket_count]['currency'] = $t['currency'];
					$tickets[$ticket_count]['price'] = $t['price'];
					$tickets[$ticket_count]['quantity_available'] = $t['quantity_available'];
					$tickets[$ticket_count]['quantity_sold'] = $t['quantity_sold'];
					$tickets[$ticket_count]['visible'] = $t['visible'];
					
					$ticket_count++;
				}
				*/
				
				/*
				// stuff we want to replace
				$variables[] = array(
					'id' => $event['event']['id'],
					'title' => $event['event']['title'],
					'capacity' => $event['event']['capacity'],
					'created' => $event['event']['created'],
					'end_date' => strtotime($event['event']['end_date']),
					'start_date' => strtotime($event['event']['start_date']),
					'timezone' => $event['event']['timezone'],
					'description' => clean_html($event['event']['description']),
					'tickets' => $tickets,
					'url' => $event['event']['url'],
					'venue_name' => $event['event']['venue']['name'],
					'venue_address' => $event['event']['venue']['address'],
					'venue_address_2' => $event['event']['venue']['address_2'],
					'venue_city' => $event['event']['venue']['city'],
					'venue_country' => $event['event']['venue']['country'],
					'venue_country_code' => $event['event']['venue']['country_code'],
					'venue_id' => $event['event']['venue']['id'],
					'venue_latitude' => $event['event']['venue']['latitude'],
					'venue_longitude' => $event['event']['venue']['longitude'],
					'venue_zip' => $event['event']['venue']['postal_code'],
					'venue_state' => $event['event']['venue']['region']
				);
				*/
				// parse the variables
				//$parsed_data = $this->EE->TMPL->parse_variables($tagdata, $variables); 

				// return
				//return $parsed_data;
			}
			else
			{
				$this->return_data = "There was an error in your template.";
			}
			
		}
		else
		{
			return "Invalid Event ID.";
		}
	}

//----------------------------------------------------------------------------------	
}

/* End of file mod.eventbrite.php */
/* Location: ./system/expressionengine/third_party/eventbrite/mod.eventbrite.php */