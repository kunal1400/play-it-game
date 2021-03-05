<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/kunal1400
 * @since      1.0.0
 *
 * @package    Play_It_Game
 * @subpackage Play_It_Game/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h1>Your Plugin Page Title</h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'play_it_game_settings' ); ?>
    	<?php do_settings_sections( 'play_it_game_settings' ); ?>
    	<table class="form-table">
	        <tr valign="top">
	        	<th scope="row">All Games Page</th>
	        	<td>
	        		<?php 
		        		$allPages = get_pages(array('child_of' => 0, 'echo' => false));
		        		$all_games_page_id = get_option('all_games_page');
		        		echo '<select name="all_games_page">';
						if ( is_array($allPages) && count($allPages) > 0 ) {
							foreach ($allPages as $i => $page) {
								if ( $all_games_page_id == $page->ID ) {
									echo '<option selected value="'.$page->ID.'">'.$page->post_title.'</option>';
								}
								else {									
									echo '<option value="'.$page->ID.'">'.$page->post_title.'</option>';
								}
							}
						}
		        		echo '</select>';
					?>
	        	</td>
	        </tr>	         
	        <tr valign="top">
	        	<th scope="row">After Login Redirect Page</th>
	        	<td>
	        		<?php 
		        		$allPages = get_pages(array('child_of' => 0, 'echo' => false));
		        		$after_login_page_id = get_option('after_login_redirect');		        		
		        		echo '<select name="after_login_redirect">';
						if ( is_array($allPages) && count($allPages) > 0 ) {
							foreach ($allPages as $i => $page) {
								if ( $after_login_page_id == $page->ID ) {
									echo '<option selected value="'.$page->ID.'">'.$page->post_title.'</option>';
								}
								else {									
									echo '<option value="'.$page->ID.'">'.$page->post_title.'</option>';
								}
							}
						}
		        		echo '</select>';
					?>
	        	</td>
	        </tr>	        
	    </table>
		<?php submit_button(); ?>
	</form>
</div>