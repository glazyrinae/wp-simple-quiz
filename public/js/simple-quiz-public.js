/**
 * JS front for user
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage public/js
 * @author     Glazyrin Andrey <railot116@example.com>
 *
 */
jQuery(document).on("click", ".quiz-question", function(){

    var $str 		   = '';
    var $is_right_user = '';
    var $id_question   = jQuery(this).data("id_question");
    var json 	       = jQuery.parseJSON(content_quiz);
    var $question 	   = json[$id_question];

    jQuery(".quiz-question").each(function(){
        jQuery(this).removeClass("active");
    });
    jQuery(this).addClass("active");

    $str += '<div class="quiz-question-title" data-question='+$id_question+'>'+
        $question['title']+'</div><hr>';

    $str += '<ul class="quiz-answers">';

    jQuery.each($question['answers'], function( name, value ){

        var class_active = '';
        var class_verify = '';
        if ( 1 == value['score'] ) {
            if ( 1 == value['is_right_user'] )
                class_active = 'right-answer';
        }
        else {
            if ( 1 == value['is_right'] ) {
                class_verify = 'verify-answer';
                class_active='';
            }
            if ( 1 == value['is_right_user'] && 'verify-answer' != class_verify ) {
                class_active = 'right-answer';
            }
        }
        $str += '<li class="quiz-answer '+class_active+' '+class_verify +'" ' +
            'data-answer='+value['id_answer']+'>'+value['text']+'</li>';
    });

    $str += '</ul>';
    jQuery("#wrapper-quiz-content").html($str);

    send_updated_contents();
});

jQuery(document).on("click", ".next-question", function(){
    var $this = jQuery(".wrapper_quiz").find(".active").next();
    if ( $this.hasClass("quiz-question") ) {
        $this.trigger("click");
    }
    else {
        $this = null;
        return false;
    }
    send_updated_contents();
});

jQuery(document).on("click", ".prev-question", function(){
    var $this = jQuery(".wrapper_quiz").find(".active").prev();
    if ( $this.hasClass("quiz-question") ) {
        $this.trigger("click");
    }
    else {
        $this = null;
        return false;
    }
    send_updated_contents();
});

jQuery(document).on("click", ".quiz-answer", function(){

    var data 	    = jQuery.parseJSON(content_quiz);
    var id_answer   = jQuery(this).attr("data-answer");
    var id_question = jQuery(this).closest(".question_content").find(".quiz-question-title").data("question");

    if ( jQuery(this).hasClass("right-answer") ){
        data[id_question]['answers'][id_answer]['is_right_user'] = 0;
        jQuery(this).removeClass("right-answer");
    }
    else{

        jQuery(this).addClass("right-answer");
        data[id_question]['answers'][id_answer]['is_right_user'] = 1;
    }
    content_quiz = JSON.stringify(data);
    send_updated_contents();
});

function send_updated_contents() {
    var active_question = jQuery('.wrapper_quiz').find('.active').data('id_question');
    var status_quiz = jQuery('#content-questions-simple-quiz').data('status');

    wpCookies.set('active_tab', active_question);

    if ( status_quiz == 'active' ) {
        jQuery.ajax({
            type: "POST",
            data: {
                action: 'update_user_results',
                content: content_quiz
            },
            url: ajaxurl,
            success: function (res) {
                console.log('Success submitting data');
            },
            error: function () {
                console.log('Error from server');
            }
        });
    }
};

jQuery(document).on("keyup", ".user_name", function(){

    //alert( jQuery(this).val() );
    if ( jQuery(this).val() ) {
        jQuery('.user_reg').prop('disabled', false);
    }
    else{
        jQuery('.user_reg').prop('disabled', true);
    }
})

jQuery(document).ready(function() {

    jQuery('#user-registration-form-simple-quiz .user_reg').prop('disabled', true);
    if ( jQuery('#user-registration-form-simple-quiz').length ){
        wpCookies.set('active_tab', '');
    }

    if ( wpCookies.get('active_tab') ) {

        jQuery('.wrapper_quiz').find('.quiz-question').each(function () {
            if (wpCookies.get('active_tab') == jQuery(this).data('id_question'))
                jQuery(this).trigger('click');
        });
    }
    else {
        jQuery('.quiz-question:eq(0)').trigger('click');
    }
});