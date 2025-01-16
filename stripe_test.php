<?php
date_default_timezone_set('Asia/Shanghai');
//ini_set('session.gc_maxlifetime', 3600);
//session_start();
header('Content-Type: application/json');
// 引入自动加载文件和配置文件
require 'vendor/autoload.php';
require_once 'config.php';
require_once 'session.php';

stripe_payout_charge();
function stripe_payout_charge(){
//    $paymentIntent = \Stripe\PaymentIntent::retrieve('pi_3Qh3I3ChUD7GJMbF1joNTDWl');
    \Stripe\Stripe::setApiKey(SECRET_KEY_LIVE);
    try {
        // 获取当前时间和一周前的时间戳
        $endDate = time(); // 当前时间
        $startDate = strtotime('-7 days', $endDate); // 一周前的时间

        // 查询过去一周的提现记录
        $payouts = \Stripe\Payout::all([
            'created' => ['gte' => $startDate, 'lte' => $endDate], // 时间范围：一周前到现在
            'limit' => 10 // 每次查询的最大记录数
        ]);
        echo '<pre>';
        print_r($payouts);
        // 遍历提现记录
        foreach ($payouts->data as $payout) {
            echo "提现 ID: " . $payout->id . "<br>";
            echo "提现金额: " . ($payout->amount / 100) . " " . strtoupper($payout->currency) . "<br>";
            echo "提现状态: " . $payout->status . "<br>";
            echo "创建时间: " . date('Y-m-d H:i:s', $payout->created) . "<br><br>";
            stripe_transaction_list($payout->id);
        }

    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo "获取提现记录失败: " . $e->getMessage();
    }
}
function stripe_transaction_list($payoutId){
    try {

        // 获取提现记录

        // 获取与提现相关的 Balance Transactions
        $balanceTransactions = \Stripe\BalanceTransaction::all([
            'payout' => $payoutId,
            'limit' => 50
        ]);
        echo '<pre>';
        print_r($balanceTransactions);
        echo '</pre>';
        // 遍历 Balance Transactions 并获取订单信息
        foreach ($balanceTransactions->data as $transaction) {
            echo "交易 ID: " . $transaction->id . "<br>";
            echo "交易金额: " . ($transaction->amount / 100) . " " . strtoupper($transaction->currency) . "<br>";
            echo "交易类型: " . $transaction->type . "<br>";
            echo "关联订单（Source ID）: " . $transaction->source . "<br>";
            echo "交易创建时间: " . date('Y-m-d H:i:s', $transaction->created) . "<br><br>";

            // 如果 source 是 Charge，获取订单详情
            if (strpos($transaction->source, 'ch_') === 0) {
                $charge = \Stripe\Charge::retrieve($transaction->source);
                echo "-- 订单金额: " . ($charge->amount / 100) . " " . strtoupper($charge->currency) . "<br>";
                echo "-- 订单状态: " . $charge->status . "<br>";
                echo "-- 订单创建时间: " . date('Y-m-d H:i:s', $charge->created) . "<br><br>";
            }
        }

    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo "获取提现记录失败: " . $e->getMessage();
    }
}
function stripe_checkout_cart(){
    \Stripe\Stripe::setApiKey(SECRET_KEY_LIVE);
    try {
        // 创建 Stripe Checkout 话
        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => "USD",  // 必须：货币类型
                        'product_data' => [
                            'name' => 'Total', //$order_id,  // 必须：商品名称
                        ],
                        'unit_amount' => 3200,  // 必须：商品价格，单位为最小货币单位
                    ],
                    'quantity' => 1,  // 必须：商品数量
                ],
            ],
            'mode' => 'payment',
            'metadata' => [
                'order_id' => "orderId32",  // 传递订单号
            ],
            'success_url' => DOMAIN_PATH . 'api/success.php',
            'cancel_url' => DOMAIN_PATH . 'api/cancel.html',
        ]);
#    var_dump($checkout_session);
        // 向 Stripe Checkout 页
//    header("HTTP/1.1 303 See Other");
//    header("Location: " . $checkout_session->url);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // 如 Stripe API 错返错误信息
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>


