<?php

/**
* Instamojo Shortcode
*/
class instamojo_shortcode{
	
	function __construct(){
		add_shortcode( 'instamojo', array( &$this, 'add_short' ) );
	}

	function add_short($atts, $content){
		$currency_html_map = array();
		$currency_html_map["INR"] = "&#8377;";
		$currency_html_map["USD"] = "&#36;";
		$atts = shortcode_atts( array(
				'instamojo_url' => '',
				'type' => 'small'
			), $atts);
		$add_feed = (substr($atts['instamojo_url'], -1) == '/') ? "feed.json" : "/feed.json";
		$offer_array = json_decode(file_get_contents($atts['instamojo_url'] . $add_feed));
		$offer_title = $offer_array->{'offer'}->{'title'};
		$offer_description = $offer_array->{'offer'}->{'description'};
		$offer_base_price = $offer_array->{'offer'}->{'base_price'};
		$offer_currency = $offer_array->{'offer'}->{'currency'};
		if($atts['type'] == 'small') return $this->small_html($offer_title, $offer_base_price, $currency_html_map[$offer_currency]);
		return "<h1>$type</h1>";
	}

	function small_html($offer_title, $offer_base_price, $currency_html, $offer_url){
		$html = "";
		$html .= "<div id='small-div'>";
		$html .= "<div id='offer-title'>";
		$html .= "<h4>$offer_title</h4>";
		$html .= "</div>";
		$html .= "<div id='currency-price'>";
		$html .= "<h4>$currency_html $offer_base_price</h4>";
		$html .= "</div>";
		$html .= "<div id='mojo-link'>";
		$html .= "<form action=$offer_url target='_blank'>";
		$html .= "<input type='submit' value='BUY'>";
		$html .= '</form>';
		$html .= '</div>';
		$html .= '</div>';
		return $html;
	}
}

?>