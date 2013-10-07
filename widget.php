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
		if ($instance['title']) {
			echo $before_title . $instance['title'] . $after_title;
		}
		else{
			echo $before_title . 'My Instamojo Product' . $after_title;
		}
		wp_register_style('widgetcss', plugin_dir_url(__FILE__).'static/style.css');
		wp_enqueue_style('widgetcss');
		$button_html = "<div id='mojo-link'><form action='".$instance['instamojo_url']."' target='_blank'><input type='submit' value='BUY'></form></div>";
		?>
		<div id="small-div">
		    <?php if($instance['button_pos'] == "top") echo $button_html;?>
			<div id="offer-title">
				<h4><?php echo $offer_title;?></h4>
			</div>
			<div id="currency-price">
				<h4><?php echo $currency_html_map[$offer_currency] . ' ' . $offer_base_price;?></h4>
			</div>
			<?php if($instance['button_pos'] == "bottom") echo $button_html;?>
		</div>
		<?php
		echo $after_widget;
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['button_pos'] = strip_tags($new_instance['button_pos']);
		$instance['instamojo_url'] = strip_tags($new_instance['instamojo_url']);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['type'] = $new_instance['type'];
		$instance['text-color'] = $new_instance['text-color'];
		$instance['button-color'] = $new_instance['button-color'];
		$instance['bg-color'] = $new_instance['bg-color'];
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