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
				'button_pos' => 'bottom',
				'topic_color' => '000000',
				'button_color' => 'b8e0ff',
				'bg_color' => '1ff4be',
				'description_color' => '000000',
				'price_color' => '000000',
			), $atts);
		$atts['instamojo_url'] = substr($atts['instamojo_url'], 0, -1);
		$add_feed = (substr($atts['instamojo_url'], -1) == '/') ? "feed.json" : "/feed.json";
		$offer_array = json_decode(file_get_contents($atts['instamojo_url'] . $add_feed));
		$offer_data = array();
		$offer_data["title"] = $offer_array->{'offer'}->{'title'};
		$offer_data["description"] = $offer_array->{'offer'}->{'description'};
		$offer_data["base_price"] = $offer_array->{'offer'}->{'base_price'};
		$offer_data["url"] = $atts['instamojo_url'];
		$offer_data["cover_image"] = $offer_array->{'offer'}->{'cover_image'};
		$offer_data["button_pos"] = $atts["button_pos"];
		$offer_data["topic_color"] = $atts["topic_color"];
		$offer_data["description_color"] = $atts["description_color"];
		$offer_data["button_color"] = $atts["button_color"];
		$offer_data["bg_color"] = $atts["bg_color"];
		$offer_data["price_color"] = $atts["price_color"];
		$offer_currency_html = $currency_html_map[$offer_array->{'offer'}->{'currency'}];
		$response = get_headers($atts['instamojo_url']);
		$responce_code = substr($response[0], 9, 3);
		if(substr($atts['instamojo_url'], -1) == '/')
			$atts['instamojo_url'] = substr($atts['instamojo_url'], 0, -1);
		if ((strpos($atts['instamojo_url'], 'www.instamojo.com') === false && $responce_code != "404") || $responce_code == "404") {
			$offer_data['title'] = "404 error";
			$offer_data['url'] = "#";
			$offer_data['description'] = "";
			$offer_data['cover_image'] = "";
		}
		if($atts['type'] == 'small') return $this->small_html($offer_currency_html, $offer_data);
		return $this->large_html($offer_currency_html, $offer_data);
	}

	function change_js($data){
		wp_register_script('short-color-script', plugin_dir_url(__FILE__) . 'scripts/shortcode.js');
		wp_enqueue_script('short-color-script');
		wp_localize_script('short-color-script', 'short_php_data', $data);
	}

	function small_html($currency_html, $data){
		$button_html = "<div id='mojo-link'><form action='".$data['url']."' target='_blank'><input type='submit' value='BUY'></form></div>";
		$html = "";
		$html .= "<div id='short-small-div'>";
			if($data["button_pos"]=="top") $html .= $button_html;
			$html .= "<div id='short-offer-title'>";
				$html .= "<h4>".$data['title']."</h4>";
			$html .= "</div>";
			$html .= "<div id='short-currency-price'>";
				$html .= "<h4>".$currency_html." ".$data['base_price']."</h4>";
			$html .= "</div>";
			if($data["button_pos"]=="bottom") $html .= $button_html;
		$html .= '</div>';
		$html .= "<script>document.getElementById('short-offer-title').style.color = " . '"#' . $data['topic_color'] . '";';
		$html .= "document.getElementById('short-small-div').style.background = " . '"#' . $data['bg_color'] . '";';
		$html .= "document.getElementById('short-small-div').style.borderRadius = " . '"10px";';
		$html .= "document.getElementById('short-small-div').style.padding = " . '"4px";';
		$html .= "document.getElementById('short-small-div').style.width = " . '"180px";';
		$html .= "document.getElementById('short-small-div').style.textAlign = " . '"center";</script>';
		return $html;
	}

	function large_html($currency_html, $data){
		$button_html = "<div id='mojo-link'><form action='".$data['url']."' target='_blank'><input type='submit' value='BUY'></form></div>";
		$html = "";
		$html .= "<div id='short-large-div'>";
			if($data["button_pos"]=="top") $html .= $button_html;
			$html .= "<div id='short-large-offer-title'>";
				$html .= "<h5>".$data['title']."</h5>";
			$html .= "</div>";
			$html .= "<div id='short-cover-image'>";
				$html .= "<img src=".$data["cover_image"]."></img>";
			$html .= "</div>";
			$html .= "<div id='short-description'>";
				$html .= "<h6>" . $data['description'] . "</h6>";
			$html .= "</div>";
			$html .= "<div id='short-currency-price'>";
				$html .= "<h5>" . $currency_html . " " . $data['base_price'] . "</h5>";
			$html .= "</div>";
			if($data["button_pos"]=="bottom") $html .= $button_html;
		$html .= '</div>';
		$html .= "<script>document.getElementById('short-large-offer-title').style.color = " . '"#' . $data['topic_color'] . '";';
		$html .= "document.getElementById('short-description').style.color = " . '"#' . $data['description_color'] . '";';
		$html .= "document.getElementById('short-large-div').style.background = " . '"#' . $data['bg_color'] . '";';
		$html .= "document.getElementById('short-large-div').style.borderRadius = " . '"10px";';
		$html .= "document.getElementById('short-large-div').style.padding = " . '"4px";';
		$html .= "document.getElementById('short-large-div').style.textAlign = " . '"center";</script>';
		return $html;
	}
}

?>