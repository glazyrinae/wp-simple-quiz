<?php
/**
 * Controller for table questions
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/classes
 * @author     Glazyrin Andrey <railot116@example.com>
 *
 */
if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class WPSSQ_Render_Table_Questions extends WP_List_Table {

    private $__db;

    public function __construct( $db ) {

        $this->__db         = $db;

        parent::__construct([
            'singular' => __('Simple quiz', 'simple_quiz'),
            'plural'   => __('Simple quiz', 'simple_quiz'),
            'ajax'     => false
        ]);

    }
    /**
     * Getting array of questions
     * @return array
     */
    public function get_questions( $per_page = 5, $page_number = 1 ) {

        //validate input data
        $id_quiz     = !empty($_REQUEST['id_quiz'] ) && is_numeric( $_REQUEST['id_quiz'] )
            ? (int)$_REQUEST['id_quiz'] : null;

        $order    = ! empty( $_REQUEST['order'] ) && is_string( $_REQUEST['order'] )
            ? ' '.esc_sql( $_REQUEST['order'] ) : ' ASC';

        $orerby = ! empty( $_REQUEST['orderby'] ) && is_string( $_REQUEST['order'] )
            ? esc_sql( $_REQUEST['orderby'] ) : null;
        //validate input data

        $sql = 'SELECT * FROM '.SSQ_TBL_QUESTIONS.' WHERE id_quiz='.$id_quiz;

        if ( isset( $orerby ) ) {
            $sql .= ' ORDER BY ' . $orerby;
            $sql .= $order;
        }
        $sql .= ' LIMIT '. $per_page;
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $this->__db->get_results( $sql, 'ARRAY_A' );

        return $result;

    }
    /**
     * Delete a question
     */
    public function delete_question( $id ) {

        $this->__db->delete(
            SSQ_TBL_QUESTIONS,
            [ 'id_question' => $id ],
            [ '%d' ]
        );
    }
    /**
     * Getting count of record in table
     * @return array
     */
    public function record_count() {

        $sql = 'SELECT COUNT(*) FROM '.SSQ_TBL_QUESTIONS.' WHERE id_quiz = %d';
        return $this->__db->get_var( $this->__db->prepare( $sql, $_REQUEST['id_quiz'] ));
    }
    /**
     * Text displayed when no customer data is available
     *
     */
    public function no_items() {
        _e( 'No quiz available.', 'simple_quiz' );
    }
    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_text( $item ) {

        //$delete_nonce = wp_create_nonce( 'id_question_'.$item['id_question'] );
        $title = '<strong>' . stripslashes( $item['text'] ) . '</strong>';
        $actions = [
            'edit'   => sprintf( '<a href="?page=do_question&id_quiz=%s&id_question=%s">'.
                __( 'Edit', 'simple_quiz' ).'</a>',
                absint( $item['id_quiz'] ), absint ( $item['id_question'] ) ),
            'delete' => sprintf( '<a href="?page=%s&id_quiz=%s&id_question=%s&action=%s">'.
                __( 'Delete', 'simple_quiz' ).'</a>',
                esc_attr( $_REQUEST['page'] ), absint( $item['id_quiz'] ) , absint( $item['id_question'] ),
                'delete' )
        ];
        return $title . $this->row_actions( $actions );

    }
    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'text':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }
    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="ids_delete[]" value="%s" />', $item['id_question']
        );
    }
    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
            'cb'      => '<input type="checkbox" />',
            'text'    => __('The question', 'simple_quiz')
        ];
        return $columns;
    }
    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = [
            //'text' => array( 'text', true )
        ];
        return $sortable_columns;
    }
    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => __( 'Delete', 'simple_quiz' )
        ];
        return $actions;
    }
    /**
     * Prepare items in table
     *
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();
        $this->process_submitted_action();
        $per_page     = $this->get_items_per_page( __('Items per page', 'simple_quiz' ), 10 );
        $current_page = $this->get_pagenum();
        $total_items  = $this->record_count();
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ]);
        $this->items = $this->get_questions( $per_page, $current_page );
    }
    /**
     * Process submitted action (deletes in table)
     *
     */
    public function process_submitted_action() {


        $delete_ids = isset( $_REQUEST['ids_delete'] ) ? esc_sql( $_REQUEST['ids_delete'] ) : null;

        if ( 'delete' === $this->current_action() ) {

            $this->delete_question( absint( $_REQUEST['id_question'] ) );

        }
        if ( !empty( $_REQUEST['action2'] ) && !empty( $delete_ids ) ){

            foreach ( $delete_ids as $id ) {

                $this->delete_question( $id );
            }

        }
    }
}