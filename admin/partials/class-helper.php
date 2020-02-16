<?php
/**
 * Rendering message box with notifications
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/partials
 * @author     Glazyrin Andrey <railot116@example.com>
 */
trait WPSSQ_Quiz_Helper
{
    public function render_message_box( $message, $url='' )
    {

        return sprintf('<div class="notice %s is-dismissible">
             <p> %s <a href="/wp-admin/admin.php?page=%s">%s</a></p>
            </div>', key( $message ), current( $message ), $url,
            __('Return to list of quizzes','simple_quiz') );
    }
}