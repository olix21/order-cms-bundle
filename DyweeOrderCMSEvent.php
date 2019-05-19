<?php

namespace Dywee\OrderCMSBundle;

final class DyweeOrderCMSEvent
{
    const DISPLAY_BASKET            = 'dywee_order_cms.display_basket';
    const VALID_BASKET              = 'dywee_order_cms.valid_basket';
    const DISPLAY_BILLING           = 'dywee_order_cms.display_billing';
    const VALID_BILLING             = 'dywee_order_cms.valid_billing';
    const DISPLAY_SHIPPING          = 'dywee_order_cms.display_shipping';
    const VALID_SHIPPING            = 'dywee_order_cms.valid_shipping';
    const DISPLAY_SHIPPING_METHODS  = 'dywee_order_cms.display_shipping_methods';
    const DISPLAY_PAYMENT_METHOD    = 'dywee_order_cms.display_payment_method';
    const VALID_PAYMENT_METHOD      = 'dywee_order_cms.valid_payment_method';
    const DISPLAY_RECAP             = 'dywee_order_cms.display_recap';
    const REDIRECT_TO_PAYMENT       = 'dywee_order_cms.redirect_to_payment';
    const VALID_PAYMENT             = 'dywee_order_cms.valid_payment';
}
