<?php
/*
Name: WP Featured Image - Linked
Author: Girlie
Description: WP Featured Image Linked to Post
Version: 1.0
Class: thesis_linked_wp_featured_image
*/

class thesis_linked_wp_featured_image extends thesis_box {
	protected function translate() {
		global $thesis;
		$this->title = sprintf(__('%s Featured Image - Linked', 'thesis'), $thesis->api->base['wp']);
	}

	protected function construct() {
		add_theme_support('post-thumbnails');
	}

	public function html() {
		echo '<a href="' . get_permalink() . '" title="' . get_the_title() . '">';
		echo get_the_post_thumbnail();
		echo '</a>';
	}
}
