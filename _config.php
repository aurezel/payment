<?php

define("DOMAIN_PATH",'');
define("WEBHOOK_KEY","");

define("PERMARY_KEY_LIVE",'');
define("SECRET_KEY_LIVE",'');


define("SECRET_KEY_TEST",'');
define("PERMARY_KEY_TEST",'');

define("FILENAME_JSON",'orders.json');
define("PRICE_CSV",'product.csv');
define("PRICE_TEST_CSV",'product_test.csv');
define("CURRENCY_LIMIT",serialize(["BIF","CLP","GNF","JPY","KMF","KRW","MGA","PYG","RWF","UGX","VND","VUV","XAF","XOF","DJF","XPF"]));
define("CURRENCY_SUPPORT",serialize(["CNY","MYR","TWD","HKD","SGD","THB","VND","PHP","MOP","JPY","AUD","NZD","IDR","KRW","NGN","EUR","GBP","CAD","AED","SAR","EGP","USD"]));

/**
 * 读取 orders.json 文件并返回订单数据。
 *
 * @param string $filename 文件名
 * @return array 订单数据数组
 */
function readOrders($filename = 'orders.json') {
    if (file_exists($filename)) {
        $fileContent = file_get_contents($filename);
        $orders = json_decode($fileContent, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $orders;
        } else {
            // 如果 JSON 解析失败，返回空数组
            return [];
        }
    }

    return [];  // 文件不存在，返回空数组
}
function getOrder($orderId, $filename = 'orders.json') {
    $orders = readOrders($filename);
    foreach ($orders as $order) {
        if ($order['orderId'] == $orderId) {
            return $order;
        }
    }
    return null; // 未找到订单
}


function createOrder($newOrder, $filename = 'orders.json') {
    $orders = readOrders($filename);

    // 添加唯一订单 ID
    $newOrder['orderId'] = 'order_' . uniqid();
    $orders[] = $newOrder;

    // 写入更新后的订单数据
    return writeOrders($orders, $filename) ? $newOrder : false;
}
/**
 * 修改订数，根据订单 ID 更新状态或其他信息。
 *
 * @param string $orderId 订单 ID
 * @param array $updatedData 更新的订单数据
 * @param string $filename 文件名
 * @return array 返回更新后的订单数据
 */
function updateOrder($orderId, $updatedData, $filename = 'orders.json') {
    // 取现有的订单数据
    $orders = readOrders($filename);

    // 遍历订单数据并更新匹配的订单
    foreach ($orders as $index => $order) {
        if ($order['orderId'] == $orderId) {
            // 更新订单数据
            $orders[$index] = array_merge($order, $updatedData);
        }
    }

    return file_put_contents($filename, json_encode($orders, JSON_PRETTY_PRINT)) !== false;
}
// updateOrder('order_676143b8da447',['status'=>1]);
// updateOrder('order_675fdfdd1cfc9',['status'=>2]);
/**
 * 将订单数据写入到 orders.json 文件。
 *
 * @param array $orders 订单数据
 * @param string $filename 文件名
 * @return bool 返写入成功失败
 */
function writeOrders($orders, $filename = 'orders.json') {
    // 将订单数编码为 JSON 格式
    $jsonData = json_encode($orders, JSON_PRETTY_PRINT);

    // 如 JSON 编码失败，返 false
    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }

    // 将编码后的数据写入文件
    return file_put_contents($filename, $jsonData) !== false;
}

function downloadOrdersAsCSV($filename = 'orders.json') {
    $orders = readOrders($filename);

    if (empty($orders)) {
        die("No orders available to export.");
    }

    // 设置表头
    $headers = [
        "订单编号",
        "用户名",
        "订单状态",
        "订单金额",
        "货币",
        "  创建时间  ",
        "订单备注"
    ];

    // 设置 HTTP 头以触发下载
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="orders.csv"');

    // 打开输出流
    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);

    // 写入订单数据
    foreach ($orders as $order) {
        $status = "";
        if($order['status'] == 1){
            $status = "已支付";
        }elseif($order['status'] == 2){
            $status = "已取水";
        }else{
            $status = "待支付";
        }
        fputcsv($output, [
            $order['orderId'] ?? '',
            $order['name'] ?? '',
            $status,
            $order['amount'] ?? '',
            $order['currency'] ?? '',
            $order['paymentTime'] ?? '',
            $order['note'] ?? ''
        ]);
    }
    fclose($output);
    exit;
}