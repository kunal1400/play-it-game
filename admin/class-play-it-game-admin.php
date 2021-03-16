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

}
