<?php
/**
 * Rendering form with questions
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/partials
 * @author     Glazyrin Andrey <railot116@example.com>
 */
require_once 'class-helper.php';
//if ( ! class_exists( '_WP_Editors', false ) ) {
    require( ABSPATH . WPINC . '/class-wp-editor.php' );
//}
trait WPSSQ_View_Questions {

    use WPSSQ_Quiz_Helper;

	public function view_question( $data, $message ) {

	    $label = __( 'Save question','simple_quiz' );

	    if ( !empty( $data ) ) {

		    extract($data);

		    $url = admin_url('admin.php?page=do_question&id_quiz=' .
                    urlencode( wp_unslash( (int)$_GET['id_quiz'] ) ) ). '&id_question=' .
                    urlencode( wp_unslash( isset( $id_question ) ? (int)$id_question : 0 ) );
        }
        $href = 'do_quiz&id_quiz='.(int)$_GET['id_quiz'];
	?>	
	<div class='wrap'>
        <?=( !empty( $message ) ) ? $this->render_message_box( $message, $href ) : '' ?>
		<form id='form-submit-question' method='post' action="<?=$data ? $url : ''?>" >
			<hr class='wp-header-end'>
			<br class='clear'>
			<h2 class='wp-heading-inline' ><?php _e('Think up a question', 'simple_quiz')?>
                <a href='' class="page-title-action add-type btn btn-primary pull-right"
                   href="javascript: void(0)" title="<?php _e('Click to add more', 'simple_quiz')?>">
                    <i class='glyphicon glyphicon-plus-sign'></i>
                    <?php _e('Add new answer', 'simple_quiz')?>
                </a>
            </h2>
            <?php
            wp_editor( !empty($text) ? stripslashes($text) : '',
                'question_text', $settings = array( 'textarea_rows'=> '5' ) );
            ?>
            <br>
            <?php submit_button( $label, 'primary', 'add_question' );  ?>

            <?php $this->render_answers(); ?>

			<div id='type-container'>
			<?php
			if ( !empty( $answers ) ) {
				
				foreach( $answers as $answer ){

					$this->render_answers($answer);
				}
			}
			?>
			</div>
		</form>
	</div>
	<?php	
	}
	
	public function render_answers( $answer = 0 ){
        $is_right = '';
        if ( !empty( $answer ) ) extract( $answer );
	?>	
	<br>
        <div class='wrap question-content'
             id="<?=isset( $id_answer ) ? $id_answer : '' ?>"
             style="<?=isset( $id_answer ) ? '' : 'display:none' ?>" >

            <div class='inside'>
                <div  class='lable-check' >
                    <strong  > <?php _e('Click here', 'simple_quiz')?> </strong>
                    <span> <?php _e('this answer to be define as right', 'simple_quiz')?> </span>
                    <input type="checkbox" name="post[]" class="right_answer"
                           value='<?=isset( $id_answer ) ? $id_answer : 0 ?>'
                           <?= !empty( $is_right ) ? 'checked' : '' ?> >
                </div>
                <div  class='box-right-position'>
                    <a class='remove-type color-delete-label' targetDiv='<?=isset( $id_answer ) ? $id_answer : 0 ?>'
                       data-id=0  href='javascript: void(0)'
                       onclick='if ( !confirm( "Are you sure You want to delete?" ) ) {return true}
                            else{event.stopPropagation(); event.preventDefault();
                            jQuery(this).closest(".question-content").remove()}' >
                        <i class='glyphicon glyphicon-trash'></i>
                        <?php _e('Delete answer', 'simple_quiz')?>
                    </a>
                </div>
            </div>
            <div class='box-clear-both'></div>
            <div class='wp-media-buttons'>
                <button type='button'  class='button insert-media add_media'
                        data-editor='txt_<?=isset( $id_answer ) ? $id_answer : 0 ?>'>
                    <span class='wp-media-buttons-icon'></span>
                    <?php _e('Add media', 'simple_quiz')?>
                </button>
            </div>
            <div class='box-clear-both'></div>
            <textarea class='wp-editor-area' cols='5' rows='5'
                     <?=isset( $id_answer ) ?  'id=txt_'.$id_answer : 0 ?>
                      name='answers[<?=isset( $id_answer ) ? $id_answer : 0 ?>]' >
                <?=isset( $text ) ?  stripslashes($text)  : '' ?>
            </textarea>
        </div>
	<?php							
	}
}
?>