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
	<h1>How to Setup a Game</h1>
	<ol>
		<li>For social login we use Elementor Add On, so setup your own Google/Facebook credentials</li>
		<li>
			<p><b>How to create All Games Page?</b>
			<ol>
				<li>Add this shortcode <i>[all_games]</i> to All Games page</li>
			</ol>
		</p>
		<li><p><b>How to create a Game Home Page?</b></p>
			<ol>
				<li><p>Add a Shortcode <i>[create_team form_heading="Create TEAM:"]</i> in editor. This will show the create team form.</p></li>
				<li><p>Add a Shortcode <i>[team_score_position]</i> in editor. This will promt users to loged in and after login it will show all teams associated with this game, create team form and start playing button.</p></li>
				<li><p>Create a custom fields:</p>
					<ol>
						<li><p><i>is_game_home_page</i> and set it to <i>true</i></p></li>
						<li><p><i>score_multipler</i> and set any value, if no value is set then 10 will be default</p></li>
					</ol>
				</li>
			</ol>
		</li>
		<li><p><b>How to create Game Levels?</b></p>
			<ol>
				<li><p>Shortcode for timer: <i>[show_timer hours_label="HOURS" minutes_label="MINUTES" seconds_label="SECONDS" hours_background_color="#cccccc9e" minutes_background_color="#cccccc9e" seconds_background_color="#cccccc9e" hours_font_color="#FFF" minutes_font_color="#FFF" seconds_font_color="#FFF"]</i></p></li>
				<li><p>Shortcode for next step: <i>[next_step_form answer=""]</i></p></li>
				<li><p>Shortcode for clue: <i>[show_clue seconds_to_add="50" image_url="" text=""]</i></p></li>
			</ol>
		</li>
	</ol>
	<hr/>
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
	        <tr valign="top">
	        	<th scope="row">Default Games Image</th>
	        	<td>
	        		<input type="text" name="default_game_images" value="<?php echo get_option('default_game_images') ?>">	        		
	        	</td>
	        </tr>	        
	    </table>
		<?php submit_button(); ?>
	</form>
</div>