<?php

/**
* Instamojo Widget.
*/
class instamojo_widget extends WP_Widget{


	function instamojo_widget(){
		
		$widget_options = array(
			'classname' => 'instamojo-widget',
			'description' => 'Display Instamojo offers in your blog.');

		$control_options = array(
			'id_base' => 'instamojo-widget',
			'width' => 300,
			'height' => 200);

		$this->WP_Widget('instamojo-widget', 'instamojo',  $widget_options, $control_options);
	}

	function widget($args, $instance){
		wp_register_style('widgetcss', plugin_dir_url(__FILE__).'static/style.css');
		wp_enqueue_style('widgetcss');
		$currency_html_map = array();
		$currency_html_map["INR"] = "&#8377;";
		$currency_html_map["USD"] = "&#36;";
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		$add_feed = (substr($instance['instamojo_url'], -1) == '/') ? "feed.json" : "/feed.json";
		$offer_array = json_decode(file_get_contents($instance['instamojo_url'] . $add_feed));
		$offer_title = $offer_array->{'offer'}->{'title'};
		$offer_description = $offer_array->{'offer'}->{'description'};
		$offer_base_price = $offer_array->{'offer'}->{'base_price'};
		$offer_currency = $offer_array->{'offer'}->{'currency'};
		$offer_image = $offer_array->{'offer'}->{'cover_image'};
		if ($instance['title']) {
			echo $before_title . $instance['title'] . $after_title;
		}
		else{
			echo $before_title . 'My Instamojo Product' . $after_title;
		}
		$button_html = "<div id='mojo-link'><form action='".$instance['instamojo_url']."' target='_blank'><input type='submit' value='BUY'></form></div>";
		/*wp_register_script('color-script', plugin_dir_url(__FILE__).'scripts/widget.js');
		wp_enqueue_script('color-script');
		$data = array("text_color" => $instance['text-color'], "bg_color" => $instance['bg-color'], "button_color" => $instance['button-color']);
		wp_localize_script('color-script', 'php_data', $data);
		*/
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
			document.getElementById("wid-small-div").style.color = <?php echo  "\"#" . $instance['text-color'] . "\""?>;
			document.getElementById("wid-small-div").style.background = <?php echo  "\"#" . $instance['bg-color'] . "\"" ?>;
			document.getElementById('wid-small-div').style.borderRadius = "10px";
			document.getElementById('wid-small-div').style.padding = "4px";
			document.getElementById('wid-small-div').style.textAlign = "center";
		</script>
		<?php
		echo $after_widget;
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		if(substr($instance['instamojo_url'], -1) == '/')
			$instance['instamojo_url'] = substr($instance['instamojo_url'], 0, -1);
		$instance['button_pos'] = strip_tags($new_instance['button_pos']);
		$instance['instamojo_url'] = strip_tags($new_instance['instamojo_url']);
		$ch = curl_init($instance['instamojo_url']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_exec($ch);
		$instance['instamojo_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['type'] = $new_instance['type'];
		$instance['text-color'] = $new_instance['text-color'];
		$instance['button-color'] = $new_instance['button-color'];
		$instance['bg-color'] = $new_instance['bg-color'];
		$response = get_headers($instance['instamojo_url']);
		$responce_code = substr($response[0], 9, 3);
		if ((strpos($instance['instamojo_url'], 'www.instamojo.com') === false && $responce_code != "404") || $responce_code == "404") {
			$instance['title'] = "404 error";
			$instance['instamojo_url'] = "#";
		}
		return $instance;
	}

	function form($instance){
		$defaults = array('instamojo_url' => '', 'type' => true);
		$instance = wp_parse_args((array)$instance, $defaults);
		?>
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
			<input class="color" id="<?php echo $this->get_field_id('text-color');?>" 
				name="<?php echo $this->get_field_name('text-color');?>"
				value="<?php echo $instance['text-color'];?>"
			style="width:100%"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('bg-color');?>">Background Color:</label>
			<input class="color" id="<?php echo $this->get_field_id('bg-color');?>" 
				name="<?php echo $this->get_field_name('bg-color');?>"
				value="<?php echo $instance['bg-color'];?>"
			style="width:100%"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('button-color');?>">Button Color:</label>
			<input class="color" id="<?php echo $this->get_field_id('button-color');?>" 
				name="<?php echo $this->get_field_name('button-color');?>"
				value="<?php echo $instance['button-color'];?>"
			style="width:100%"/>
		</p>
		<?php
	}
}


add_action('admin_enqueue_scripts', 'pw_load_scripts');

function pw_load_scripts() {
    wp_register_script('custom-js', plugin_dir_url(__FILE__).'scripts/jscolor.js');
	wp_enqueue_script('custom-js');
}
?>