<?php
/**
 * Controller for table results
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/classes
 * @author     Glazyrin Andrey <railot116@example.com>
 *
 */
if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class WPSSQ_Render_Table_Results extends WP_List_Table {

    private $__params;
    private $__db;

    public function __construct( $db ) {

        $this->__db          = $db;
        parent::__construct([
            'singular' => __( 'Simple quiz', 'simple_quiz' ), //singular name of the listed records
            'plural'   => __( 'Simple quiz', 'simple_quiz' ), //plural name of the listed records
            'ajax'     => false //should this table support ajax?
        ]);

    }
    public function get_list( $per_page = 5, $page_number = 1 ) {

        $sql = 'SELECT quz.title AS title, 
                       res.user_info AS name, 
                       res.id_quiz AS id_quiz,
                       res.id_result AS id_result, 
                       res.date_quiz AS date,
                       (CASE 
                            WHEN res.status=0 THEN "proccesing"
                            WHEN res.status=1 THEN "complited"
                            ELSE NULL      
                       END) AS status,
                       res.total_right_answers AS right_questions,
                       ( SELECT COUNT(*) FROM '.SSQ_TBL_QUESTIONS. ' WHERE id_quiz=res.id_quiz  ) AS total_questions'
            .' FROM '.SSQ_TBL_RESULTS.' AS res, '.SSQ_TBL_QUIZZES.' AS quz WHERE res.id_quiz=quz.id_quiz';

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ?  ' '
                . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
        $sql .= ' LIMIT '. $per_page;
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $this->__db->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    public function delete_result( $id ) {

        $this->__db->delete(
            SSQ_TBL_RESULTS,
            [ 'id_result' => $id ],
            [ '%d' ]
        );
    }

    public function count_questions( $id_quiz ) {
        return $this->__db->get_var( 'SELECT COUNT(*) FROM '.SSQ_TBL_RESULTS);
    }


    public function no_items() {
        _e( 'No results available.', 'simple_quiz' );
    }

    function column_name( $item ) {

        $delete_nonce = wp_create_nonce( 'id_result_'.$item['id_result'] );
        $title = '<strong>' . $item['name'] . '</strong>';
        $actions = [
            //'show'   => sprintf( '<a href="?page=do_quiz&id_result=%s">Show Result</a>',absint( $item['id_result'] ) ),
            'delete' => sprintf( '<a href="?page=%s&action=%s&id_result=%s&_wpnonce=%s">'.
                __( 'Delete', 'simple_quiz' ).'</a>',
                esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id_result'] ), $delete_nonce ),
        ];
        return $title . $this->row_actions( $actions );
    }

    public function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'name':
            case 'date':
            case 'status':
            case 'title':
                return $item[ $column_name ];
            case 'right_questions':
                return _e( 'Right answers: ', 'simple_quiz' ).'<strong>' .
                    $item[ 'right_questions' ].'</strong> ('.$item[ 'total_questions' ].')';

            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="ids_delete[]" value="%s" />', $item['id_result']
        );
    }

    function get_columns() {
        $columns = [
            'cb'      		  => '<input type="checkbox" />',
            'name'    		  => __( 'Login', 'simple_quiz' ),
            'date'    		  => __( 'Date', 'simple_quiz' ),
            'status'          => __( 'Status', 'simple_quiz' ),
            'title'           => __( 'Title quiz', 'simple_quiz' ),
            'right_questions' => __( 'Result', 'simple_quiz' ),
        ];
        return $columns;
    }

    public function get_sortable_columns() {
        $sortable_columns = [
            'name' => [ 'name', false ],
            'date' => [ 'date',  true  ]
        ];
        return $sortable_columns;
    }

    public function get_bulk_actions() {
        $actions = [
            'ids_delete' => __( 'Delete', 'simple_quiz' )
        ];
        return $actions;
    }

    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();
        $this->process_submitted_action();
        $per_page     = $this->get_items_per_page( 'items_per_page', 10 );
        $current_page = $this->get_pagenum();
        $total_items  = $this->record_count();
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ]);
        $this->items = $this->get_list( $per_page, $current_page );
    }

    public function process_submitted_action() {

        $delete_ids = isset( $_REQUEST['ids_delete'] ) ? esc_sql( $_REQUEST['ids_delete'] ) : null;

        if ( 'delete' === $this->current_action() ) {
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );
            if ( ! wp_verify_nonce( $nonce, 'id_result_'.$_REQUEST['id_result'] ) ) {
                die( __( 'Verification Error', 'simple_quiz' ) );
            }
            else {
                $this->delete_result( absint( $_REQUEST['id_result'] ) );
            }
        }
        if ( !empty( $_REQUEST['action2'] ) && !empty( $delete_ids ) ){
            foreach ( $delete_ids as $id ) {
                $this->delete_result( $id );
            }
        }
    }
}