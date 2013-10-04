<?php

class instamojo_shortcode(){

	function __construct(){
		add_shortcode('instamojo', array(&$this, 'add_short'));
	}

	function get_details($username, $offer){

	}

	function add_short($atts){
		extract(shortcode_atts( array(
				'username' => '',
				'offer' => ''
			), $atts));

		//$offer_details = get_details($username, $offer);
		return '<h1>Dude</h1>';
	}
}

?>