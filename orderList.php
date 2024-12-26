<?php
// 设定一个硬编码码（你可以根据需要从数据库或其他来源获）
$correctPassword = '123456';

// 获取请求参数
$password = isset($_GET['password']) ? $_GET['password'] : '';

// 验密码
if ($password == $correctPassword) {
    // 订单数（示例数据，可从数据库获取）
    $ordersFile = 'orders.json';
    $ordersData = file_get_contents($ordersFile);

    // 将 JSON 数据解析为 PHP 数组
    $ordersArray = json_decode($ordersData, true);

    // 检查解析是否成功
    if ($ordersArray === null) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid JSON data in orders file.'
        ]);
        exit;
    }

    // 将订单数组倒序
    $reversedOrders = array_reverse($ordersArray);

    // 将倒序后的数组转换为 JSON 格式并返回
    // echo json_encode($reversedOrders);

    // 返回成功 JSON 数
    echo json_encode([
        'status' => 'success',
        'orders' => $reversedOrders
    ]);
} else {
    // 码错误，回错误信息
    echo json_encode([
        'status' => 'error',
        'message' => 'Incorrect password.'
    ]);
}
?>