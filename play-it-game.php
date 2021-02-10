<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/kunal1400
 * @since             1.0.0
 * @package           Play_It_Game
 *
 * @wordpress-plugin
 * Plugin Name:       Play-It-Game
 * Plugin URI:        https://github.com/kunal1400
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Kunal Malviya
 * Author URI:        https://github.com/kunal1400
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       play-it-game
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLAY_IT_GAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-play-it-game-activator.php
 */
function activate_play_it_game() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-play-it-game-activator.php';
	Play_It_Game_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-play-it-game-deactivator.php
 */
function deactivate_play_it_game() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-play-it-game-deactivator.php';
	Play_It_Game_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_play_it_game' );
register_deactivation_hook( __FILE__, 'deactivate_play_it_game' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-play-it-game.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_play_it_game() {

	$plugin = new Play_It_Game();
	$plugin->run();

}
run_play_it_game();
