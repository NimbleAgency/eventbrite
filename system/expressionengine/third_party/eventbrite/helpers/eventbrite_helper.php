<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Curl
 *
 * @access public
 * @param null
 * @return null
 *
 */

	function curl_eventbrite($url)
	{
		// create a new curl resource
	    $ch = curl_init();

	    // set URL to download
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    $output = curl_exec($ch);

	    // close the curl resource, and free system resources
	    curl_close($ch);

	    // print output
	    return $output;
	}
	
/**
 * Clean XHTML
 * cleans html string, and returns xhtml string
 *
 * @access public
 * @param string
 * @return string
 *
 */

	function clean_html($html=false)
	{
		// get instance
		$EE =& get_instance();
		
		// load htmlpurifier
		$EE->load->library('HTMLPurifier');
		
		// htmlpurifier settings
		$purify_config = array(
			'HTML.ForbiddenAttributes' => array('style','face'),
			'HTML.ForbiddenElements' => array('font','img','span'),
			'AutoFormat.AutoParagraph' => true,
			'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
			'AutoFormat.RemoveEmpty' => true
		);

		// tags we dont want to strip
		$valid_tags = "<br><p><a><i><em><u><ul><li><h1><h2><h3><h4><h5><h6>";

		// first, lets strip everything except valid elements
		$stripped = strip_tags($html, $valid_tags);
		$clean = $EE->htmlpurifier->purify( $html , $purify_config);

		// return clean
		return $clean;
	}
	
/**
 * Convert XML to array
 *
 * @access public
 * @param null
 * @return null
 *
 */

	function convert_xml_to_array($xml=false)
	{
		// get instance
		$EE =& get_instance();
		
		// load libraries
		$EE->load->library('xml2array');
		
		return $EE->xml2array->convert($xml);
	}


// -----------------------------------------------------
?>