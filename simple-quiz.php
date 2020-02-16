<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://smart-blog.net
 * @since             1.0.0
 * @package           simple_quiz
 *
 * @wordpress-plugin
 * Plugin Name:       Super Simple Quiz
 * Plugin URI:        http://smart-blog.net
 * Description:       This plugin is created for creation a simple quiz. It may be used for questionnaire. To get started: activate the plugin and then go to your simple quiz menu and enjoy!
 * Version:           1.0.0
 * Author:            glazyrinae
 * Author URI:        https://vk.com/glazyrinae
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simple-quiz
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-quiz-initialization.php';
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-simple-quiz-activator.php
 */
function activate_simple_quiz() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-quiz-activator.php';
    WP_SSQ_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-simple-quiz-deactivator.php
 */
function deactivate_simple_quiz() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-quiz-deactivator.php';
    WP_SSQ_Deactivator::deactivate();
}

function delete_simple_quiz() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-quiz-unistall.php';
    WP_SSQ_Unistall::unistall();
}

register_activation_hook( __FILE__, 'activate_simple_quiz' );
register_deactivation_hook( __FILE__, 'deactivate_simple_quiz' );
register_uninstall_hook(__FILE__, 'delete_simple_quiz');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-simple-quiz.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_simple_quiz() {
	$simple_quiz = new WPSSQ_Simple_Quiz();
    $simple_quiz->run();
}
run_simple_quiz();
