<?php

require_once('api/Okay.php');
require_once 'InvoiceSDK/RestClient.php';
require_once 'InvoiceSDK/CREATE_TERMINAL.php';
require_once 'InvoiceSDK/CREATE_PAYMENT.php';
require_once 'InvoiceSDK/common/ORDER.php';
require_once 'InvoiceSDK/common/SETTINGS.php';

class Invoice extends Okay {
    /**
     * @var RestClient
     */
    private $restClient;
    private $settings;

    public function init($orderId)
    {
        $order = $this->orders->get_order((int)$orderId);
        $payment_method = $this->payment->get_payment_method($order->payment_method_id);
        $this->settings = $this->payment->get_payment_settings($payment_method->id);
        $this->restClient = new RestClient($this->settings['invoice_login'], $this->settings['invoice_api_key']);
    }

    public function checkout_form($orderId) {
        $this->init($orderId);
        $order = $this->orders->get_order((int)$orderId);
        $payment_method = $this->payment->get_payment_method($order->payment_method_id);
        $payment_currency = $this->money->get_currency(intval($payment_method->currency_id));

        $price = round($this->money->convert($order->total_price, $payment_method->currency_id, false), 2);

        $invoice_order = new INVOICE_ORDER_OBJECT($order->total_price);
        $invoice_order->id = $orderId;
        $invoice_order->amount = $price;

        $invoice_settings = new INVOICE_SETTINGS($this->checkOrCreateTerminal());
        $invoice_settings->success_url = $this->config->root_url;

        $request = new CREATE_PAYMENT($invoice_order, $invoice_settings, []);
        $response = $this->restClient->CreatePayment($request);

        if($response == null) throw new Exception("Ошибка при создании платежа!");
        if(isset($response->error)) throw new Exception("Ошибка при создании платежа(".$response->description.")");

        $res['payment_url'] = $response->payment_url;
        $res['price'] = $price;

        return $res;
    }

    public function createTerminal() {
        $request = new CREATE_TERMINAL($this->settings['invoice_terminal_name']);
        $request->type = 'dynamical';

        $response = $this->restClient->CreateTerminal($request);

        if($response == null) throw new Exception("Ошибка при создании терминала");
        if(isset($response->error)) throw new Exception("Ошибка при создании терминала(".$response->description.")");
        $this->saveTerminal($response->id);

        return $response->id;
    }

    public function checkOrCreateTerminal() {
        $tid = $this->getTerminal();
        if($tid == null or empty($tid)) {
            $tid = $this->createTerminal();
        }
        return $tid;
    }

    public function saveTerminal($tid) {
        file_put_contents("invoice_tid", $tid);
    }

    public function getTerminal() {
        if(!file_exists("invoice_tid")) return "";
        return file_get_contents("invoice_tid");
    }
}