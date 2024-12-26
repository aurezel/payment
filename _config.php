<?php 

define("DOMAIN_PATH",'https://witrugo.com/'); //ser
define("WEBHOOK_KEY","whsec_lYmldmq2GfSjdTL9uCS5perRLqENpijK");

define("PERMARY_KEY_LIVE",'pk_test_51QRqwtP5ytAfp9h7V5hyNaX1rMMukORWer2a6hT8LjhPEScNBqFnlY3O65s7yxfz42aPq82W5m8UeF9ZcFolEyXM00sohfeX4u');
define("SECRET_KEY_LIVE",'sk_test_51QRqwtP5ytAfp9h739lA4zcxZze29FDylruIgfZ2BlDgGOsHUyjlpIzypqnqTklqQ8oIFBNlG4NxOSFuvz5HsK1b00zKgfURNc');


define("SECRET_KEY_TEST",'');
define("PERMARY_KEY_TEST",'');

define("FILENAME_JSON",'orders.json');
define("PRICE_CSV",'product.csv');
define("PRICE_TEST_CSV",'product_test.csv');
define("CURRENCY_LIMIT",serialize(["BIF","CLP","GNF","JPY","KMF","KRW","MGA","PYG","RWF","UGX","VND","VUV","XAF","XOF","DJF","XPF"]));

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
