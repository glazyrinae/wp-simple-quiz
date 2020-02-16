<?php
/**
 * Rendering quiz form
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage public/partials
 * @author     Glazyrin Andrey <railot116@example.com>
 */
trait WP_SSQ_Public_Display
{

    public function render_user_form( $quiz_info )
    {
        ob_start();

        if ( !empty( $quiz_info ) )
            extract($quiz_info);
        ?>
        <div id='content-registration-user-simple-quiz'>
            <div class='title-simple-quiz' >
                <?php echo $title ?>
            </div>
            <div class='description-simple-quiz'>
                <?php echo stripslashes( $description ) ?>
            </div>
            <form id='user-registration-form-simple-quiz' method='POST' action=''>
                <input type='text' class='full-size-simple-quiz' name='user_login'
                       placeholder="<?php _e('Please! Input user login!','simple_quiz') ?>"/>
                <input type='hidden' name='id_quiz' value='<?php echo $id_quiz ?>'/>
                <br>
                <input type='submit' class='button-simple-quiz'
                       value='<?php _e('Start this quiz', 'simple_quiz') ?>'/>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }


    public function render_content_quiz( $questions_list, $data_result )
    {
        ob_start();
        $num = 1;

        extract( $data_result );

        ?>
        <div id='content-questions-simple-quiz'
             data-status='<?=( SSQ_STATUS_COMPLITED == $status ) ? "completed" : "active" ?>'>
            <div class='wrapper_quiz'>
                <div class='prev-question'> < </div>
                <?php foreach ( $questions_list as $question ): ?>
                    <div class='quiz-question' data-id_question=<?= $question['id_question'] ?>>
                        <?= $num++ ?>
                    </div>
                <?php endforeach ?>
                <div class='next-question'> ></div>
            </div>
            <div style='clear:both'></div>
            <form method='POST' action=''>
                <div id='wrapper-quiz-content' class='question_content'>
                </div>
                <?php if ( SSQ_STATUS_COMPLITED == $status ): ?>
                    <?php $this->render_result_quiz( $total_right_answers, $num ) ?>
                <?php else: ?>
                    <input type='submit' name='completed' class='button-simple-quiz'
                           value='<?php _e('Complete this quiz!', 'simple_quiz') ?>'/>
                    <input type='hidden' name='id_quiz' value='<?= $id_quiz ?>'/>
                <?php endif ?>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_result_quiz($result, $num)
    {
        ?>
        <div class='result_content'><?php  _e('You have current result: ', 'simple_quiz') ?><?= $result ?>
            <?php _e('from ', 'simple_quiz') ?><?= --$num ?></div>
        <input type='submit' name='reset_quiz' class='button-simple-quiz' style='margin-top:10px; width:100%;'
               value='<?php _e('Start quiz again!', 'simple_quiz') ?>'/>
        <?php
    }
}



