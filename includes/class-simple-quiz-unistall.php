<?php
/**
 * Fired during plugin uninstalling.
 *
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/includes
 * @author     Glazyrin Andrey <railot116@gmail.com>
 */
class WP_SSQ_Unistall {

	/**
	 * Delete all tables for plugin
	 *
	 * @since    1.0.0
	 */
	public static function unistall() {

	    global $wpdb;

        $tables   = [
            SSQ_TBL_ANSWERS,
            SSQ_TBL_QUESTIONS,
            SSQ_TBL_RESULTS,
            SSQ_TBL_QUIZZES
        ];

        foreach( $tables as $table ){

            $wpdb->query(" DROP TABLE IF EXISTS $table " );
        }
	}
}
