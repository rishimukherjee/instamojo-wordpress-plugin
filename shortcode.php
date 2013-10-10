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
				'type' => 'small',
				'button-pos' => 'bottom',
				'text-color' => '7640C7',
				'button-color' => 'b8e0ff',
				'bg-color' => '#0570c2'
			), $atts);
		$add_feed = (substr($atts['instamojo_url'], -1) == '/') ? "feed.json" : "/feed.json";
		$offer_array = json_decode(file_get_contents($atts['instamojo_url'] . $add_feed));
		$offer_data = array();
		$offer_data["title"] = $offer_array->{'offer'}->{'title'};
		$offer_data["description"] = $offer_array->{'offer'}->{'description'};
		$offer_data["base-price"] = $offer_array->{'offer'}->{'base_price'};
		$offer_data["url"] = $atts['instamojo_url'];
		$offer_data["cover-image"] = $offer_array->{'offer'}->{'cover_image'};
		$offer_data["button-pos"] = $atts["button-pos"];
		$offer_data["text-color"] = $atts["text-color"];
		$offer_data["button-color"] = $atts["button-color"];
		$offer_data["bg-color"] = $atts["bg-color"];
		$offer_currency_html = $currency_html_map[$offer_array->{'offer'}->{'currency'}];
		if($atts['type'] == 'small') return $this->small_html($offer_currency_html, $offer_data);
		return $this->large_html($offer_currency_html, $offer_data);
	}

	function change_js($data){
		wp_register_script('short-color-script', plugin_dir_url(__FILE__).'scripts/custom.js');
		wp_enqueue_script('short-color-script');
		wp_localize_script('short-color-script', 'short_php_data', $data);
	}

	function small_html($currency_html, $data){
		//$this->change_js($data);
		$button_html = "<div id='short-mojo-link'><form action='".$data['url']."' target='_blank'><input type='submit' value='BUY'></form></div>";
		$html = "";
		$html .= "<div id='short-small-div'>";
		if($data["button-pos"]=="top") $html .= $button_html;
			$html .= "<div id='short-offer-title'>";
				$html .= "<h4>".$data['title']."</h4>";
			$html .= "</div>";
			$html .= "<div id='short-currency-price'>";
				$html .= "<h4>".$currency_html." ".$data['base-price']."</h4>";
			$html .= "</div>";
			if($data["button-pos"]=="bottom") $html .= $button_html;
		$html .= '</div>';
		$html .= "<script>document.getElementById('short-small-div').style.color = " . '"#' . $data['text-color'] . '"' . ";</script>";
		return $html;
	}

	function large_html($data){
		$html = "";
		return $html;
	}
}

?>