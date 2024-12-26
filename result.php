<?php
date_default_timezone_set('Asia/Shanghai');
// 引入自动加载文件和配置文件
require 'vendor/autoload.php';
require 'config.php';

// 确保请求是 POST 方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 方法不被允许
    echo json_encode(['code' => 405, 'status' => 'error', 'message' => 'Only POST requests are allowed']);
    exit;
}

// 确保表单数据完整
if (empty($_POST['account']) || empty($_POST['amount']) || empty($_POST['currency'])) {
    http_response_code(400); // 请求错误
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// 获取并验证表单数据
$name = htmlspecialchars(trim($_POST['account']));  // 防止XSS攻击
$amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);  // 确保amount为浮动数字
$currency = strtoupper(trim($_POST['currency'])); // 转为大写

if ($amount <= 0) {
    http_response_code(400);
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid amount']);
    exit;
}

// 确保货币是有效的，假设我们只支持特定的货币
$validCurrencies = ["CNY","MYR","TWD","HKD","SGD","THB","VND","PHP","MOP","JPY","AUD","NZD","IDR","KRW","NGN","EUR","GBP","CAD","AED","SAR","EGP","USD"];
if (!in_array($currency, $validCurrencies)) {
    http_response_code(400);
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid currency']);
    exit;
}

// 生ID，使用uniqid()生成唯一订单ID
$orderId = uniqid('order_');

// 订单信息
$order = [
    'orderId' => $orderId,
    'name' => $name,
    'amount' => $amount,
    'currency' => $currency,
    'status' => 0,  // 订单状态 0 代表未支付
    'paymentTime' => date("Y-m-d H:i:s"),
];

// 读取当前订单列表
$orders = readOrders();

// 将新订单添加到订单列表
$orders[] = $order;

// 将订单信息写回文件
if (!writeOrders($orders)) {
    http_response_code(500); // 内部服务器错误
    echo json_encode(['code' => 500, 'status' => 'error', 'message' => 'Failed to save order']);
    exit;
}

// 返回 JSON 响应，包含支付链接
$result = [
    'code' => 200,
    'status' => 'success',
    'link' => DOMAIN_PATH . 'payment.php?order_id=' . $orderId
];

echo json_encode($result);
?>

