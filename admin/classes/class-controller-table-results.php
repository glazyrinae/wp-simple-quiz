<?php
/**
 * Controller for table results
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/classes
 * @author     Glazyrin Andrey <railot116@example.com>
 *
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-render-table-results.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/class-render-result-form.php';

class WPSSQ_Controller_Table_Results {

    use WPSSQ_Render_Result_Form;

    static $instance;

    public $results;

    private $__db;

    public function __construct() {

        global $wpdb;

        $this->__db              = $wpdb;

        add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
        add_action( 'admin_menu', [ $this, 'render_plugin' ] );

    }

    public static function set_screen( $status, $option, $value ) {
        return $value;
    }

    public function render_plugin() {

        $hook_results = add_submenu_page('wp_simple_quiz',
            __('View list of quiz results',  'simple_quiz' ), __('List of quiz results', 'simple_quiz'),
            'manage_options',  'quiz_results', [$this, 'quiz_results'] );

        add_action( "load-$hook_results",   [ $this,  'view_results' ] );

    }

    public function view_results(){

        $option = 'per_page';
        $args   = [
            'label'   => __('List of Result','simple_quiz' ),
            'default' => 5,
            'option'  => __('Items per page', 'simple_quiz' )
        ];
        add_screen_option( $option, $args );
        $this->results = new WPSSQ_Render_Table_Results(
            $this->__db
        );

    }

    public function quiz_results(){
        $this->render_results();
    }

    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}