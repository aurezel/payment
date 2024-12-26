<?php

require '../vendor/autoload.php';
require '../config.php';
// 设置 Stripe API 密
\Stripe\Stripe::setApiKey(PERMARY_KEY_LIVE);
 
// Webhook secret来自 Stripe Dashboard）
$endpoint_secret = WEBHOOK_KEY; // "whsec_BZF7iXTP9IW9wDeIwRasskPiFmUYq9xK";//"whsec_o9XGwy4CjmROFwT5D3jkMnx1FLe5q0Hf";

// 获取原请求体名
$input = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
@file_put_contents("debug.txt", "Stripe Signature Header: " . $_SERVER['HTTP_STRIPE_SIGNATURE'] . "\n", FILE_APPEND);
 
$event = null;
 
try {
    // 验 Webhook 名
    $event = \Stripe\Webhook::constructEvent(
        $input, 
        $sig_header, 
        $endpoint_secret
    );
}catch(\UnexpectedValueException $e) {
    //  payload
    http_response_code(400); // 返 400 错代
    echo 'Invalid payload';
    exit();
}catch(\Stripe\Exception\SignatureVerificationException $e) {
    // 签
    http_response_code(400); //  400 代
    echo 'Invalid signature';
    exit();
}
// 成payment_intent.succeeded
switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object; // 取 Checkout Session 对
        
        // 取传的 order_id
        $order_id = $session->metadata->order_id;
        
        // 取金额
        $amount_total = $session->amount_total;  // 总金额
 
        // 取款户（如果
        $customer_id = $session->customer;
        updateOrder($order_id, ['status'=> 2], '../orders.json'); 
        // 这行外的操发件、更库存等
        error_log("Payment for Order $order_id was successful. customer_id ID: $customer_id");
        break;

    default:
        // 事没处
        error_log('Unhandled event type ' . $event->type);
}

// 返成功
http_response_code(200); // 回 200 状码示成处理事

