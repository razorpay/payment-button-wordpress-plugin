<?php

require_once __DIR__.'/../templates/razorpay-settings-templates.php';

class RZP_Setting
{
    public function __construct()
    {
        // Initializes display options when admin page is initialized
        add_action('admin_init', array($this, 'display_options'));

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
    function razorpay_settings()
    {
        $this->template->razorpay_settings();
    }
	/**
     * Uses Settings API to create fields
    **/
    function display_options()
    {
        $this->template->display_options();
    }

    /**
     * Settings page header
    **/        
    function display_header()
    {
        $this->template->display_header();
    }

    /**
     * Enable field of settings page
    **/
    function display_enable()
    {
        $this->template->display_enable();
    }

    /**
     * Title field of settings page
    **/
    function display_title()
    {   
        $this->template->display_title();
    }

    /**
     * Description field of settings page
    **/
    function display_description()
    {
        $this->template->display_description();
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
    function display_key_secret()
    {
        $this->template->display_key_secret();
    }

    /**
     * Payment action field of settings page
    **/
    function display_payment_action()
    {
        $this->template->display_payment_action();
    }
}
