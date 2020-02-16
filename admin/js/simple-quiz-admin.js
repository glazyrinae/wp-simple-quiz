/**
 * JS front for admin panel
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/js
 * @author     Glazyrin Andrey <railot116@example.com>
 *
 */
jQuery(document).ready(function() {

    var $ = jQuery;
    var $_last = jQuery('#type-container .question-content').last();
    var $type_container = '<br><div id="type-container" ></div>';
    var answer_count= $_last.attr('id') ? $_last.attr('id') : 0;
    var $question_container = $('.question-content:first');

    $('.inside').css('cursor','pointer');


    //tinyMCE.EditorManager.execCommand('mceAddEditor', false, 'simple-quiz-text-question');


    //tinyMCE.get('simple-quiz-text-question').focus();

    if ( 'type-container' != $('#type-container').attr('id') ) {

        //jQuery("body").append( jQuery(tmp) );

    }
    else{
        var container = $('#type-container').find('.question-content');

        container.each(function(){

            var editorId = jQuery(this).find(".wp-editor-area").attr('id');


            tinymce.EditorManager.execCommand('mceAddEditor', false, editorId);

        });
    }

    if ( $('.set_custom_images').length > 0 ) {

        if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {

            $( document ).on( 'click', '.set_custom_images', function(e) {

                e.preventDefault();
                var button = $(this);
                var id = button.prev();

                wp.media.editor.send.attachment = function(props, attachment) {
                    id.val(attachment.id);
                };

                wp.media.editor.open(button);
                return false;
            });
        }
    };

    function AddRemoveTinyMce( editorId ) {

        if( tinyMCE.get(editorId) ) {
            tinyMCE.EditorManager.execCommand('mceRemoveEditor', true, editorId);
        }
        else {
            tinymce.EditorManager.execCommand('mceAddEditor', false, editorId);
            tinyMCE.EditorManager.execCommand('mceFocus', false, editorId);
            tinyMCE.get(editorId).focus();
        }
    }

    $('a.toggle-tinymce').die('click').live('click', function(e) {
        AddRemoveTinyMce('description');
    });

    $('a.add-type').die('click').live('click', function(e) {

        e.preventDefault();
        element = null;
        answer_count++;
        for ( var i = 0; i<1; i++ ) {
            element = $question_container.clone();
            element.attr('id', answer_count);
            element.find('.remove-type').attr('targetDiv', answer_count);
            element.find('.wp-editor-area').attr('id', 'txt_'+answer_count);
            element.find('.wp-editor-area').attr('name', 'answers['+answer_count+']');
            element.find('.button').attr('data-editor','txt_'+answer_count);
            element.find('.right_answer').attr('value', answer_count);
            element.show();
            element.appendTo('#type-container');
            AddRemoveTinyMce('txt_'+answer_count);
        }

    });


    $( document ).on( 'click', '.lable-check', function(e) {

        if ( $(this).find('.right_answer').prop('checked') ) {
            $(this).find('.right_answer').prop('checked', false)
        }
        else{
            $(this).find('.right_answer').prop('checked', true)
        }
    });


    $( document ).on( 'click', '.right_answer', function(e) {
        e.stopPropagation();
    });

    $( document ).on( 'click', '#add_question', function(e){
        e.preventDefault();

        $( '.right_answer' ).each( function( index ) {
            var editorId = 'txt_'+$(this).closest('.question-content').attr('id');

            if ( $(this).is( ":checked" ) &&  tinyMCE.get(editorId) ) {
                var txt = $.trim( tinyMCE.get(editorId).getContent() );
                if ( !txt ) {
                    $(this).prop('checked', false);
                }
            }
        });
        $('#form-submit-question').submit();
    });
});



