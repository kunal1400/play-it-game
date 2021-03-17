<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/kunal1400
 * @since      1.0.0
 *
 * @package    Play_It_Game
 * @subpackage Play_It_Game/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Play_It_Game
 * @subpackage Play_It_Game/admin
 * @author     Kunal Malviya <lucky.kunalmalviya@gmail.com>
 */
class Play_It_Game_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Play_It_Game_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Play_It_Game_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/play-it-game-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Play_It_Game_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Play_It_Game_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/play-it-game-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function login_redirect_cb() {
		$user = wp_get_current_user();		
		if ( in_array( 'subscriber', (array) $user->roles ) ) {
			return home_url( 'all-games' );
		}
		else {
			return site_url();
		}
	}

	public function init_action_cb() {
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => 'sanitize_text_field',
            'default' => NULL,
        );
    	register_setting( 'play_it_game_settings', 'all_games_page', $args ); 
    	register_setting( 'play_it_game_settings', 'after_login_redirect', $args ); 
	}

	/**
	* This function is rendering the settings page of plugin
	**/
	public function ph_infinite_add_menu() {
		$menu_slug 	= 'playit_games_settings';
		$menu_name 	= 'Play-It Settings';
		$menu_main 	= 'Play-It';

		add_menu_page($menu_name, $menu_main, 'manage_options', $menu_slug, '',plugin_dir_url(__FILE__).'assets/img/logo-wp.png', 57);	

		add_submenu_page( $menu_slug, $menu_name, $menu_main, 'manage_options', $menu_slug, array($this, 'infi_settings'));
	}

	public function infi_settings() {
		include "partials/play-it-game-admin-display.php";
	}

	public function getLevelInfo( $teamId, $levelId, $userId ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_games';
		$sql = "SELECT * FROM $tblname WHERE level_id=$levelId AND team_id=$teamId AND user_id=$userId";
		return $wpdb->get_row($sql, ARRAY_A);
	}

	/**
	* Checking if $pageId has parent or not if it has parent then getting its siblings else returning all child pages
	* 
	* $id can be gameid or level id
	**/
	public function getOtherLevels( $pageId ) {
		$levelsWithUrl = array();
		$levelInfo = get_post( $pageId );
		
		// Implies that it is level
		if ( isset($levelInfo->post_parent) && $levelInfo->post_parent != 0 ) {
			$gameLevels = get_pages(array(
				'child_of' => $levelInfo->post_parent
			));
		}
		else {
			$gameLevels = get_pages(array(
				'child_of' => $pageId
			));			
		}

		foreach ($gameLevels as $id => $level) {
			$levelsWithUrl[$level->ID] = $level->guid;				
		}

		return $levelsWithUrl;
	}

	public function manageGameLevel($team_id, $game_id, $user_id, $level_id, $clue_seconds=0) {
		global $table_prefix, $wpdb;
		// $time_taken = time();
		$tblname = $table_prefix . 'gm_games';
		
		// Checking the attribute value in db
		$row = $this->getLevelInfo( $team_id, $level_id, $user_id );

		if ( is_array($row) && count($row) > 0 ) {
	    	$sql = "UPDATE $tblname SET clue_seconds=$clue_seconds WHERE level_id=$level_id AND user_id=$user_id AND team_id=$team_id";
		} 
		else {
			/**
			* Generating the query to insert game with all its levels
			**/
			$vals = "";
			$otherLevels = $this->getOtherLevels($game_id);
			if ( is_array($otherLevels) && count($otherLevels) > 0 ) {
				$counter = 0;
				foreach ($otherLevels as $lId => $otherLevel) {
					// Appending the commas
					if ( $counter !== 0 )
						$vals .= ",";

					if ( $lId == $level_id ) {
						$vals .= "($team_id, $game_id, $user_id, $lId, 0, 0, $clue_seconds)";
					} else {
						$vals .= "($team_id, $game_id, $user_id, $lId, 0, 0, 0)";
					}									
					$counter++;
				}
			}

			$sql = "INSERT INTO $tblname (team_id, game_id, user_id, level_id, time_taken, is_cleared, clue_seconds) VALUES ".$vals;
		}
		
		return $wpdb->query($sql);
	}

	public function add_clue_cb() {
		if( isset($_REQUEST['secondsToAdd']) && isset($_REQUEST['current_level_id']) && isset($_REQUEST['current_team_id']) && isset($_REQUEST['current_user_id']) && isset($_REQUEST['current_game_id']) ) {

			$gameLevelRes = $this->manageGameLevel($_REQUEST['current_team_id'], $_REQUEST['current_game_id'], $_REQUEST['current_user_id'], $_REQUEST['current_level_id'], $_REQUEST['secondsToAdd']
			);

			echo '<pre>';
			print_r($gameLevelRes);
			echo '</pre>';
		}		
		exit;
	}
}
