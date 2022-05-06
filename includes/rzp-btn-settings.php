<?php

require_once __DIR__.'/../templates/razorpay-settings-templates.php';

class RZP_Setting
{
    public function __construct()
    {
        // Initializes display options when admin page is initialized
        add_action('admin_init', array($this, 'displayOptions'));

        // initializing our object with all the setting variables
        $this->title = get_option('title_field');

        $this->description = get_option('description_field');

        $this->keyID = get_option('key_id_field');

        $this->keySecret = get_option('key_secret_field');
        
        $this->paymentAction = get_option('payment_action_field');

        $this->template = new RZP_Payment_Button_Templates();
    }

    /**
     * Generates admin page options using Settings API
    **/
    function razorpaySettings()
    {
        $this->template->razorpaySettings();
    }

    /**
     * Uses Settings API to create fields
    **/
    function displayOptions()
    {
        $this->template->displayOptions();
    }

    /**
     * Settings page header
    **/        
    function displayHeader()
    {
        $this->template->displayHeader();
    }

    /**
     * Enable field of settings page
    **/
    function displayEnable()
    {
        $this->template->displayEnable();
    }

    /**
     * Title field of settings page
    **/
    function displayTitle()
    {   
        $this->template->displayTitle();
    }

    /**
     * Description field of settings page
    **/
    function displayDescription()
    {
        $this->template->displayDescription();
    }

    /**
     * Key ID field of settings page
    **/
    function display_key_id()
    {
        $this->template->display_key_id();
    }

    /**
     * Key secret field of settings page
    **/
    function displayKeySecret()
    {
        $this->template->displayKeySecret();
    }

    /**
     * Payment action field of settings page
    **/
    function display_payment_action()
    {
        $this->template->display_payment_action();
    }
}
