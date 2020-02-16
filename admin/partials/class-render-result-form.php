<?php
/**
 * Rendering table with quiz results
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/partials
 * @author     Glazyrin Andrey <railot116@example.com>
 */
trait WPSSQ_Render_Result_Form {

    public function render_results() {
        ?>
        <div class="wrap">
            <h2><?php _e(  'Results', 'simple_quiz' )?></h2>
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-3">
                        <div id="post-body-content">
                            <div class="meta-box-sortables ui-sortable">
                                <form method="post">
                                    <?php
                                    $this->results->prepare_items();
                                    $this->results->display();
                                    ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <br class="clear">
                </div>
            </div>
        <?php
    }
}