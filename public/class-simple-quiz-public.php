<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    simple_quiz
 * @subpackage simple_quiz/public
 * @author     Glazyrin Andrey <railot116@gmail.com>
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/partials/simple-quiz-public-display.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/classes/class-quiz-public-model.php';

class WP_SSQ_Public {

    use WP_SSQ_Public_Display;
    use WP_SSQ_Public_Model;
	/**
	 * The ID of this plugin.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string    $plugin_name    The ID of this plugin.
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( __CLASS__, plugin_dir_url( __FILE__ ) . 'css/simple-quiz-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( __CLASS__, plugin_dir_url( __FILE__ ) . 'js/simple-quiz-public.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'utils',   plugin_dir_url( __FILE__ ) . '/wp-includes/js/utils.js', array( 'jquery' ), $this->version, false );
	}

    /**
     * Initialize a user session.
     *
     * @since    1.0.0
     */
    public function init_session_user() {

        if ( !session_id() ) {
            session_start();
        }

    }
    /**
     * Initialize the class and set its properties.
     *
     * @since       1.0.0
     * @param       integer $status     It defines status of quiz (in progress: 0, completed: 1)
     * @content     string  $content    The content of quiz in json format.
     * @total_anw   integer $total_anw  Total answers in quiz.
     * @result      null
     */
     public function update_user_results( $status=0, $content='', $total_anw=0 ){

        global $wpdb;

         $data = [
             'content_results' => $content ? addslashes(json_encode( $content )) : $_POST['content']
         ];


        if ( !empty( $status ) )
            $data[ 'status' ] = $status;

        if ( !empty( $total_anw ) )
            $data[ 'total_right_answers' ] = $total_anw;

        if ( !empty( $data['content_results'] ) )
            $wpdb->update( SSQ_TBL_RESULTS, $data, [ 'id_result' => $_SESSION['id_user_result'] ] );

        if( wp_doing_ajax() ) {
            echo __('Updating successful!', 'simple_quiz');
            exit;
        }
        else{
            wp_redirect( $_SERVER['REQUEST_URI'], 302);
        }
    }
    /**
     * Get quiz content
     *
     * @since       1.0.0
     * @id_quiz     integer
     * @result      string
     */
    public function get_quiz_content( $id_quiz ) {

        $num  = 1;
        $quiz = [];
        $results = $this->get_questions( $id_quiz );

        foreach ( $results as $result ) {
            $quiz[ $result['id_question'] ]['num'] = $num;
            $quiz[ $result['id_question'] ]['title']=$result['text'];
            $answers = $this->get_quiz_answers( $result['id_question'] );
            $buff = [];
            foreach ( $answers as $idx=>$ans ) {
                $buff[ $ans[ 'id_answer' ] ] = $answers[ $idx ];
                $buff[ $ans[ 'id_answer' ] ][ 'is_right_user' ] = 0;
            }
            $quiz[ $result['id_question'] ][ 'answers' ] = $buff;
            $num++;
        }

        $quiz = str_replace(['\r\n', '\n', '\r'], '<br>', json_encode( $quiz ));

        return addslashes($quiz);
	}
    /**
     * Registration session for user
     *
     * @since       1.0.0
     *
     */
    public function register_session_user () {

        global $wpdb;

        if ( !empty( $_POST['id_quiz'] ) && !empty( $_POST['user_login'] ) ) {

            //validate
            $id_quiz    = is_numeric( $_POST['id_quiz'] ) ? (int)$_POST['id_quiz'] : null;
            $user_login = is_string( $_POST['user_login'] ) ? sanitize_text_field( trim($_POST['user_login']) ) : null;


            $data = [
                'id_quiz'   => $id_quiz,
                'user_info' => $user_login,
                'content_results'=>$this->get_quiz_content($id_quiz)
            ];

            if ( $wpdb->insert( SSQ_TBL_RESULTS, $data ) )
                $_SESSION['id_user_result'] = $wpdb->insert_id;
            else
                wp_die(__('An error occurred while updating the result', 'simple_quiz'));

            wp_redirect( $_SERVER['REQUEST_URI'], 302);
            exit;
        }

    }
    /**
     * Ajax handler
     *
     * @since       1.0.0
     *
     */
    public function ajax_handling_user_action( $action ) {
	    switch ($action){
            case '':
                return;
        }
    }
    /**
     * Add java script for content quiz
     *
     * @since       1.0.0
     *
     */
    public function add_variable_ajaxurl() {

        if ( !empty( $_SESSION['id_user_result'] ) ) {

            $res = $this->get_session_informations( $_SESSION['id_user_result'] );

            if ( empty( $res ) ){
                unset( $_SESSION );
            }
            else {
                extract($res);
            }

        }

        echo "<script type='text/javascript'>
           var ajaxurl = '" . admin_url('admin-ajax.php') . "';
           var content_quiz ='".$content_results."';
        </script>";

    }
    /**
     * Reset result for user
     *
     * @since       1.0.0
     *
     */
    public function reset_quiz() {

         global $wpdb;

        if ( !empty( $_SESSION['id_user_result'] ) && isset( $_POST['reset_quiz'] ) ){

            //validate
            $id_user_result = is_numeric( $_SESSION['id_user_result'] ) ?
                (int)$_SESSION['id_user_result'] : null;

            $res = $wpdb->delete( SSQ_TBL_RESULTS, [ 'id_result'=>$id_user_result ] );
            if ( $res ) {
                $_SESSION['id_user_result'] = null;
            }
            wp_redirect( $_SERVER['REQUEST_URI'], 302);
            exit;
        }
    }

    /**
     * Compliting quiz for user
     *
     * @since       1.0.0
     *
     */
    public function complete_quiz() {

        if ( !empty( $_POST['id_quiz'] ) && !empty( $_POST['completed'] ) && !empty( $_SESSION['id_user_result'] ) ) {

            $score        = $total_right_answers = 0;
            $answers_user = $answers_right = $buff = [];

            //validate
            $id_quiz = is_numeric( $_POST['id_quiz'] ) ?
                (int)$_POST['id_quiz'] : null;
            $id_user_result = is_numeric( $_SESSION['id_user_result'] ) ?
                (int)$_SESSION['id_user_result'] : null;

            extract( $this->get_session_informations( $id_user_result ) );

            $contents = json_decode( stripslashes( $content_results ), true );

            foreach ( $contents as $key=>$content ) {
                $answers_user[$key] = array_map( function($answers) {
                    return $answers['is_right_user'];
                }, $content['answers'] );
            }
            $results_quiz = $this->get_results_quiz( $id_quiz );
            foreach ( $results_quiz as $key=>$content ) {
                if ( !in_array( $content['id_question'], $buff ) ) {
                    $buff[] = $content['id_question'];
                    array_walk( $results_quiz, function($v) use ($content, &$answers_right) {
                        if ( $v['id_question'] == $content['id_question'] )
                            $answers_right[ $content['id_question'] ][$v['id_answer']] = $v['is_right'];
                    });
                }
            };
            foreach ( $answers_user as $quest=>$answ ) {
                if ( $answers_user[$quest] == $answers_right[$quest] ) {
                    $contents[$quest]['score'] = 1;
                }
                else {
                    $contents[$quest]['score'] = 0;
                    foreach ( $contents[$quest]['answers'] as $k=>$v) {
                        $contents[$quest]['answers'][$k]['is_right'] = $answers_right[$quest][$k];
                    }
                }
            }
            $answers_right = $answers_user;
            foreach ( $results_quiz as $question=>$value ) {
                $answers_right[ $value['id_question'] ][ $value['id_answer'] ] = $value['is_right'];
            }
            array_walk ($answers_user, function( $value, $key )
            use ( &$answers_right, &$score, &$total_right_answers ) {
                if ( $value == $answers_right[$key] ) {
                    $answers_right[$key]['score'] = $score;
                    $total_right_answers++;
                }
                else {
                    $answers_right[$key]['score'] = 0;
                }
            });

            $this->update_user_results( SSQ_STATUS_COMPLITED, $contents, $total_right_answers );

            wp_redirect( $_SERVER['REQUEST_URI'], 302);
            exit;
        }

    }
    /**
     * Create short code
     *
     * @since       1.0.0
     * @atts        string
     * @result      mixed
     */
    public function get_quiz( $atts ) {

        $id_quiz = isset( $atts['id'] ) ? $atts['id'] : 0;

        $quiz_result = $this->get_quiz_informations( $id_quiz );

        if ( !empty( $quiz_result ) ) {

            if ( !empty( $_SESSION['id_user_result'] ) ) {

                //validate
                $id_user_result = is_numeric( $_SESSION['id_user_result'] ) ?
                    (int)$_SESSION['id_user_result'] : null;

                $data_result =  $this->get_session_informations( $id_user_result );


                if ( $id_quiz == $data_result['id_quiz'] ) {


                    return $this->render_content_quiz( $this->get_questions($id_quiz), $data_result );

                }
                else {
                    _e('You have an active quiz', 'simple_quiz');
                }
            }
            else {
                return $this->render_user_form( $quiz_result );
            }
        }
        else{
            return;
        }

    }

}
