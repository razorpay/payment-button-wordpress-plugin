<?php

class RZP_Payment_Button_Templates
{
    /**
     * Generates admin page options using Settings API
    **/
    function razorpaySettings()
    {
        echo
            '<div class="wrap">
                <h2>Razorpay Payment Button Settings</h2>
                <form action="options.php" method="POST">';

                    settings_fields('razorpay_fields');
                    do_settings_sections('razorpay_sections_pb');
                    submit_button();

        echo
                '</form>
            </div>';
    }

    /**
     * Uses Settings API to create fields
    **/
    function displayOptions()
    {
        add_settings_section('razorpay_fields', 'Edit Settings', array($this, 'displayHeader'), 'razorpay_sections_pb');

        $settings = $this->get_settings();

        foreach ($settings as $settingField => $settingName)
        {
            $displayMethod = $this->get_display_setting_method($settingField);

            add_settings_field(
                $settingField,
                $settingName,
                array(
                    $this,
                    $displayMethod
                ),
                'razorpay_sections_pb',
                'razorpay_fields'
            );

            register_setting('razorpay_fields', $settingField);
        }
    }

    /**
     * Settings page header
    **/
    function displayHeader()
    {
        $header = '<p>Razorpay is an online payment gateway for India with transparent pricing, seamless integration and great support</p>';

        echo $header;
    }

    /**
     * Enable field of settings page
    **/
    function displayEnabled()
    {
        $default = get_option('enabled_field');

        $enable = <<<EOT
<input type="checkbox" name="enabled_field" id="enable" value="{$default}" checked/>
<label for ="enable">Enable Razorpay Payment Button Module.</label>
EOT;

        echo $enable;
    }

    /**
     * Title field of settings page
    **/
    function displayTitle()
    {
        $default = get_option('title_field', "Credit Card/Debit Card/NetBanking");

        $title = <<<EOT
<input type="text" name="title_field" id="title" size="35" value="{$default}" /><br>
<label for ="title">This controls the title which the user sees during checkout.</label>
EOT;

        echo $title;
    }

    /**
     * Description field of settings page
    **/
    function displayDescription()
    {
        $default = get_option('description_field', "Pay securely by Credit or Debit card or internet banking through Razorpay");

        $description = <<<EOT
<input type="text" name="description_field" id="description" size="35" value="{$default}" /><br>
<label for ="description">This controls the display which the user sees during checkout.</label>
EOT;

        echo $description;
    }

    /**
     * Key ID field of settings page
    **/
    function displayKeyID()
    {
        $default = get_option('key_id_field');

        $keyID = <<<EOT
<input type="text" name="key_id_field" id="key_id" size="35" value="{$default}" /><br>
<label for ="key_id">The key Id and key secret can be generated from "API Keys" section of Razorpay Dashboard. Use test or live for test or live mode.</label>
EOT;

        echo $keyID;
    }

    /**
     * Key secret field of settings page
    **/
    function displayKeySecret()
    {
        $default = get_option('key_secret_field');

        $keySecret = <<<EOT
<input type="text" name="key_secret_field" id="key_secret" size="35" value="{$default}" /><br>
<label for ="key_id">The key Id and key secret can be generated from "API Keys" section of Razorpay Dashboard. Use test or live for test or live mode.</label>
EOT;

        echo $keySecret;
    }

    /**
     * Payment action field of settings page
    **/
    function displayPaymentAction()
    {
        $default = get_option('payment_action_field');

        $selected_capture = ($default === 'capture') ? 'selected' : '' ;
        $selected_authorize = ($default === 'authorize') ? 'selected' : '' ;

        $paymentAction = <<<EOT
<select name="payment_action_field" id="payment_action" value="{$default}" />
    <option value="capture" {$selected_capture}>Authorize and Capture</option>
    <option value="authorize" {$selected_authorize}>Authorize</option>
</select>
<br>
<label for ="payment_action">Payment action when order is compelete.</label>
EOT;

        echo $paymentAction;
    }

    protected function get_settings()
    {
        $settings = array(
            'enabled_field'        => 'Enabled/Disabled',
            'title_field'          => 'Title',
            'description_field'    => 'Description',
            'key_id_field'         => 'Key_id',
            'key_secret_field'     => 'Key_secret',
            'payment_action_field' => 'Payment_action'
        );

        return $settings;
    }

    protected function get_display_setting_method($settingsField)
    {
        $settingsField = ucwords($settingsField);

        $fieldWords = explode('_', $settingsField);

        array_pop($fieldWords);

        return 'display' . implode('', $fieldWords);
    }
}
