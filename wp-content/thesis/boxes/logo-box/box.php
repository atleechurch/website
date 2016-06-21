<?php
/*
Name: Simple Logo Box
Author: DIYWPBlog.com
Description: Simple Box to Add a Logo
Version: 1.0.4
Class: diywp_logo_box
*/

class diywp_logo_box extends thesis_box {

    protected function translate() {
        $this->title = __('Logo', 'diywp');
        $this->name = $this->title;
        }
        
    protected function construct() {
     	global $diywp_update;
     	     	if(is_admin()) {
     	     	if(!isset($diywp_ah)) { // Check if the Asset Handler has already been created once, no point in creating the same asset handler multiple times.
     	     	if(!class_exists('diywp_asset_handler')) // Load the asset handler class if it hasn't been already.
     	     	require_once( dirname(__FILE__) . '/diywp_asset_handler.php');

     	     	$diywp_update = new diywp_asset_handler;
             }
        }
    }

    protected function options() {
      global $thesis;
      return array(
      'class' => array(
              'type' => 'text',
              'width' => 'medium',
              'code' => true,
              'label' => $thesis->api->strings['html_class'],
              'tooltip' => sprintf('%s %s', $thesis->api->strings['class_tooltip'], $thesis->api->strings['class_note'])),
       'url_to' => array(
              'type' => 'text',
              'width' => 'long',
              'label' => sprintf(__('Destination %s', 'diywp'), $thesis->api->base['url']),
              'tooltip' => __('Enter the URL where you would like the logo to take you if clicked. If nothing is entered it will default to your site URL.', 'diywp')),
       'logo_img' => array(
              'type' => 'text',
              'width' => 'long',
              'label' => $this->title,
              'tooltip' => __('Enter the URL for your image.', 'diywp')),
       'width' => array(
              'type' => 'text',
              'width' => 'tiny',
              'label' => __('Width', 'diywp'),
       'tooltip' => __('Enter the Width of the image, just enter the digit. (px) will be added automatically.', 'diywp')),
              'height' => array(
              'type' => 'text',
              'width' => 'tiny',
              'label' => __('Height', 'diywp'),
              'tooltip' => __('Enter the Height of the image, just enter the digit. (px) will be added automatically.', 'diywp')),
       'alt_att' => array(
              'type' => 'text',
              'width' => 'medium',
              'label' => __('Alt', 'diywp'),
              'tooltip' => __('The alt attribute is required to correctly validate an image.', 'diywp')));
        }

    public function html($args = false) {
              extract($args = is_array($args) ? $args : array());
              $tab = str_repeat("\t", !empty($depth) ? $depth : 0);

              // Defaults
              $alt_att = !empty($this->options['alt_att']) ? $this->options['alt_att'] : 'logo';
              $height = !empty($this->options['height']) && is_numeric($this->options["height"]) ? $this->options['height'] : '';
              $width = !empty($this->options['width']) && is_numeric($this->options["width"]) ? $this->options['width'] : '';
              $logo_img = !empty($this->options['logo_img']) ? $this->options['logo_img'] : '';
              $url_to = !empty($this->options['url_to']) ? $this->options['url_to'] : site_url();
              $class = !empty($this->options['class']) ? $this->options['class'] : 'logo';

              // output
              printf('%s<div class="%s"><a href="%s"><img src="%s" width="%s" height="%s" alt="%s"/></a></div>', $tab, esc_attr($class), esc_url($url_to), esc_url($logo_img), esc_attr($width), esc_attr($height), esc_attr($alt_att));
              }
}