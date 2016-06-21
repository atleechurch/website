<?php
/*
Name: DIY WP Related Posts
Author: DIYWPBlog.com
Description: Add related Posts with thumbnails.
Version: 1.0.3
Class: diywp_related_posts_box
*/

class diywp_related_posts_box extends thesis_box {

   protected function translate() {
     $this->title = __('DIYWP Related Posts', 'diywp');
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
      require_once( dirname(__FILE__) . '/lib/related_posts_resizer.php'); 
   }

   protected function options() {
     global $thesis;
     return array(
       'class' => array(
               'type' => 'text',
               'width' => 'medium',
               'code' => true,
               'label' => $thesis->api->strings['html_class'],
               'tooltip' => sprintf('%s %s', $thesis->api->strings['class_tooltip'], $thesis->api->strings['class_note']),
               'placeholder' => __('related-posts', 'diywp')),
       'title' => array(
               'type' => 'text',
               'width' => 'medium',
               'label' => __('Title', 'diywp'),
               'tooltip' => __('Title for your related posts.', 'diywp'),
               'placeholder' => __('Related Posts', 'diywp')),
       'number_posts' => array(
               'type' => 'text',
               'width' => 'tiny',
               'label' => __('Number of Related Posts', 'diywp'),
               'tooltip' => __('Enter the number of Related posts to display.', 'diywp'),
               'placeholder' => __('4', 'diywp')),            
       'img_width' => array (
               'type' => 'text',
               'width' => 'tiny',
               'label' => __('Width of the Featured Image', 'diywp'),
               'tooltip' => __('This will set the width for your Featured images and is intended to maintain optimized images in terms of dimensions.', 'diywp'),
               'placeholder' => __('120', 'diywp')),
       'img_height' => array (
               'type' => 'text',
               'width' => 'tiny',
               'label' => __('Height of the Featured Image.', 'diywp'),
               'tooltip' => __('This will set the height for your Featured images and is intended to maintain optimized images in terms of dimensions.', 'diywp'),
               'placeholder' => __('120', 'diywp')),
       'title_length' => array(
	       'type' => 'text',
	       'width' => 'tiny',
	       'label' => 'Title max number of words',
	       'tooltip' => __('Enter the URL for your image.', 'diywp'),
	       'placeholder' => '16'));
        }

        public function html($args = false) {
            extract($args = is_array($args) ? $args : array());
            $tab = str_repeat("\t", !empty($depth) ? $depth : 0);
            global $thesis, $posts, $post;

            $img_width = !empty($this->options['img_width']) && is_numeric($this->options["img_width"]) ? $this->options['img_width'] : '120';
            $img_height = !empty($this->options['img_height']) && is_numeric($this->options["img_height"]) ? $this->options['img_height'] : '120';
            $number_posts = !empty($this->options['number_posts']) && is_numeric($this->options["number_posts"]) ? $this->options['number_posts'] : '4';
            $title = !empty($this->options['title']) ? $this->options['title'] : 'Related Posts';
            $class = !empty($this->options['class']) ? $this->options['class'] : 'related-posts';
            $title_length = !empty($this->options['title_length']) ? $this->options['title_length'] : '16';
            //Get the categories related to the post.
            $orig_post = $post;

               $categories = get_the_category($post->ID);
               if ($categories) {
               $category_ids = array();
               foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;

            $argz = array(
              'category__in' => $category_ids,
              'post__not_in' => array($post->ID),
              'posts_per_page'=> esc_attr($number_posts), // Number of related posts that will be shown.
              'ignore_sticky_posts' => 1
            );
            
            $my_query = new wp_query($argz);
            if( $my_query->have_posts()) {
                echo '<div class="' . esc_attr($class) . '"><h3>' . esc_attr($title) . '</h3><ul>';  
            while ($my_query->have_posts()) {
            $my_query->the_post();
            //Auto image resizing setup
            $thumb = get_post_thumbnail_id();
            $img_url = wp_get_attachment_url($thumb);
            $image = related_posts_resizer( $img_url, $img_width, $img_height, true );
            // Catch the Image defaults
            $fetch_img_url = fetch_that_image( $thumb,'full' );
            $fetch_image = related_posts_resizer( $fetch_img_url, $img_width, $img_height, true );
            
            
             echo
                 "$tab<li>\n";
             if(has_post_thumbnail()) {  
             echo
                 "$tab<div class=\"" . esc_attr($class) . "-thumb\">\n".
                 "$tab<a href=\"" . get_permalink() . "\" rel=\"bookmark\" title=\"" . the_title_attribute( 'echo=0' ) . "\">\n".
                 "$tab<img src=\"" . $image . "\" width=\"" . esc_attr($img_width) . "\" height=\"" . esc_attr($img_height) . "\" alt=\"" . get_the_title() . "\"/></a>\n".
                 "$tab</div>\n";
                 }
             elseif(fetch_that_image()){ 
             echo
                  "$tab<div class=\"" . esc_attr($class) . "-thumb\">\n".
                  "$tab<a href=\"" . get_permalink() . "\" title=\"" . get_the_title() . "\">\n".
                  "$tab<img src=\"" . $fetch_image . "\" width=\"" . esc_attr($img_width) . "\" height=\"" . esc_attr($img_height) . "\" alt=\"" . get_the_title() . "\"></a>\n".
                  "$tab</div>\n";  
                 }  
             echo
                 "$tab<div class=\"" . esc_attr($class) . "-title\"><a href=\"" . get_permalink() . "\" rel=\"bookmark\" title=\"" . the_title_attribute( 'echo=0' ) . "\">" . related_post_title('...', $title_length) . "</a></div>\n";
                
             }
             echo '</li></ul></div>'; 
         } 
       }
     wp_reset_query();
   }
}
function related_post_title($after = '', $length) {
           $mytitle = explode(' ', get_the_title(), $length);
           if (count($mytitle)>=$length) {
               array_pop($mytitle);
               $mytitle = implode(" ",$mytitle). $after;
           } else {
               $mytitle = implode(" ",$mytitle);
           }
               return $mytitle;
        }

function fetch_that_image() {
        global $post, $posts;
        $first_img = '';
        ob_start();
        ob_end_clean();
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        if(count($matches [1]))$first_img = $matches [1] [0];
        return $first_img;
}