<?php

require_once __DIR__.'/../razorpay-payment-buttons.php';
require_once __DIR__.'/../razorpay-sdk/Razorpay.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors;

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class RZP_Payment_Buttons extends WP_List_Table {
		
    function __construct() {
		parent::__construct( 
            array(
                'singular'  => 'wp_list_text_link', //Singular label
                'plural'    => 'wp_list_test_links', //plural label, also this well be one of the table css class
    			'ajax'      => false        //does this table support ajax?
            ) 
        );
		
	}

    public function get_Razorpay_Api_Instance() {

        $key = get_option('key_id_field');

        $secret = get_option('key_secret_field');

        return new Api($key, $secret);
    }   

	function rzp_buttons() {
		echo '<div>
            <div class="wrap"><h2>Razorpay Buttons</h2>'; 

            $this->prepare_items();
		
        echo '<input type="hidden" name="page" value="" />
            <input type="hidden" name="section" value="issues" />';

            $this->views();

		echo '<form method="post">
            <input type="hidden" name="page" value="">';
		
        $this->search_box( 'search', 'search_id' );
		$this->display();  
		
        echo '</form></div>
            </div>';
	}

	/**
	 * Add columns to grid view
	 */
	function get_columns() {

        $columns = array(
            'title'=>__('Title'),
            'total_sales'=>__('Total Sales'),
            'created_at'=>__('Created At'),
            'status'=>__('Status'),
        );

		return $columns;
	}	

	function column_default( $item, $column_name ) {
		switch( $column_name ) {
            case 'id':
            case 'title':
            case 'total_sales':
            case 'created_at':
            case 'status':
                return $item[ $column_name ];

            default:
		  
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}		
		
    protected function get_views() { 
        $views = array();
        $current = ( !empty($_REQUEST['status']) ? $_REQUEST['status'] : 'all');

        //All Buttons
        $class = ($current == 'all' ? ' class="current"' :'');
        $all_url = remove_query_arg('status');
        $views['all'] = "<a href='{$all_url }' {$class} >All</a>";

        //Recovered link
        $foo_url = add_query_arg('status','active');
        $class = ($current == 'active' ? ' class="current"' :'');
        $views['status'] = "<a href='{$foo_url}' {$class} >Enabled</a>";

        //Abandon
        $bar_url = add_query_arg('status','inactive');
        $class = ($current == 'inactive' ? ' class="current"' :'');
        $views['disabled'] = "<a href='{$bar_url}' {$class} >Disabled</a>";

        return $views;
	}
		
		
		
    function usort_reorder( $a, $b ) {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'title';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }
		
    function get_sortable_columns() {
        $sortable_columns = array(
        'title'  => array('title',false),
        );
        return $sortable_columns;
    }

    function column_title($item) {
        $actions = array(
            'view'      => sprintf('<a href="?page=%s&btn=%s">View</a>','rzp_button_view', $item['id']),
        );

        return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions, $always_visible = true ) );
    }

    /**
    * Prepare admin view
    */	
    function prepare_items() {

        $per_page = 2;
        $current_page = $this->get_pagenum();
        if ( 1 < $current_page ) {
        	$offset = $per_page * ( $current_page - 1 );
        } else {
        	$offset = 0;
        }

        //Retrieve $customvar for use in query to get items.
        $customvar = ( isset($_REQUEST['status']) ? $_REQUEST['status'] : '');

        $payment_pages = $this->get_items($customvar, $per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);	
        usort( $payment_pages, array( &$this, 'usort_reorder' ) );

        $count = count($payment_pages);
        $this->items = $payment_pages;

        // Set the pagination
        $this->set_pagination_args( array(
        	'total_items' => $count,
        	'per_page'    => $per_page,
        	'total_pages' => ceil( $count / $per_page )
        ) );
    }

    function get_items($status, $count){
        $items = array();
        
        $api = $this->get_Razorpay_Api_Instance();

        try
        {
            $buttons = $api->paymentPage->all(['view_type' => 'button', "status" => $status]);
        }
        catch (Exception $e)
        {
            $message = $e->getMessage();

            throw new Exception("RAZORPAY ERROR: Payment button fetch failed with the following message: '$message'");
        }
        if ( $buttons ) {
            foreach ( $buttons['items'] as $button ) {
              $items[] = array(
                'id' => $button['id'],
                'title' => $button['title'],
                'total_sales' => '<span class="rzp-currency">₹</span> '.(int) round($button['total_amount_paid'] / 100),
                'created_at' => date("d F Y H:i A", $button['created_at']),
                'status' => $button['status'],
              );
            }
          }
        return $items;
    }
		

}