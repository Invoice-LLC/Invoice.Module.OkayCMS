<?php


class CREATE_PAYMENT
{
    /**
     * @var INVOICE_ORDER_OBJECT
     */
    public $order;
    /**
     * @var INVOICE_SETTINGS
     */

    public $settings;
    /**
     * @var array
     */
    public $custom_parameters;
    /**
     * @var array(ITEM)
     */
    public $receipt;

    /**
     * Optional fields
     * @var $mail string
     * @var $phone string
     */
    public $mail;
    public $phone;

    /**
     * CREATE_PAYMENT constructor.
     * @param $order INVOICE_ORDER_OBJECT
     * @param $settings INVOICE_SETTINGS
     * @param $receipt array
     */
    public function __construct($order, $settings, $receipt)
    {
        $this->settings = $settings;
        $this->order = $order;
        $this->receipt = $receipt;
    }
}