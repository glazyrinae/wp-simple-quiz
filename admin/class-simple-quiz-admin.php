<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    simple_quiz
 * @subpackage admin
 * @author     Glazyrin Andrey <railot116@gmail.com>
 */
class WP_SSQ_Admin {

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

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-controller-table-questions.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-controller-table-quizzes.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-controller-table-results.php';

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
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( __CLASS__, plugin_dir_url( __FILE__ ) . 'css/simple-quiz-admin.css', array(), $this->version, 'all' );

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
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( __CLASS__, plugin_dir_url( __FILE__ ) . 'js/simple-quiz-admin.js', array( 'jquery' ), $this->version, false );

	}
    /**
     * Adding a questing with redirect to controller quizzes
     *
     * @since    1.0.0
     */
    public function add_table_quizzes() {

        if ( isset( $_GET['page'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] &&
            'do_quiz'=== $_GET['page'] && empty($_GET['id_quiz'])
            && !empty( $_POST['quiz_title'] ) ) {

            $obj = WPSSQ_Controller_Table_Quizzes::get_instance();
            $id_quiz = $obj->do_quiz();
            wp_redirect( admin_url('admin.php?page=do_quiz&msg=added&id_quiz=' .
                urlencode( wp_unslash( $id_quiz ) )), 302 );
            exit;
        }
        WPSSQ_Controller_Table_Quizzes::get_instance();
    }
    /**
     * Adding a questing with redirect to controller questions
     *
     * @since    1.0.0
     */
    public function add_table_questions() {

        if ( isset( $_GET['page'] ) &&
            'do_question'=== $_GET['page'] &&
             empty( $_GET['id_question'] ) &&
            !empty( $_GET['id_quiz'] ) &&
            !empty( $_POST['question_text'] ) &&
            !empty( $_POST['answers'] ) &&
            !empty( $_POST['post'] )   ) {

            //validate
            $id_quiz  =(int)$_GET['id_quiz'];
            $text = trim( stripslashes( $_POST['question_text']) );

            $data = [
                'id_quiz' => $id_quiz,
                'text'    => $text,
            ];

            $obj = WPSSQ_Controller_Table_Questions::get_instance();
            $id_question = $obj->add_question($data);

            wp_redirect(admin_url('admin.php?page=do_question&msg=added&id_quiz=' .
                    urlencode( wp_unslash($id_quiz)) ) . '&id_question=' .
                    urlencode( wp_unslash($id_question) ), 302);
            exit;
        }
        WPSSQ_Controller_Table_Questions::get_instance();
    }
    /**
     * Adding the quiz results
     *
     * @since    1.0.0
     */
    public function add_table_results() {

        WPSSQ_Controller_Table_Results::get_instance();

	}

}
