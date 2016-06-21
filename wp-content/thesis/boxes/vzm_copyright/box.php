<?php
/*
	Name: VZM Copyright Box
	Author: Tim Milligan
	Description: Adds a customizable copyright box to the skin editor.
	Version: 1.3
	Class: vzm_copyright
*/

class vzm_copyright extends thesis_box {
	protected function translate() {
		$this->title = __('Copyright', 'vzm');
	}

	protected function options() {
		global $thesis;
		return array(
			'name' => array(
				'type' => 'text',
				'width' => 'medium',
				'label' => __('Name', 'vzm')
			),
			'url' => array(
				'type' => 'text',
				'label' => __('URL (leave blank for none)', 'vzm'),
				'width' => 'medium'
			),
			'new-window' => array(
				'type' => 'checkbox',
				'options' => array(
					'yes' => __('Open Link in New Window', 'vzm')
				)
			),
			'start_date' => array(
				'type' => 'text',
				'width' => 'tiny',
				'label' => __('Copyright Start Date (leave blank for none)', 'vzm')
			),
			'class' => array(
				'type' => 'text',
				'width' => 'medium',
				'code' => true,
				'label' => $thesis->api->strings['html_class'],
				'tooltip' => $thesis->api->strings['class_tooltip'] . $thesis->api->strings['class_note']),
		);
	}

	public function html($args = false) {
		extract($args = is_array($args) ? $args : array());
		$tab = str_repeat("\t", !empty($depth) ? $depth : 0);
		
		$name = $this->options['name'];
		$url = $this->options['url'];
		$new_window = $this->options['new-window'];
		$start_date = $this->options['start_date'];
		$class = $this->options['class'];
		
		echo
			$tab .
			sprintf(__("<p%s>Copyright &copy; %s%d %s%s%s. All Rights Reserved."), ($class ? ' class="' . $class . '"' : ''), ($start_date ? $start_date . ' &ndash; ' : ''), date('Y'), (!empty($url) ? '<a href="' . $url . '"' . ($new_window['yes'] ? ' target="_blank">' : '>') : ''), $name, (!empty($url) ? '</a>' : '')) . "</p>\n";
	}
}
