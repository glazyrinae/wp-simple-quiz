<?php
/**
 * Controller for table quizzes
 * @since      1.0.0
 * @package    simple_quiz
 * @subpackage admin/classes
 * @author     Glazyrin Andrey <railot116@example.com>
 *
 */
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class WPSSQ_Render_Table_Quizzes extends WP_List_Table {

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
	public function get_quiz_list( $per_page = 5, $page_number = 1 ) {

		$sql = 'SELECT * FROM '.SSQ_TBL_QUIZZES;
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

	public function delete_quiz( $id ) {

	    $this->__db->delete(
            SSQ_TBL_QUIZZES,
			[ 'id_quiz' => $id ],
			[ '%d' ]
		);
	}

	public function count_questions( $id_quiz ) {
		return $this->__db->get_var(
		    'SELECT 
             COUNT(*) 
                FROM '.SSQ_TBL_QUESTIONS.'
		     WHERE 
		        id_quiz='.$id_quiz );
	}

	public function count_results( $id_quiz ){
	    return $this->__db->get_var( 'SELECT COUNT(*) FROM '.SSQ_TBL_RESULTS.'
		WHERE id_quiz='.$id_quiz );
    }


	public function no_items() {
        _e( 'No quiz available.', 'simple_quiz' );
	}

	function column_title( $item ) {

		$delete_nonce = wp_create_nonce( 'id_quiz_'.$item['id_quiz'] );
		$title = '<strong>' . esc_attr($item['title']) . '</strong>';
		$actions = [
			'edit'   => sprintf( '<a href="?page=do_quiz&id_quiz=%s">'.
                __( 'Edit', 'simple_quiz' ).'</a>',absint( $item['id_quiz'] ) ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&id_quiz=%s&_wpnonce=%s">'.
                __( 'Delete', 'simple_quiz' ).'</a>',
                esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id_quiz'] ), $delete_nonce ),
        ];
		return $title . $this->row_actions( $actions );
	}

	public function column_default( $item, $column_name ) {

	    switch ( $column_name ) {
			case 'title':
			case 'date':
                return $item[ $column_name ];
			case 'short_code':
                return '[quiz id = '.$item['id_quiz'].']';
            case 'count_questions':
                return $this->count_questions( $item['id_quiz'] );
            case 'rating_quizzes':
                return (int)$this->count_results( $item['id_quiz'] );
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="ids_delete[]" value="%s" />', $item['id_quiz']
		);
	}

	function get_columns() {
		$columns = [
			'cb'      		  => '<input type="checkbox" />',
			'title'    		  => __( 'Title', 'simple_quiz' ),
			'date'    		  => __( 'Date', 'simple_quiz' ),
			'short_code'      => __( 'Short code', 'simple_quiz' ),
            'count_questions' => __( 'Count questions', 'simple_quiz' ),
            'rating_quizzes'  => __( 'Rating', 'simple_quiz' ),
		];
		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = [
            'title'=> [ 'title', false ],
            'date' => [ 'date',  true  ]
			//'count_questions'=>[ 'count_questions',  true  ]
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
		$per_page     = $this->get_items_per_page( __('Items_per_page', 'simple_quiz' ), 10 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();
		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		]);
		$this->items = $this->get_quiz_list( $per_page, $current_page );
	}
	
	public function process_submitted_action() {

        $delete_ids = isset( $_REQUEST['ids_delete'] ) ? esc_sql( $_REQUEST['ids_delete'] ) : null;

        if ( 'delete' === $this->current_action() ) {
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			if ( ! wp_verify_nonce( $nonce, 'id_quiz_'.$_REQUEST['id_quiz'] ) ) {
				die( __( 'Verification Error', 'simple_quiz' ) );
			}
			else {
				$this->delete_quiz( absint( $_REQUEST['id_quiz'] ) );
			}
		}

        if ( !empty( $_REQUEST ) && !empty( $delete_ids ) ){

			foreach ( $delete_ids as $id ) {
				$this->delete_quiz( $id );
			}
		}
	}
}	