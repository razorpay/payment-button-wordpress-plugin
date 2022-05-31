<?php

require_once __DIR__.'/../razorpay-payment-buttons.php';
require_once __DIR__.'/../razorpay-sdk/Razorpay.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors;

if(! class_exists('WP_List_Table'))
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class RZP_Subscription_Buttons extends WP_List_Table
{
    function __construct() 
    {
        parent::__construct( 
            array(
                'singular'  => 'wp_list_text_link', //Singular label
                'plural'    => 'wp_list_test_links', //plural label, also this well be one of the table css class
                'ajax'      => false        //does this table support ajax?
            ) 
        );
    }

    function rzp_buttons() 
    {
        echo '<div>
            <div class="wrap"><h2>Razorpay Subscription Buttons</h2>'; 

            $this->prepare_items();
        
        echo '<input type="hidden" name="page" value="" />
            <input type="hidden" name="section" value="issues" />';

            $this->views();

        echo '<form method="post">
            <input type="hidden" name="page" value="">';
        
        $this->display();
        
        echo '</form></div>
            </div>';
    }

    /**
     * Add columns to grid view
     */
    function get_columns() 
    {
        $columns = array(
            'title'=>__('Title'),
            'total_sales'=>__('Total Sales'),
            'created_at'=>__('Created At'),
            'status'=>__('Status'),
        );

        return $columns;
    }   

    function column_default($item, $column_name)
    {
        switch($column_name) 
        {
            case 'id':
            case 'title':
            case 'total_sales':
            case 'created_at':
            case 'status':
                return $item[ $column_name ];
            default:
                return print_r($item, true) ; //Show the whole array for troubleshooting purposes
        }
    }       
        
    protected function get_views() 
    { 
        $current = 'all';
        
        if(isset($_REQUEST['status']))
        {
            $current = (!empty(sanitize_text_field($_REQUEST['status'])) ? sanitize_text_field($_REQUEST['status']) : 'all');
        }

        $views = array();

        //All Buttons
        $class = ($current === 'all' ? ' class="current"' :'');
        $all_url = remove_query_arg('status');
        $views['all'] = "<a href='{$all_url }' {$class} >All</a>";

        //Recovered link
        $foo_url = add_query_arg('status','active');
        $class = ($current === 'active' ? ' class="current"' :'');
        $views['status'] = "<a href='{$foo_url}' {$class} >Enabled</a>";

        //Abandon
        $bar_url = add_query_arg('status','inactive');
        $class = ($current === 'inactive' ? ' class="current"' :'');
        $views['disabled'] = "<a href='{$bar_url}' {$class} >Disabled</a>";

        return $views;
    }

    function usort_reorder($a, $b)
    {
        if(isset($_GET['orderby']) and isset($_GET['order']))
        {
            // If no sort, default to title
            $orderby = (! empty(sanitize_text_field($_GET['orderby']))) ? sanitize_text_field($_GET['orderby']) : 'title';
            // If no order, default to asc
            $order = (! empty(sanitize_text_field($_GET['order']))) ? sanitize_text_field($_GET['order']) : 'desc';
            // Determine sort order
            $result = strcmp($a[$orderby], $b[$orderby]);

            // Send final sort direction to usort
            return ($order === 'asc') ? $result : -$result;
        }
    }
        
    function get_sortable_columns() 
    {
        $sortable_columns = array(
        'title'  => array('title',false),
        );

        return $sortable_columns;
    }

    function column_title($item) 
    {
        $paged = isset($_REQUEST['paged']) ? $_REQUEST['paged']:1;
        $actions = array(
            'view'      => sprintf('<a href="?page=%s&btn=%s&type=%s&paged=%s">View</a>','rzp_button_view', $item['id'],'subscription',$paged),
        );

        return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions, $always_visible = true));
    }

    /**
    * Prepare admin view
    */  
    function prepare_items() 
    {
        $per_page = 10;
        $current_page = $this->get_pagenum();

        if (1 < $current_page) 
        {
            $offset = $per_page * ($current_page - 1);
        } 
        else 
        {
            $offset = 0;
        }

        //Retrieve $customvar for use in query to get items.
        $customvar = (isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : '');

        $payment_page = $this->get_items($customvar, $per_page);
        $count = count($payment_page);
        $payment_pages = array();

        for($i=0;$i<$count;$i++)
        {
            if($i >= $offset and $i < $offset+$per_page)
            {
                $payment_pages[] = $payment_page[$i];
            }
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);   
        usort($payment_pages, array(&$this, 'usort_reorder'));

        $this->items = $payment_pages;

        // Set the pagination
        $this->set_pagination_args(
            array(
                'total_items' => $count,
                'per_page'    => $per_page,
                'total_pages' => ceil( $count / $per_page )
            )
        );
    }

    function get_items($status, $count)
    {
        $items = array();
        
        $rzp_payment_button_loader = new RZP_Payment_Button_Loader();

        $api = $rzp_payment_button_loader->get_razorpay_api_instance();
        try
        {
            $buttons = $api->paymentPage->all(['view_type' => 'subscription_button', "status" => $status]);
        }
        catch (Exception $e)
        {
            $message = $e->getMessage();

            wp_die('<div class="error notice">
                    <p>RAZORPAY ERROR: Payment button fetch failed with the following message: '.$message.'</p>
                 </div>'); 
        }
        if ($buttons) 
        {
            foreach ($buttons['items'] as $button) 
            {
              $items[] = array(
                'id' => $button['id'],
                'title' => $button['title'],
                'total_sales' => $button['payment_page_items'][0]['quantity_sold'],
                'created_at' => date("d F Y H:i A", $button['created_at']),
                'status' => $button['status'],
              );
            }
          }

        return $items;
    }
}
