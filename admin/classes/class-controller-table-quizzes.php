<?php
/**
 * Controller for table quizzes
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/classes
 * @author     Glazyrin Andrey <railot116@example.com>
 *
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/class-render-quiz-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-render-table-quizzes.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-render-table-questions.php';

class WPSSQ_Controller_Table_Quizzes {

    use WPSSQ_Render_Quiz_Form;

	static $instance;

	public $quizzes;
	public $questions;

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

        $hook_quizzes = add_menu_page( __('Simple Quiz List','simple_quiz'), __('Simple Quiz List','simple_quiz'),
            'manage_options', 'wp_simple_quiz',[$this, 'render_quizzes_list'] );

        add_submenu_page( 'wp_simple_quiz', 'Do quiz', __('Add new quiz','simple_quiz'),
            'manage_options',  'do_quiz', [$this, 'do_quiz'] );

        $hook_questions = add_submenu_page( null, 'Simple Quiz Questions', null,
            'manage_options',  'do_quiz', [$this, 'render_questions_list'] );

        add_action( "load-$hook_quizzes",   [ $this,  'view_quizzes'   ] );
		add_action( "load-$hook_questions", [ $this,  'view_questions' ] );

    }

	public function view_questions() {

		$option = 'per_page';
		$args   = [
            'label'   => __('List of Quizzes','simple_quiz' ),
			'default' => 5,
            'option'  => __('Items per page', 'simple_quiz' )
		];
		add_screen_option( $option, $args );
		$this->questions = new WPSSQ_Render_Table_Questions(
		    $this->__db
        );

	}

	public function view_quizzes() {

		$option = 'per_page';
		$args   = [
			'label'   => __('List of Quizzes','simple_quiz' ),
			'default' => 5,
			'option'  => __('Items per page', 'simple_quiz' )
		];
		add_screen_option( $option, $args );
		$this->quizzes = new WPSSQ_Render_Table_Quizzes(
            $this->__db
        );

	}

	public function do_quiz(){

	    $message = $data = [];
	    //validate
	    $id_quiz = isset( $_GET['id_quiz'] ) && is_numeric( $_GET['id_quiz'] )
            ? (int)$_GET['id_quiz'] : null;

	    $title_question = isset( $_POST['quiz_title'] ) && is_string( $_POST['quiz_title'] )
            ? sanitize_text_field( trim( $_POST['quiz_title'] ) ) : null;

        $quiz_description = isset( $_POST['quiz_description'] ) && is_string( $_POST['quiz_description'] )
            ? stripslashes( $_POST['quiz_description'] ) : null;

        $msg = isset( $_GET['msg'] ) && is_string( $_GET['msg'] )
            ? sanitize_text_field( $_GET['msg'] ) : null;

        $ntf_update = isset( $_POST[ SSQ_QUIZ_UPDATE ] ) && is_string( $_POST[ SSQ_QUIZ_UPDATE ] )
            ? sanitize_text_field( $_POST[ SSQ_QUIZ_UPDATE ] ) : null;

        $ntf_insert = isset( $_POST[ SSQ_QUIZ_ADD ] ) && is_string( $_POST[ SSQ_QUIZ_ADD ] )
            ? sanitize_text_field( $_POST[ SSQ_QUIZ_ADD ] ) : null;

        $form_actions = empty( $_POST['action'] ) || empty( $_POST['action2'] ) ? true : false;

        if ( !empty( $id_quiz ) ) {

            $query = $this->__db->prepare('SELECT * FROM '.SSQ_TBL_QUIZZES.' WHERE id_quiz = %d', $id_quiz);

            $data = (array)$this->__db->get_row( $query );

            if ( empty($data) )
                wp_die( __( 'An error has occurred: a quiz not found!' ) );
        }

        if ( !empty( $msg ) )
            $message[ SSQ_CLASS_SUCCESS ] = __( 'The quiz has been added!', 'simple_quiz' );

	    if ( 'POST' === $_SERVER['REQUEST_METHOD'] && $form_actions ) {

	        if ( !empty( $title_question ) ) {

	            $data = [
                    'id_quiz'     => $id_quiz,
                    'title'       => $title_question,
                    'description' => $quiz_description
                ];

                if ( $ntf_insert && $this->__db->insert( SSQ_TBL_QUIZZES, $data ) ) {
                    return $this->__db->insert_id;
                } elseif ( $ntf_update &&
                    false !== $this->__db->update( SSQ_TBL_QUIZZES, $data, ['id_quiz' => $data['id_quiz'] ]) ) {
                    $message[ SSQ_CLASS_SUCCESS ] = __('The quiz has been updated!', 'simple_quiz' );
                } else {
                    $message[ SSQ_CLASS_ERROR ] = __('An error has occurred!', 'simple_quiz' );
                }
            }
            else{
                $message[ SSQ_CLASS_INFO ] = __('Please enter a quiz name!', 'simple_quiz' );
            }
        }
        $this->render_quiz( $message, $data );
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}	