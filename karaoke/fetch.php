<?php
	/**
	 * fetch.php PHP5
	 * Fetch the requred JSON data using the Slideshare.net API.
	 */
	
	// No headers, we manaully evalJSON.
	// header('Content-type: text/x-javascript');
	
	ini_set('error_reporting', 'E_ERROR');
	ini_set('allow_url_fopen', 'On');
	require('incs/php/xml.php');

	$ptag	  = urlencode($_POST['ptag']);
	$ptag = $tag  = $_POST['tag'];
	$api_key  = 'SLIDESHARE API KEY HERE';
	$api_hash = 'SLIDESHARE API HASH HERE';

	// Make the API call
	$req	= "http://www.slideshare.net/api/1/karaoke_tag?api_key=".$api_key."&hash=".$api_hash."&ts=1183713711&tag=".$tag."&ptag=".$ptag."&lang=all&limit=100&offset=0";
	$res	= file_get_contents($req);
	
	$data = XML_unserialize(utf8_encode($res));

	// Generate JSON from the XML.
	$json	= '[';
	$count = $data['Tag']['count'] < 100 ? $data['Tag']['count'] : 100;
	
	if($count==1) {
		$id				= $data['Tag']['Slideshow']['id'];
		$title			= escape($data['Tag']['Slideshow']['title']);
		$stripped_title	= escape($data['Tag']['Slideshow']['stripped_title']);
		$tags			= escape($data['Tag']['Slideshow']['tags']);
		$download_link	= escape($data['Tag']['Slideshow']['download_link']);
		$extension		= escape($data['Tag']['Slideshow']['extension']);
		$total_slides	= $data['Tag']['Slideshow']['total_slides'];
		$plain_hits		= $data['Tag']['Slideshow']['plain_hits'];
		$uploaded_on	= $data['Tag']['Slideshow']['uploaded_on'];		
		$user_id		= $data['Tag']['Slideshow']['user']['user_id'];
		$user_login		= $data['Tag']['Slideshow']['user']['user_login'];
		$user_location	= escape($data['Tag']['Slideshow']['user']['user_location']);
		$json .= "[$id, $total_slides, $plain_hits, '$title','$stripped_title', '$uploaded_on', '$tags', '$download_link', '$extension',{user_id:$user_id, user_login:'$user_login', user_location:'$user_location'}],";	
	} else {
	for($i=0;$i<$count;$i++) {
		$id				= $data['Tag']['Slideshow'][$i]['id'];
		$title			= escape($data['Tag']['Slideshow'][$i]['title']);
		$stripped_title	= escape($data['Tag']['Slideshow'][$i]['stripped_title']);
		$tags			= escape($data['Tag']['Slideshow'][$i]['tags']);
		$download_link	= escape($data['Tag']['Slideshow'][$i]['download_link']);
		$extension		= escape($data['Tag']['Slideshow'][$i]['extension']);
		$total_slides	= $data['Tag']['Slideshow'][$i]['total_slides'];
		$plain_hits		= $data['Tag']['Slideshow'][$i]['plain_hits'];
		$uploaded_on	= $data['Tag']['Slideshow'][$i]['uploaded_on'];		
		$user_id		= $data['Tag']['Slideshow'][$i]['user']['user_id'];
		$user_login		= $data['Tag']['Slideshow'][$i]['user']['user_login'];
		$user_location	= escape($data['Tag']['Slideshow'][$i]['user']['user_location']);
		
		$json .= "[$id, $total_slides, $plain_hits, '$title','$stripped_title', '$uploaded_on', '$tags', '$download_link', '$extension',{user_id:$user_id, user_login:'$user_login', user_location:'$user_location'}],";
	}
	}
	$json  = $json != '[' ? substr($json, 0, -1) : $json;
	$json .=']';

	echo $json;

	// Escapes the single quotes. They are deadly for JS.
	function escape($s) {

		$order   = array("\r\n", "\n", "\r");
		return str_replace($order, '', str_replace("'", "\\'", $s));
	}

?>