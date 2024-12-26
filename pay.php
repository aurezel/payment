<?php

// 获取订单 ID
$order_id = $_GET['order_id'] ?? null; // 通过 null 合并运算符
 
// 如果没有递订单 ID，直接回误
if (!$order_id) {
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

// 初化格
$stripe_price = [];

// 取 CSV 文并将格射到数中 50 80.99
$csvFile = 'product.csv';
if (($handle = fopen($csvFile, 'r')) !== false) {
    while (($data = fgetcsv($handle)) !== false) {
        // 将格 ID 价格射
        $stripe_price[trim($data[1])] = trim($data[0]);
    }
    fclose($handle);
} else {
    // 如果件无法打返错误
    echo json_encode(['error' => 'Failed to read CSV file']);
    exit;
}

$ordersFile = 'orders.json';
if (file_exists($ordersFile)) {
    $orders = json_decode(file_get_contents($ordersFile), true); 
   foreach ($orders as $order) {
        $indexedData[$order['orderId']] = $order;
    }
}
// 格 ID 存在射数
if (!isset($indexedData[$order_id])) {
    echo json_encode(['error' => 'Invalid Order ID']);
    exit;
}
// var_dump($indexedData[$order_id]);
// echo $indexedData[$order_id]['amount'];
// 获应价 ID
$price_id = $stripe_price[$indexedData[$order_id]['amount']];

// 加 Stripe SDK
require_once 'vendor/autoload.php';
require_once 'config.php';
// 设置 Stripe API 密钥
\Stripe\Stripe::setApiKey(SECRET_KEY_LIVE);

try {
    // 创建 Stripe Checkout 话
    $checkout_session = \Stripe\Checkout\Session::create([
        'line_items' => [[ 
            'price' => $price_id, // 从 CSV 的动价 ID
            'quantity' => 1, 
            // 'product_data' => [
            //                     'name' => $order_id,
            //                 ]
        ]],
        'mode' => 'payment', 
        'metadata' => [
            'order_id' => $order_id,  // 传递订单号
        ],
        'success_url' => DOMAIN_PATH . 'api/success.php',
        'cancel_url' => DOMAIN_PATH . 'api/cancel.html',
    ]);

    // 向 Stripe Checkout 页
    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);

} catch (\Stripe\Exception\ApiErrorException $e) {
    // 如 Stripe API 错返错误信息
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

?>
