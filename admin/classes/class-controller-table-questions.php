<?php
/**
 * Controller for table question
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/classes
 * @author     Glazyrin Andrey <railot116@example.com>
 *
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/class-render-question-form.php';

class WPSSQ_Controller_Table_Questions {

    use WPSSQ_View_Questions;

    static $instance;

    private $__db;


	public function __construct() {

	    global $wpdb;
        $this->__db              = $wpdb;
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen_option' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'render_question' ] );

	}

	public static function set_screen_option( $status, $option, $value ) {
		return $value;
	}

	public function render_question() {
		add_submenu_page(null, 'Add new question', 'Add new question',
            'manage_options', 'do_question', [$this, 'do_question'] );
	}

	public function get_answers( $id_quiz, $id_question = null ) {

        $data = [];

	    if ( empty( $id_question ) )
	        return $data;

	    $data = $this->__db->get_row(
            $this->__db->prepare('SELECT id_question, text FROM '.SSQ_TBL_QUESTIONS.' 
              WHERE id_quiz = %d AND id_question = %d', $id_quiz, $id_question ),
            ARRAY_A
        );

        if ( empty( $data ) )
            wp_die( __( 'An error has occurred: a question not found!', 'simple_quiz') );

        $data['answers'] = $this->__db->get_results(
            $this->__db->prepare('SELECT * FROM '. SSQ_TBL_ANSWERS .'
              WHERE id_question = %d', $id_question ),
            ARRAY_A
        );

        return $data;
    }


    public function add_answers( $answers, $id_question, $post ) {

	    foreach ($answers as $key => $answer) {
            $data_answer = [
	            'id_question' => (int)$id_question,
                'id_answer'   => (int)$key,
                'text'        => stripslashes($answer),
                'is_right'    => in_array( $key, $post ) ? 1 : 0
            ];
	        if ( !empty($answer) && !empty( $key ) )
	            $this->__db->insert( SSQ_TBL_ANSWERS, $data_answer );

	    }
    }

    public function striptags( $arr ){

	    $validated = [];
        foreach( $arr as $key=>$el ) {
            $el = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $el);
            $validated[ $key ]= trim($el);
        }
        return $validated;
    }

    public function add_question ( $data ) {

	    //validate $post
        $validated_answers = isset( $_POST['answers'] ) && is_array( $_POST['answers'] )
            ? $this->striptags($_POST['answers']) : null;

        $right_answer = isset( $_POST['post'] ) && is_array( $_POST['post'] )
            ?  filter_var_array( $_POST['post'], FILTER_VALIDATE_INT ) : null;

	    if ( $this->__db->insert( SSQ_TBL_QUESTIONS, $data ) ) {

	        $last_question = $this->__db->insert_id;

            if ( !empty( $last_question ) && !empty( $validated_answers ) && !empty( $right_answer ) ) {

                $this->add_answers( $validated_answers, $last_question, $right_answer );

                return $last_question;
            }

        }
	    wp_die( __( 'An error has occurred: a question not found!', 'simple_quiz'));
    }


	public function do_question() {

	    $message = $condition = [];

	    //validate $request
        $id_question = isset( $_GET['id_question'] ) && is_numeric( $_GET['id_question'] )
            ? (int)$_GET['id_question'] : null;

	    $id_quiz     = isset( $_GET['id_quiz'] ) && is_numeric( $_GET['id_quiz'] )
            ? (int)$_GET['id_quiz'] : null;

        $msg   = isset( $_GET['msg'] ) && is_string( $_GET['msg'] )
            ? sanitize_text_field( trim($_GET['msg']) ) : null;

        $question_text = isset( $_POST['question_text'] ) && is_string( $_POST['question_text'] )
            ? stripslashes($_POST['question_text']) : null;

        $answers = isset( $_POST['answers'] ) && is_array( $_POST['answers'] )
            ? $this->striptags($_POST['answers']) : null;

        $right_answer = isset( $_POST['post'] ) && is_array( $_POST['post'] )
            ?  filter_var_array( $_POST['post'], FILTER_VALIDATE_INT ) : null;


        if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {

            if (!empty( $question_text ) &&
                !empty( $answers ) &&
                !empty( $right_answer )
            ) {

                $data = [
                    'id_quiz' => $id_quiz,
                    'text'    => $question_text
                ];

                $condition = [ 'id_quiz' => $data['id_quiz'], 'id_question' =>  $id_question ];

                if ( !empty( $id_question ) &&
                    false !== $this->__db->update( SSQ_TBL_QUESTIONS, $data, $condition ) ) {

                    $this->__db->delete( SSQ_TBL_ANSWERS, ['id_question' => $id_question] );

                    $this->add_answers( $answers, $id_question, $right_answer );

                    $message[ SSQ_CLASS_SUCCESS ] = __('The question has been updated!', 'simple_quiz');
                }

            }
            elseif ( empty( $answers ) || empty( $right_answer ) || empty( $question_text ) ) {

                $message[ SSQ_CLASS_INFO ] = __('Please input correct question. Input text for a question and mark a right answer!', 'simple_quiz');

            } else {

                $message[ SSQ_CLASS_ERROR ] = __('An error has occurred!', 'simple_quiz');
            }

        }
        elseif ( 'added' === $msg && !empty( $id_question ) && !empty( $id_quiz ) ) {

            $message[ SSQ_CLASS_SUCCESS ] = __('The question has been added!', 'simple_quiz');
        }

        $this->view_question( $this->get_answers( $id_quiz, $id_question ), $message );
	}
	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}	