<?php

class Session {

    // 会话超时的默认时间（单位：秒，30 分钟）
    private $timeout_duration = 1800;

    // 会话类构造函数
    public function __construct($timeout_duration = 1800) {
        // 初始化会话超时设置
        $this->timeout_duration = $timeout_duration;
        // 启动会话
        session_start();

        // 如果是首次访问，会话中没有 last_activity，则设置为当前时间
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
        }
    }

    // 更新最后活动时间
    public function updateLastActivity() {
        $_SESSION['last_activity'] = time();
    }

    // 检查会话是否超时
    public function checkSessionTimeout() {
        // 计算用户最后活动和当前时间的差值
        $inactive_duration = time() - $_SESSION['last_activity'];

        if ($inactive_duration > $this->timeout_duration) {
            // 会话超时，清除会话并跳转到登录页面
            $this->destroySession();
            header("Location: login.php");  // 跳转到登录页面
            exit;
        }
    }

    // 销毁会话
    public function destroySession() {
        session_unset();    // 清除所有会话变量
        session_destroy();  // 销毁会话
    }

    // 检查用户是否已登录
    public function isLoggedIn() {
        return isset($_SESSION['username']);  // 假设登录时设置了用户名
    }

    // 设置登录状态
    public function login($username) {
        $_SESSION['username'] = $username;  // 设置用户名
        $this->updateLastActivity();         // 更新最后活动时间
    }

    // 登出
    public function logout() {
        $this->destroySession();  // 销毁会话
    }

    // 获取当前会话的剩余时间（单位：秒）
    public function getRemainingTime() {
        return $this->timeout_duration - (time() - $_SESSION['last_activity']);
    }
}
?>
