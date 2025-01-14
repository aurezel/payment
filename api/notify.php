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

$decodedInput = json_decode($input, true);
if ($decodedInput !== null) {
    $compressedInput = json_encode($decodedInput); // 压缩 JSON 数据
} else {
    $compressedInput = $input; // 保留原始数据（如果不是 JSON 格式）
}
@file_put_contents("debugok.txt", $compressedInput . "\n==================".date("Y-m-d H:i:s")."show all=====================\n", FILE_APPEND);


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

@file_put_contents("debugEvent.txt",  $event->type."\n", FILE_APPEND);
// 成payment_intent.succeeded
switch ($event->type) {
//    case 'checkout.session.completed':
//        break;
    case 'charge.succeeded':
        $charge = $event->data->object;
        @file_put_contents("debugmany.txt",  $charge.":end 1 \n\n\n\n", FILE_APPEND);
        $paymentIntentId = $charge->payment_intent;
        $transactionNumber = $charge->id;
        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
        @file_put_contents("debugmany.txt",  $paymentIntent.":end 2 \n\n\n\n", FILE_APPEND);
// 检查是否成功获取到 PaymentIntent 对象
        if ($paymentIntent) {
            // 打印 debug 信息，查看 PaymentIntent 的内容
            @file_put_contents("debugm.txt", "start***************start:".print_r($paymentIntent, true) . "\n", FILE_APPEND);

            // 检查是否有 metadata 并且包含 order_id
            if (isset($paymentIntent->metadata) && isset($paymentIntent->metadata->order_id)) {
                $orderId = $paymentIntent->metadata->order_id;
            } else {
                // 如果没有找到 order_id，输出相应的调试信息
                @file_put_contents("debugm.txt", "Order ID not found in metadata.\n", FILE_APPEND);
            }
        } else {
            @file_put_contents("debugm.txt", "Failed to retrieve PaymentIntent.\n", FILE_APPEND);
        }
//        $charge = $event->data->object;
//        @file_put_contents("debugmany.txt",  $charge."\n\n\n\n", FILE_APPEND);
//        $paymentIntentId = $charge->payment_intent;
//        $transactionNumber = $charge->id;
////        @file_put_contents("debugmany.txt",  $event->type."\n", FILE_APPEND);
////        @file_put_contents("debugmany.txt",  $paymentIntentId.' '.$transactionNumber."xx\n", FILE_APPEND);
//        // 获取订单 ID
//        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
//        @file_put_contents("debugm.txt",  $paymentIntent."\n", FILE_APPEND);
//        $orderId = $paymentIntent->metadata->order_id;
//        @file_put_contents("debugmany.txt",  "test\n".$paymentIntent."\n", FILE_APPEND);
//        $data = [];
//        $data['status'] = 1;
//        $data['transactionNumber'] = $transactionNumber;
//        @file_put_contents("debug2.txt",  "\n==================".$orderId."show order_id=====================\n", FILE_APPEND);
//
//        // 更新数据库中的订单状态为支付成功
//        $order = getOrder($orderId, '../orders.json');
//        if ($order['status'] == 1) {
//            $order['note'] = 'Repeat Orders';
//            $order['transactionNumber'] = $transactionNumber;
//            createOrder($order, 'orders.json');
//         } else {
//            $data['status'] = 1;
//            updateOrder($orderId, $data, '../orders.json');
//        }
//        @file_put_contents("debug2.txt", "\n".json_encode($data) . "\n==================".date("Y-m-d H:i:s")."show success=====================\n", FILE_APPEND);

        break;
    case 'charge.failed':
        $charge = $event->data->object;
        $orderId = $charge->metadata->order_id; // 如果你在 charge 中存储了 order_id
        $failureMessage = $charge->failure_message; // 获取失败的原因
        // 更新数据库中的订单状态为失败，并记录失败原因
        updateOrder($orderId, ['status'=>2,'note'=>$failureMessage], '../orders.json');
        break;
    case "payment_intent.payment_failed":
        $paymentIntent = $event->data->object;
        $orderId = $paymentIntent->metadata->order_id;
        $failureMessage = $paymentIntent->last_payment_error->message;  // 获取失败原因

        // 更新数据库中的订单状态为失败，并记录失败原因
        updateOrder($orderId, ['status'=>2,'note'=>$failureMessage], '../orders.json');
        break;
    default:
        // 事没处
        error_log('Unhandled event type ' . $event->type);
}

// 返成功
http_response_code(200); // 回 200 状码示成处理事

