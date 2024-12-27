<?php
date_default_timezone_set('Asia/Shanghai');
//ini_set('session.gc_maxlifetime', 3600);
//session_start();
header('Content-Type: application/json');
// 引入自动加载文件和配置文件
require 'vendor/autoload.php';
require 'config.php';
require_once 'session.php';

//// 确保请求是 POST 方法
//if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//    http_response_code(405); // 方法不被允许
//    echo json_encode(['code' => 405, 'status' => 'error', 'message' => 'Only POST requests are allowed']);
//    exit;
//}



$response = [
    'status' => 'error',
    'message' => '无效的操作',
    'data' => null
];
$act = $_REQUEST['act'];
switch($act){
    case "login":
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if ($username === 'admin' && $password === 'As1234') {
            $session = new Session();  // 创建 Session 对象
            $session->login($username);  // 设置登录状态
            $response = [
                'code' => 200,
                'status' => 'success',
                'message' => '登录成功！',
                'data' => [
                    'username' => $username,
                    'redirect_url' => 'order_list.php' // 登录成功后跳转的 URL
                ]
            ];
        } else {
            $response = [
                'code' => 201,
                'status' => 'error',
                'message' => '用户名或密码错误！',
                'data' => null
            ];
        }
        break;
    case "logout":
        require_once 'Session.php';

        $session = new Session();  // 创建 Session 对象
        $session->logout();  // 销毁会话
        header('Location: login.php');  // 重定向到登录页面
        exit;
        break;
    case "create":
        // 确保表单数据完整
        if (empty($_POST['account']) || empty($_POST['amount']) || empty($_POST['currency'])) {
            http_response_code(400); // 请求错误
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }
        $name = htmlspecialchars(trim($_POST['account']));  // 防止XSS攻击
        $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);  // 确保amount为浮动数字
        $currency = strtoupper(trim($_POST['currency'])); // 转为大写

        if ($amount <= 0) {
            http_response_code(400);
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid amount']);
            exit;
        }
        $validCurrencies = unserialize(CURRENCY_SUPPORT);
        if (!in_array($currency, $validCurrencies)) {
            http_response_code(400);
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid currency']);
            exit;
        }

        if(!in_array($currency,$validCurrencies)){
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
            'note' => '',
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
        $response = [
            'code' => 200,
            'status' => 'success',
            'link' => DOMAIN_PATH . 'payment.php?order_id=' . $orderId
        ];
        break;
    case "list":

        $orders = readOrders("orders.json");
        $page = count($orders) / 20;
        $data = [];
        $data['code'] = 0;
        $data['data']['total'] = count($orders);
        $data['data']['pages'] = ceil($page);
        $data['data']['content'] = $orders;
        $response = $data;
//        $orderId = isset($_POST['order_id']) ? $_POST['order_id'] : '';
//        $orderDate = isset($_POST['order_date']) ? $_POST['order_date'] : '';
//        $orderStatus = isset($_POST['order_status']) ? $_POST['order_status'] : '';
        break;
    case "topay":
        $order_id = $_GET['order_id'] ?? null; // 通过 null 合并运算符

// 如果没有递订单 ID，直接回误
        if (!$order_id) {
            echo json_encode(['error' => 'Order ID is required']);
            exit;
        }
        $order = getOrder($order_id);
        if(empty($order)){
            echo json_encode(['error' => 'Invalid Order ID']);
            exit;
        }
        if($order['status'] > 0){
            header("Location: payment.php?order_id=" . $order_id);
            exit;
        }
        $currency = $order['currency'] ?? "USD";
        $limitCurrency = unserialize(CURRENCY_LIMIT);
        if(in_array($currency,$limitCurrency)){
            $amount = $order['amount'];
        }else{
            $amount = $order['amount'] * 100;
        }
        if(empty($currency)) $currency = "USD";

        \Stripe\Stripe::setApiKey(SECRET_KEY_LIVE);
        try {
            // 创建 Stripe Checkout 话
            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $currency,  // 必须：货币类型
                            'product_data' => [
                                'name' => 'Total', //$order_id,  // 必须：商品名称
                            ],
                            'unit_amount' => $amount,  // 必须：商品价格，单位为最小货币单位
                        ],
                        'quantity' => 1,  // 必须：商品数量
                    ],
                ],
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
        break;
    case 'download':
        downloadOrdersAsCSV();
        break;

}
echo json_encode($response);exit;

?>

