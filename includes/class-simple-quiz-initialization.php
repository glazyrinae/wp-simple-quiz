<?php
/**
 * Define constants for plugin.
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/includes
 * @author     Glazyrin Andrey <railot116@gmail.com>
 */
global $wpdb;
$prefix = $wpdb->prefix;

(new class ( $prefix ) {
    public function __construct( $prefix ) {

        DEFINE( 'SSQ_SIMPLE_QUIZ_VERSION', '1.0.0' );
        /* tables */
        DEFINE('SSQ_TBL_QUIZZES',   $prefix.'ssq_quizzes', true);
        DEFINE('SSQ_TBL_QUESTIONS', $prefix.'ssq_quizzes_questions', true);
        DEFINE('SSQ_TBL_ANSWERS',   $prefix.'ssq_quizzes_answers', true);
        DEFINE('SSQ_TBL_RESULTS',   $prefix.'ssq_quizzes_results', true);

        /*notifications*/
        DEFINE('SSQ_CLASS_SUCCESS', 'notice-success', true);
        DEFINE('SSQ_CLASS_INFO',    'notice-info', true);
        DEFINE('SSQ_CLASS_ERROR',   'notice-error', true);

        /*actions*/
        DEFINE('SSQ_QUIZ_ADD',    'ssq_add_quiz', true);
        DEFINE('SSQ_QUIZ_UPDATE', 'ssq_update_quiz', true);

        /*status*/
        DEFINE('SSQ_STATUS_COMPLITED', 1, true);
        DEFINE('SSQ_STATUS_OPENED', 0, true);
    }
});
