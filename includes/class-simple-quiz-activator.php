<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/includes
 * @author     Glazyrin Andrey <railot116@gmail.com>
 */
require_once 'class-simple-quiz-initialization.php';
class WP_SSQ_Activator {

	/**
     * Creating tables for plugin
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

	    global $wpdb;
        $table_quizzes   = SSQ_TBL_QUIZZES;
        $table_questions = SSQ_TBL_QUESTIONS;
        $table_answers   = SSQ_TBL_ANSWERS;
        $table_results   = SSQ_TBL_RESULTS;

        $charset_collate = $wpdb->get_charset_collate();


        $sqls =[
            "CREATE TABLE IF NOT EXISTS $table_quizzes (
                id_quiz INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(600) NOT NULL,
                description TEXT NOT NULL,
                date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                PRIMARY KEY(id_quiz)
            ) ENGINE = INNODB $charset_collate",
            "CREATE TABLE IF NOT EXISTS $table_results (
                id_result INT NOT NULL AUTO_INCREMENT,
                id_quiz INT NOT NULL,
                user_info VARCHAR(600) NOT NULL,
                content_results TEXT(200) NOT NULL DEFAULT '',
                date_quiz TIMESTAMP DEFAULT NOW(),
                status TINYINT(1) NOT NULL DEFAULT 0,
                total_right_answers TINYINT(200) UNSIGNED DEFAULT 0,
                PRIMARY KEY (id_result),
                FOREIGN KEY (id_quiz) REFERENCES $table_quizzes(id_quiz) ON DELETE CASCADE
                ) ENGINE = INNODB $charset_collate",
            "CREATE TABLE IF NOT EXISTS $table_questions (
                id_question INT AUTO_INCREMENT NOT NULL,
                id_quiz INT NOT NULL,
                text TEXT NOT NULL,
                PRIMARY KEY(id_question),
                FOREIGN KEY (id_quiz) REFERENCES $table_quizzes (id_quiz) ON DELETE CASCADE
                ) ENGINE = INNODB $charset_collate",
            "CREATE TABLE IF NOT EXISTS $table_answers (
                id_question INT NOT NULL,
                id_answer INT NOT NULL,
                text TEXT NOT NULL,
                is_right TINYINT(1) NOT NULL DEFAULT 0,
                UNIQUE KEY (id_question,id_answer),
                FOREIGN KEY (id_question) REFERENCES $table_questions (id_question) ON DELETE CASCADE
                ) ENGINE = INNODB $charset_collate"
        ];
		
        foreach ( $sqls as $sql  ) {
            dbDelta( $sql );
        }
	}
}
