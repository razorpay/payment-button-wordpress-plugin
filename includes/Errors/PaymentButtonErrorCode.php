<?php

namespace Razorpay\PaymentButton\Errors;

use Razorpay\Api\Errors as ApiErrors;

class Payment_Button_Error_Code extends ApiErrors\ErrorCode
{
    // Razorpay Payment Button
    const API_PAYMENT_BUTTON_FETCH_FAILED     = 'Razorpay payment button fetch request failed';
    const API_PAYMENT_BUTTON_ACTION_FAILED     = 'Razorpay API payment button activate/deactivate failed';
}
