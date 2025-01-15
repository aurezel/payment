<?php

require '../vendor/autoload.php';
require '../config.php';

// 设置 Stripe API 密
//\Stripe\Stripe::setApiKey(PERMARY_KEY_LIVE);
\Stripe\Stripe::setApiKey(SECRET_KEY_LIVE);
// Webhook secret来自 Stripe Dashboard）
$endpoint_secret = WEBHOOK_KEY; // "whsec_BZF7iXTP9IW9wDeIwRasskPiFmUYq9xK";//"whsec_o9XGwy4CjmROFwT5D3jkMnx1FLe5q0Hf";

// 获取原请求体名
$input = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

$decodedInput = json_decode($input, true);
if ($decodedInput !== null) {
    $compressedInput = json_encode($decodedInput); // 压缩 JSON 数据
} else {
    $compressedInput = $input; // 保留原始数据（如果不是 JSON 格式）
}
@file_put_contents("debug.txt", $compressedInput . "\n==================".date("Y-m-d H:i:s")."show all=====================\n", FILE_APPEND);


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

@file_put_contents("debugEvent.txt",  $event->type."\n\n", FILE_APPEND);
// 成payment_intent.succeeded
switch ($event->type) {
    case 'checkout.session.completed':
        $checkout = $event->data->object;
        $orderId = $checkout->metadata->order_id;
        $paymentIntentId = $checkout->payment_intent;
        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
        $transactionNumber = $paymentIntent->latest_charge;
        $order = getOrder($orderId, '../orders.json');
        $note = '';
        $status = 0;//'succeeded | canceled | processing';
        if($paymentIntent->status == 'succeeded'){
            $status = 1;
        }else if($paymentIntent->status == 'canceled'){
            $status = 2;
        }

        if ($paymentIntent->status === 'requires_payment_method') {
            // 获取失败的具体原因
            if (isset($paymentIntent->last_payment_error)) {
//                echo "Error Code: " . $paymentIntent->last_payment_error->code . "\n";
                $note = $paymentIntent->last_payment_error->message . "\n";
//                echo "Decline Code: " . $paymentIntent->last_payment_error->decline_code . "\n";
            }
        }

        if ($order['status'] == 1) {
            $order['note'] = $note;
            $order['transactionNumber'] = $transactionNumber;
            createOrder($order, 'orders.json');
         } else {
            $data = [];
            $data['transactionNumber'] = $transactionNumber;
            $data['status'] = $status;
            $data['note'] = $note;
            updateOrder($orderId, $data, '../orders.json');
        }

        break;
    case 'charge.failed':
//        $charge = $event->data->object;
//        @file_put_contents("debugfailure.txt",  "{$charge}\nstart ************************start\n", FILE_APPEND);
    case 'charge.succeeded':
//        $charge = $event->data->object;
//        @file_put_contents("debugscccess.txt",  "{$charge}\nstart ************************start\n", FILE_APPEND);
//        break;
    default:
        // 事没处
        error_log('Unhandled event type ' . $event->type);
}

// 返成功
http_response_code(200); // 回 200 状码示成处理事

