<?php
/*
	Name: UberMenu Thesis Box
	Author: Chris Mavricos
	Description: Adds support for UberMenu in Thesis 2.0.  Thanks to Tim Milligan
	Version: 1.0
	Class: ubermenu_thesis_nav_menu
*/

class ubermenu_thesis_nav_menu extends thesis_box {
	protected function translate() {
		$this->title = __('UberMenu Nav Menu', 'thesis');
		$this->name = __('ubermenu_thesis_nav_menu', 'thesis');
	}
	
	protected function construct() {
		register_nav_menu( 'ubermenu_thesis' , __( 'UberMenu Thesis' ) );
	}

	public function html($depth) {
		wp_nav_menu( array( 'theme_location' => 'ubermenu_thesis' ) );
	}
}