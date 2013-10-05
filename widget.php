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

		$this->WP_Widget('instamojo-widget', 'Instamojo',  $widget_options, $control_options);
	}

	function widget($args, $instance){
		$currency_html_map = array();
		$currency_html_map["INR"] = "&#8377;";
		$currency_html_map["USD"] = "&#36;";
		extract($args);
		$title = apply_filters('widget_title', 'Instamojo');
		echo $before_widget;
		$add_feed = (substr($instance['instamojo_url'], -1) == '/') ? "feed.json" : "/feed.json";
		$offer_array = json_decode(file_get_contents($instance['instamojo_url'] . $add_feed));
		$offer_title = $offer_array->{'offer'}->{'title'};
		$offer_description = $offer_array->{'offer'}->{'description'};
		$offer_base_price = $offer_array->{'offer'}->{'base_price'};
		$offer_currency = $offer_array->{'offer'}->{'currency'};
		echo $before_title . "Instamojo" . $after_title;
		wp_register_style('smallwidget', plugin_dir_url(__FILE__).'static/style.css');
		wp_enqueue_style('smallwidget');
		?>
		<!--<link rel="stylesheet" href="wp-content/static/style.css" type="text/css" media="all" />-->
		<div id="main-div">
			<div id="offer-title">
				<h4><?php echo $offer_title;?></h4>
			</div>
			<div id="currency-price">
				<h4><?php echo $currency_html_map[$offer_currency] . ' ' . $offer_base_price;?></h4>
			</div>
			<div id="mojo-link">
				<form action="<?php echo $instance['instamojo_url'];?>" target='_blank'>
					<input type="submit" value="BUY">
				</form>
			</div>
		</div>
		<?php
		echo $after_widget;
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['instamojo_url'] = strip_tags($new_instance['instamojo_url']);
		$instance['type'] = $new_instance['type'];
		return $instance;
	}

	function form($instance){
		$defaults = array('instamojo_url' => '', 'type' => true);
		$instance = wp_parse_args((array)$instance, $defaults);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('instamojo_url');?>">Instamojo Offer URL:</label>
			<input id="<?php echo $this->get_field_id('instamojo_url');?>" 
				name="<?php echo $this->get_field_name('instamojo_url');?>"
				value="<?php echo $instance['instamojo_url'];?>"
			style="width:100%"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Button type:'); ?></label>
			<select id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
				<option value="small" <?php if($instance['type'] == 'small') echo 'selected="selected"';?>>Small button</option>
				<option value="large" <?php if($instance['type'] == 'large') echo 'selected="selected"';?>>Large button</option>
			</select>
		</p>
		<?php 
	}
}

?>