<?php

chdir ('../../');
require_once('api/Okay.php');

class callback {
    private $okay;
    private $settings;

    public function __construct($settings, $okay)
    {
        $this->okay = $okay;
        $this->settings = $settings;
    }

    public function handle($notification) {
        $type = $notification["notification_type"];
        $id = $notification["order"]["id"];
        $order = $this->getOrder($id);

        $signature = $notification["signature"];

        if($signature != $this->getSignature($notification["id"], $notification["status"], $this->settings['invoice_api_key'])) {
            return "Wrong signature";
        }

        if($type == "pay") {

            if($notification["status"] == "successful") {
                $this->okay->orders->update_order(intval($order->id), array('paid'=>1));

                $this->okay->notify->email_order_user(intval($order->id));
                $this->okay->notify->email_order_admin(intval($order->id));

                $this->okay->orders->close(intval($order->id));
                return "payment successful";
            }
            if($notification["status"] == "error") {
                return "payment failed";
            }
        }

        return "null";
    }

    public function getOrder($id) {
        return $this->okay->orders->get_order(intval($id));
    }

    public function getSignature($id, $status, $key) {
        return md5($id.$status.$key);
    }
}

$okay = new Okay();
$postData = file_get_contents('php://input');
$notification = json_decode($postData, true);

$order = $okay->orders->get_order(intval($notification['order']['id']));

$method = $okay->payment->get_payment_method(intval($order->payment_method_id));
$settings = unserialize($method->settings);


$callback = new callback($settings, $okay);
die($callback->handle($notification));