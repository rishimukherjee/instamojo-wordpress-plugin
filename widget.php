<?php

/**
* Instamojo Widget.
* It extends the Wordpress Widget class.
*/
class instamojo_widget extends WP_Widget{


	/**
	*	Default constructor.
	*/
	function instamojo_widget(){
		// Load the collor picker to the site.
		add_action( 'load-widgets.php', array(&$this, 'my_custom_load') );

		// Name and class of widget.
		$widget_options = array(
			'classname' => 'instamojo-widget',
			'description' => 'Display Instamojo offers in your blog.');

		// Id, width and height of the widget.
		$control_options = array(
			'id_base' => 'instamojo-widget',
			'width' => 300,
			'height' => 200);

		// Initialize the widget.
		$this->WP_Widget('instamojo-widget', 'instamojo',  $widget_options, $control_options);
	}

	/**
	*	Called in the constructor.
	*/
	function my_custom_load() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    /**
	*	Implements thw widget() function as required by wordpress.
	*	This is responsible for how the widget looks in your wordpress site.
	*/
	function widget($args, $instance){
		wp_register_style('widgetcss', plugin_dir_url(__FILE__).'static/style.css');
		wp_enqueue_style('widgetcss');

		// Holds a mapping for currency to HTML of that currency.
		$currency_html_map = array();
		$currency_html_map["INR"] = "&#8377;";
		$currency_html_map["USD"] = "&#36;";

		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		$add_feed = (substr($instance['instamojo_url'], -1) == '/') ? "feed.json" : "/feed.json";

		// Getting details from the link given in instamojo_url.
		$offer_array = json_decode(file_get_contents($instance['instamojo_url'] . $add_feed));

		$offer_title = $offer_array->{'offer'}->{'title'};
		$offer_description = $offer_array->{'offer'}->{'description'};
		$offer_base_price = $offer_array->{'offer'}->{'base_price'};
		$offer_currency = $offer_array->{'offer'}->{'currency'};
		$offer_image = $offer_array->{'offer'}->{'cover_image'};

		// If title is not given make it My Instamojo Product.
		if ($instance['title']) {
			echo $before_title . $instance['title'] . $after_title;
		}
		else{
			echo $before_title . 'My Instamojo Product' . $after_title;
		}

		$button_html = "<div><form action='".$instance['instamojo_url']."' target='_blank'><input id='wid-mojo-link' type='submit' value='BUY'></form></div>";
		// Assumes that the title is never "404 error".
		?>
		<div id="wid-small-div">
		    <?php if($instance['button_pos'] == "top") echo $button_html;?>
			<div id="wid-offer-title">
			    <?php if($instance['title'] == "404 error") echo "<h4>Error in offer URL!</h4>";
			    	  else echo "<h4>$offer_title</h4>";
			    ?>
			</div>
			<div id="wid-currency-price">
				<h4><?php echo $currency_html_map[$offer_currency] . ' ' . $offer_base_price;?></h4>
			</div>
			<?php if($instance['button_pos'] == "bottom") echo $button_html;?>
		</div>
		<script>
		    document.getElementById("wid-mojo-link").style.background = <?php echo  "\"" . $instance['button-color'] . "\""?>;
			document.getElementById("wid-small-div").style.color = <?php echo  "\"" . $instance['text-color'] . "\""?>;
			document.getElementById("wid-small-div").style.background = <?php echo  "\"" . $instance['bg-color'] . "\"" ?>;
			document.getElementById('wid-small-div').style.borderRadius = "10px";
			document.getElementById('wid-small-div').style.padding = "4px";
			document.getElementById('wid-small-div').style.textAlign = "center";
		</script>
		<?php
		echo $after_widget;
	}

	/**
	*	Implements the updatet() function as required by wordpress.
	*	This works when you fill data in the widget form input from the wordpress admin.
	*/
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$url_explode =
		$instance['button_pos'] = strip_tags($new_instance['button_pos']);
		$instance['instamojo_url'] = strip_tags($new_instance['instamojo_url']);
		$ch = curl_init($instance['instamojo_url']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_exec($ch);
		$instance['instamojo_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch);
		if(substr($instance['instamojo_url'], -1) == '/')
			$instance['instamojo_url'] = substr($instance['instamojo_url'], 0, -1);
		if($instance['instamojo_url'][0] == 'h' && $instance['instamojo_url'][5] == 's')
			$instance['instamojo_url'] = substr($instance['instamojo_url'], 9);
		if($instance['instamojo_url'][0] == 'h')
			$instance['instamojo_url'] = substr($instance['instamojo_url'], 8);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['type'] = $new_instance['type'];
		$instance['text-color'] = $new_instance['text-color'];
		$instance['button-color'] = $new_instance['button-color'];
		$instance['bg-color'] = $new_instance['bg-color'];
		$response = get_headers($instance['instamojo_url']);
		$responce_code = substr($response[0], 9, 3);
		$url_pieces = explode("/", $instance['instamojo_url']);
		if ((strpos($instance['instamojo_url'], 'www.instamojo.com') === false && $responce_code != "404") || $responce_code == "404" || count($url_pieces) != 3) {
			$instance['title'] = "404 error";
			$instance['instamojo_url'] = "#";
		}
		$instance['instamojo_url'] = 'https://' . $instance['instamojo_url'];
		return $instance;
	}

	/**
	*	Implements thw form() function as required by wordpress.
	* 	This is responsible for how the form in the wordpess admin looks.
	*/
	function form($instance){
		$defaults = array('title' => '', 'instamojo_url' => '', 'button_pos' => 'top', 'type' => true);
		$instance = wp_parse_args((array)$instance, $defaults);
		?>
		<script type='text/javascript'>
    		jQuery(document).ready(function($) {
        	$('.my-color-picker').wpColorPicker();
    		});
		</script>
		<p>
			<label for="<?php echo $this->get_field_id('title');?>">Widget Title:</label>
			<input id="<?php echo $this->get_field_id('title');?>"
				name="<?php echo $this->get_field_name('title');?>"
				value="<?php echo $instance['title'];?>"
			style="width:100%"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('instamojo_url');?>">Instamojo Offer URL:</label>
			<input id="<?php echo $this->get_field_id('instamojo_url');?>"
				name="<?php echo $this->get_field_name('instamojo_url');?>"
				value="<?php echo $instance['instamojo_url'];?>"
			style="width:100%"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('button_pos'); ?>"><?php _e('Button Position:'); ?></label>
			<select id="<?php echo $this->get_field_id('button_pos'); ?>" name="<?php echo $this->get_field_name('button_pos'); ?>">
				<option value="top" <?php if($instance['button_pos'] == 'top') echo 'selected="selected"';?>>Top</option>
				<option value="bottom" <?php if($instance['button_pos'] == 'bottom') echo 'selected="selected"';?>>Bottom</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Button type:'); ?></label>
			<select id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
				<option value="small" <?php if($instance['type'] == 'small') echo 'selected="selected"';?>>Small button</option>
				<option value="large" <?php if($instance['type'] == 'large') echo 'selected="selected"';?>>Large button</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('text-color');?>">Text Color:</label>
			<input class="my-color-picker" id="<?php echo $this->get_field_id('text-color');?>"
				name="<?php echo $this->get_field_name('text-color');?>"
				value="<?php echo $instance['text-color'];?>"
			style="width:100%"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('bg-color');?>">Background Color:</label>
			<input class="my-color-picker" id="<?php echo $this->get_field_id('bg-color');?>"
				name="<?php echo $this->get_field_name('bg-color');?>"
				value="<?php echo $instance['bg-color'];?>"
			style="width:100%"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('button-color');?>">Button Color:</label>
			<input class="my-color-picker" id="<?php echo $this->get_field_id('button-color');?>"
				name="<?php echo $this->get_field_name('button-color');?>"
				value="<?php echo $instance['button-color'];?>"
			style="width:100%"/>
		</p>
		<?php
	}
}
?>
