<?php
/**
 * Rendering table with quiz content
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/partials
 * @author     Glazyrin Andrey <railot116@example.com>
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/class-helper.php';

trait WPSSQ_Render_Quiz_Form {

	use WPSSQ_Quiz_Helper;

	public function render_quizzes_list () {
	?>
		<div class="wrap">
			<h2><?php _e( 'List of the quizzes','simple_quiz')?>
                <a href="admin.php?page=do_quiz" class="page-title-action" >
                    <?php _e(  'Add new quiz','simple_quiz' )?>
                </a>
            </h2>
			<br>
			
			<hr class="wp-header-end">
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-3">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->quizzes->prepare_items();
								$this->quizzes->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}
	public function render_quiz ( $message = [], $data = [] ) {

	    if ( !empty($data) ) extract($data);

        if ( isset($_GET['id_quiz']) ) {
            $header = __( 'Edit', 'simple_quiz' );
            $button_name = SSQ_QUIZ_UPDATE;
            $button_lable = __( 'Save the quiz', 'simple_quiz' );
            $url = "?page=do_quiz&id_quiz=".$id_quiz;
        }
        else{
            $header = __( 'Add', 'simple_quiz' );
            $button_name = SSQ_QUIZ_ADD;
            $button_lable = __( 'Add new quiz', 'simple_quiz' );
            $url = "";
        }
    ?>
		<div class="wrap">
            <form action="<?=$url?>" method="post" >
                <h1 class="wp-heading-inline"><?=$header?> Quiz</h1>
                <?=( !empty( $message ) ) ? $this->render_message_box( $message, 'wp_simple_quiz' ) : ''?>
                <div id="titlediv">
					<div id="titlewrap">
						<input id="title" name="quiz_title" type="text" size="30"
                               value="<?=!empty($title) ? $title : ''?>"  spellcheck="true" >
					</div>
				</div>
				<div id="titlediv">
					<div id="titlewrap">
						<h2 class="wp-heading-inline" ><?php _e( 'Description', 'simple_quiz' )?></h2>
						<?php
							wp_editor( !empty( $description ) ?  stripslashes($description)   : '',
                                'quiz_description', $settings = array('textarea_rows'=> '5') );
						?>
					</div>
				</div>
				<?php submit_button( $button_lable, 'primary', $button_name ); ?>
			</form>
		</div>
	<?php	
	}
    public function render_questions_list()
    {
        if ( isset( $_GET['id_quiz'] ) ):
            ?>
            <div class="wrap">
                <h2><?php _e( 'List of questions', 'simple_quiz' )?>
                    <a href="admin.php?page=do_question&id_quiz=<?=(int)$_GET['id_quiz']?>"
                    class="page-title-action"><?php _e( 'Add new question', 'simple_quiz' )?></a>
                </h2>
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-3">
                        <div id="post-body-content">
                            <div class="meta-box-sortables ui-sortable">
                                <form method="post">
                                    <?php
                                    $this->questions->prepare_items();
                                    $this->questions->display();
                                    ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <br class="clear">
                </div>
            </div>
            <?php
        endif;
    }
}
?>