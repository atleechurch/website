<?php
/*
Plugin Name: WordPress Remove Old Slugs
Plugin URI: http://www.algoritmika.com/shop/wordpress-remove-old-slugs-plugin/
Description: Plugin removes old slugs/permalinks from database.
Version: 1.0.1
Author: Algoritmika Ltd.
Author URI: http://www.algoritmika.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
?>
<?php
if ( ! class_exists( 'WROS_plugin' ) ) {
	class WROS_plugin{	
	
		public function __construct(){
			if(is_admin()){
				add_action('admin_menu', array($this, 'add_plugin_options_page'));
			}
		}
		
		public function add_plugin_options_page(){
			add_submenu_page('tools.php', 'Remove Old Slugs Plugin', 'Remove Old Slugs', 'manage_options', 'wros-settings-admin', array($this, 'create_admin_page'));
		}

		public function create_admin_page(){
			?>
		<div class="wrap">
			<h2>WordPress Remove Old Slugs Plugin</h2>
			<p>This tool removes old slugs/permalinks from database. Press Start to proceed.</p>
			<form method="post" action="">
				<input type='submit' name='removeOldSlugs' value='Start'/>
			</form>			
			<p><?php 
			global $wpdb;				
			$myResOld = $wpdb->get_results("SELECT * FROM wp_postmeta WHERE meta_key = '_wp_old_slug'");				
			$numOldSlugs = count($myResOld);
			if ($numOldSlugs > 0)
			{
				?><strong><?=$numOldSlugs?></strong> old slug(s) found:<br /><?php
				//print_r($myResOld);
				$i = 0;
				foreach ($myResOld as $oldSlugObj)
				{
					$i++;
					echo $i.') '.$oldSlugObj->meta_value.' (post_id = '.$oldSlugObj->post_id.')<br />';
				}
				if (isset($_POST['removeOldSlugs']))
				{					
					$myRes = $wpdb->get_results( "DELETE FROM wp_postmeta WHERE meta_key = '_wp_old_slug'" );
					$myResOld1 = $wpdb->get_results("SELECT * FROM wp_postmeta WHERE meta_key = '_wp_old_slug'");				
					$numOldSlugs1 = count($myResOld1);
					?><br /><strong>Removing old slugs from database finished! <?=($numOldSlugs-$numOldSlugs1)?> old slug(s) deleted.</strong><?php
				}
			}
			else
			{
				?>No old slugs found in database.<?php
			}
			?></p>
			<?php
				$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') 
						=== FALSE ? 'http' : 'https';
				$host     = $_SERVER['HTTP_HOST'];
				$script   = $_SERVER['SCRIPT_NAME'];
				$params   = $_SERVER['QUERY_STRING'];
				 
				$currentUrl = $protocol . '://' . $host . $script . '?' . $params;	
				
				echo '<p><a href="'.$currentUrl.'">Refresh list</a></p>';
			?>
			<div id='setting-error-settings_updated' class='updated settings-error'>
			<p>If You like my plugin and find it useful - please buy me a coffee :) <a href="http://www.algoritmika.com/donate/">Donate</a>.</p>
			</div>
		</div>
		<?php
		}
	}
}

$WROS_plugin = &new WROS_plugin();