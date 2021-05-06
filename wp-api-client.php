<?php
   /*
   Plugin Name: Query UKCH API
   Plugin URI: https://github.com/scman1
   description: >-
    Plugin to get data from UKCH API
   Version: 0.2
   Author: ANH
   Author URI: https://github.com/scman1
   License: GPL
   */
   
defined('ABSPATH') || die('unauthorised access');


// Action when user logs into admin panel
add_shortcode( 'ukch_articles', 'get_articles_data' );

function get_articles_data($atts){
	
	$defaults = [
		'title' => 'Table Title'
	];
	$atts = shortcode_atts($defaults, $atts, 'ukch_articles');
	
	$params = array(
		'page' => '20'
	);	
	
	$results = get_articles ('articles.json', $params);

	$html = "";
	$html = "<h2>" . $atts['title'] . "</h2>";
	foreach ($results as $result){
		$html .= "<p>";
		$html .= "<b>" . $result["title"] . "</b>, ";
		$html .=  $result["container_title"] . ", ";
		$html .=  $result["doi"] . ".";
		$html .= "</p>";
	}
	return $html;
}

function get_articles( $action, $params ) {
 
    $api_endpoint = "http://188.166.149.246/";
 
    if ( null == $params ) {
        $params = array();
    }
 
    // Create URL with params
    $url = $api_endpoint . $action . '?' . http_build_query($params);
 
    // Use curl to make the query
    $ch = curl_init();
 
    curl_setopt_array(
        $ch, array( 
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        )
    );
 
    $output = curl_exec($ch);
 
    // Decode output into an array
    $json_data = json_decode( $output, true );
 
    curl_close( $ch );
 
    return $json_data;
}