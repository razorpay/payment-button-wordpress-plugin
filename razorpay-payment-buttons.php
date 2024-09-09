<?php

/**
 * Plugin Name: Razorpay Payment Button
 * Plugin URI:  https://github.com/razorpay/payment-button-wordpress-plugin
 * Description: Add a Razorpay Payment Button (Donate Now, Buy Now, Support Now and more)  to your website and start accepting payments via Credit/Debit cards, Netbanking, UPI, Wallets, Pay later etc. instantly.
 * Version:     2.4.6
 * Author:      Razorpay
 * Author URI:  https://razorpay.com
 */

require_once __DIR__.'/razorpay-sdk/Razorpay.php';
require_once __DIR__.'/includes/rzp-btn-view.php';
require_once __DIR__.'/includes/rzp-btn-action.php';
require_once __DIR__.'/includes/rzp-btn-settings.php';
require_once __DIR__.'/includes/rzp-payment-buttons.php';
require_once __DIR__.'/includes/rzp-subscription-buttons.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors;

add_action('admin_enqueue_scripts', 'bootstrap_scripts_enqueue', 0);
add_action('admin_post_rzp_btn_action', 'razorpay_payment_button_action');

function bootstrap_scripts_enqueue($admin_page)
{
    wp_register_style('button-css', plugin_dir_url(__FILE__)  . 'public/css/button.css',
        null, null);
    wp_enqueue_style('button-css');

    if ($admin_page != 'admin_page_rzp_button_view')
    {
        return;
    }

    wp_register_style('bootstrap-css', plugin_dir_url(__FILE__)  . 'public/css/bootstrap.min.css',
                null, null);
    wp_enqueue_style('bootstrap-css');
    wp_enqueue_script('jquery');
}

/**
 * This is the RZP Payment button loader class.
 *
 * @package RZP WP List Table
 */
if (!class_exists('RZP_Payment_Button_Loader')) 
{
    // Adding constants
    if (!defined('RZP_PAYMENT_BUTTON_BASE_NAME'))
    {
        define('RZP_PAYMENT_BUTTON_BASE_NAME', plugin_basename(__FILE__));
    }

    if (!defined('RZP_REDIRECT_URL'))
    {
        // admin-post.php is a file that contains methods for us to process HTTP requests
        define('RZP_REDIRECT_URL', esc_url( admin_url('admin-post.php')));
    }

    class RZP_Payment_Button_Loader
    {
        /**
         * Start up
         */
        public function __construct()
        {
            add_action('admin_menu', array($this, 'rzp_add_plugin_page'));
            add_action('enqueue_block_editor_assets', array($this , 'load_razorpay_block'), 10);

            add_filter('plugin_action_links_' . RZP_PAYMENT_BUTTON_BASE_NAME, array($this, 'razorpay_plugin_links'));

            $this->settings = new RZP_Setting();
        }

        /**
         * Creating the menu for plugin after load
        **/
        public function rzp_add_plugin_page()
        {
            /* add pages & menu items */
            add_menu_page(esc_attr__('Razorpay Payment Button', 'textdomain'), esc_html__('Razorpay Buttons' ),
            'administrator','razorpay_button',array($this, 'rzp_view_buttons_page'), '', 10);
            
            add_submenu_page(esc_attr__('razorpay_button', 'textdomain'), esc_html__('Razorpay Settings', 'textdomain'),
            'Settings', 'administrator','razorpay_settings', array( $this, 'razorpay_settings'),1);

            add_submenu_page(esc_attr__('razorpay_button', 'textdomain'), esc_html__('Razorpay Buttons', 'textdomain'),
            'Razorpay Buttons', 'administrator','razorpay_button', array( $this, 'rzp_view_buttons_page'),0);
           
            add_submenu_page(esc_attr__('razorpay_button', 'textdomain'), esc_html__('Razorpay Subscription Buttons', 'textdomain'),
            'Razorpay Subscription Buttons', 'administrator','rzp_subscription_button', array($this, 'rzp_view_Subs_buttons_page'),1);

            add_submenu_page(esc_attr__('', 'textdomain'), esc_html__('Razorpay Buttons', 'textdomain'),
            'Razorpay Buttons', 'administrator','rzp_button_view', array($this, 'rzp_button_view'));
                
            add_submenu_page(esc_attr__('', 'textdomain'), esc_html__('Razorpay Subscription Button', 'textdomain'),
            'Razorpay Subscription Button', 'administrator','rzp_button_view',array( $this, 'rzp_button_view'));
        }

        /**
         * Initialize razorpay api instance
        **/
        public function get_razorpay_api_instance()
        {
            $key = get_option('key_id_field');

            $secret = get_option('key_secret_field');

            if(empty($key) === false and empty($secret) === false)
            {
                return new Api($key, $secret);
            }

            wp_die('<div class="error notice">
                        <p>RAZORPAY ERROR: Payment button fetch failed.</p>
                     </div>'); 
        } 

        /**
         * Initialize razorpay custom block.js and initialize buttons from api
        **/
        public function load_razorpay_block() 
        {
            // Register the script
            wp_register_script('rzp_payment_button', plugin_dir_url(__FILE__) . 'public/js/blocks.js', array(
                    'wp-blocks',
                    'wp-i18n',
                    'wp-element',
                    'wp-components',
                    'wp-editor'
                ) 
            );
            if (! function_exists('get_plugin_data'))
            {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            $mod_version = get_plugin_data(plugin_dir_path(__FILE__) . 'razorpay-payment-buttons.php')['Version'];

            $button_array = array(
                'payment_buttons' => $this->get_buttons(),
                'subscription_button' => $this->get_subscription_button(),
                'payment_buttons_plugin_version' => $mod_version,
            );

            // Localize the script with new data
            wp_localize_script('rzp_payment_button', 'razorpay', $button_array);
             
            // Enqueued script with localized data.
            wp_enqueue_script('rzp_payment_button');
        }

        public function get_buttons() 
        {
            $buttons = array();

            $api = $this->get_razorpay_api_instance();

            try
            {
                $items = $api->paymentPage->all(['view_type' => 'button', "status" => 'active', 'count' => 100]);
            }
            catch (\Exception $e)
            {
                $message = $e->getMessage();

                wp_die('<div class="error notice">
                    <p>RAZORPAY ERROR: Payment button fetch failed with the following message: '.$message.'</p>
                 </div>');
            }

            if ($items) 
            {
                foreach ($items['items'] as $item) 
                {
                    $buttons[] = array(
                        'id' => $item['id'],
                        'title' => $item['title']
                    );
                }
            }
         
            return $buttons;
        }

        /** subscription button function from Api */
        public function get_subscription_button() 
        {
            $buttons = array();

            $api = $this->get_razorpay_api_instance();

            try
            {
                $items = $api->paymentPage->all(['view_type' => 'subscription_button', "status" => 'active']);
            }
            catch (\Exception $e)
            {
                $message = $e->getMessage();

                wp_die('<div class="error notice">
                    <p>RAZORPAY ERROR: Payment button fetch failed with the following message: '.$message.'</p>
                 </div>');
            }

            if ($items) 
            {
                foreach ($items['items'] as $item) 
                {
                    $buttons[] = array(
                        'id' => $item['id'],
                        'title' => $item['title']
                    );
                }
            }

            return $buttons;
        }
        
        /**
         * Creating the settings link from the plug ins page
        **/
        function razorpay_plugin_links($links)
        {
            $pluginLinks = array(
                            'settings' => '<a href="'. esc_url(admin_url('admin.php?page=razorpay_settings')) .'">Settings</a>',
                            'docs'     => '<a href="https://razorpay.com/docs/payment-button/supported-platforms/wordpress/">Docs</a>',
                            'support'  => '<a href="https://razorpay.com/contact/">Support</a>'
                        );

            $links = array_merge($links, $pluginLinks);

            return $links;
        }

        /**
         * Razorpay Payment Button Page
         */
        public function rzp_view_buttons_page()
        {
            $rzp_payment_buttons = new RZP_Payment_Buttons();

            $rzp_payment_buttons->rzp_buttons();
        }	

        /**
         * Razorpay Subscription Button Page
         */
        public function rzp_view_Subs_buttons_page()
        {
            $rzp_payment_buttons = new RZP_Subscription_Buttons();

            $rzp_payment_buttons->rzp_buttons();
        }
        
        /**
         * Razorpay Setting Page
         */
        public function razorpay_settings()
        {
            $this->settings->razorpaySettings();
        }  

        /**
         * Razorpay Setting Page
         */
        public function rzp_button_view()
        {
            $new_button = new RZP_View_Button();

            $new_button->razorpay_view_button();
        }
    }
}

/**
* Instantiate the loader class.
*
* @since     2.0
*/
$RZP_Payment_Button_Loader = new RZP_Payment_Button_Loader();

function razorpay_payment_button_action()
{
    $btn_action = new RZP_Button_Action();
    
    $btn_action->process();
}
