<?php
/**
 * Model for quiz.
 *
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage public/classes
 * @author     Glazyrin Andrey <railot116@gamail.com>
 */
trait WP_SSQ_Public_Model {
    /**
     * Select questions for quiz
     *
     * @since    1.0.0
     * @id_quiz      int    $id_quiz.
     * @results      array     $list_questions.
     */
    public function get_questions( $id_quiz ) {

        global $wpdb;

        $list_questions = $wpdb->get_results(
            $wpdb->prepare("
				SELECT 
				    id_question,
					text
				FROM ".SSQ_TBL_QUESTIONS." 
					WHERE id_quiz = %d
					", $id_quiz
            ), ARRAY_A
        );

        if ( empty($list_questions) )
            return new WP_Error( 'rest_comment_invalid_id',
                __( 'Oops! This quiz has not some questions', 'simple_quiz'),
                array( 'status' => 404 ) );

        return $list_questions;
    }

    /**
     * Select quiz
     *
     * @since    1.0.0
     * @id_quiz      int    $id_quiz.
     * @results      array     $quiz_info.
     */
    public function get_quiz_informations( $id_quiz ) {

        global $wpdb;

        $quiz_info = $wpdb->get_row(
            $wpdb->prepare("
				SELECT 
				    id_quiz,
				    title,
					description
				FROM ".SSQ_TBL_QUIZZES." 
					WHERE id_quiz = %d", $id_quiz
            ), ARRAY_A
        );

        if ( empty($quiz_info) )
            return;

        return $quiz_info;
    }
    /**
     * Select answers
     *
     * @since    1.0.0
     * @$id_question      int    $id_question.
     * @results      array     $list_answers.
     */
    public function get_quiz_answers( $id_question ) {

        global $wpdb;

        $list_answers = $wpdb->get_results(
            $wpdb->prepare("
					SELECT 
					    id_answer, 
					    text
				    FROM ".SSQ_TBL_ANSWERS."
					    WHERE id_question = %d", $id_question
            ), ARRAY_A
        );

        return $list_answers;
    }
    /**
     * Select questions for quiz information
     *
     * @since    1.0.0
     * @id_quiz      int    $id_quiz.
     * @results      mixed array | null $result.
     */
    public function get_session_informations( $id_result ){

        global $wpdb;

        $result = $wpdb->get_row(
            $wpdb->prepare("
				SELECT 
				    content_results, 
				    status, 
				    total_right_answers,
				    id_quiz
				FROM ".SSQ_TBL_RESULTS." 
					WHERE id_result = %d", $id_result
            ), ARRAY_A
        );


        return $result ? $result : null;
    }
    /**
     * Select result for quiz
     *
     * @since    1.0.0
     * @id_quiz      int    $id_quiz.
     * @results      array     $results_quiz.
     */
    public function get_results_quiz( $id_quiz ) {

        global $wpdb;

        $results_quiz = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT 
                    ans.is_right, 
                    ans.id_answer, 
                    ans.id_question
				FROM 
				    ".SSQ_TBL_ANSWERS."    AS ans, 
					".SSQ_TBL_QUESTIONS."  AS ques 
				WHERE 
				    ans.id_question  = ques.id_question AND 
				    ques.id_quiz = %d 
				GROUP BY ans.id_question, ans.id_answer
				ORDER BY ans.id_answer", $id_quiz
            ), ARRAY_A
        );

        return $results_quiz;
    }
}