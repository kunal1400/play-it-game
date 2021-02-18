<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/kunal1400
 * @since      1.0.0
 *
 * @package    Play_It_Game
 * @subpackage Play_It_Game/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Play_It_Game
 * @subpackage Play_It_Game/includes
 * @author     Kunal Malviya <lucky.kunalmalviya@gmail.com>
 */
class Play_It_Game_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $table_prefix, $wpdb;
	    $wp_track_table = $table_prefix . "gm_teams";

	    #Check to see if the table exists already, if not, then create it
	    if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table) {
	        $sql = "CREATE TABLE `". $wp_track_table . "` ( ";
	        $sql .= "  `id`  int(11)   NOT NULL auto_increment, ";
	        $sql .= "  `team_name` varchar(128) NOT NULL, ";
	        $sql .= "  `game_id`  int(11)   NOT NULL, ";
	        $sql .= "  `created_by` int(11) NOT NULL, ";
	        $sql .= "  `members_email` LONGTEXT NOT NULL, ";
	        $sql .= "  PRIMARY KEY (`id`) "; 
	        $sql .= "); ";

	        // File required to create table
	        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	        dbDelta($sql);
	    }

	    #Check to see if the table exists already, if not, then create it
	    $wp_track_attr_table = $table_prefix . "gm_games";
	    if($wpdb->get_var( "show tables like '$wp_track_attr_table'" ) != $wp_track_attr_table) {
	        $sql = "CREATE TABLE `". $wp_track_attr_table . "` ( ";
	        $sql .= "  `id`  int(11) NOT NULL auto_increment, ";
	        $sql .= "  `team_id` int(128) NOT NULL, ";
	        $sql .= "  `game_id` int(128) NOT NULL, ";
	        $sql .= "  `user_id` int(128) NOT NULL, ";
	        $sql .= "  `level_id` int(128) NOT NULL, ";
	        $sql .= "  `time_taken` int(128) NOT NULL, ";
	        $sql .= "  `is_cleared` BOOLEAN NOT NULL, ";
	        $sql .= "  PRIMARY KEY (`id`) "; 
	        $sql .= "); ";

	        // File required to create table
	        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	        dbDelta($sql);
	    }
	}

}
