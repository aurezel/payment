
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>
    <link rel="stylesheet" href="assets/layui/css/layui.css" />

</head>
<body><style>
    .demo-login-container{width: 320px; margin: 60px auto 0;}
    .demo-login-other .layui-icon{position: relative; display: inline-block; margin: 0 2px; top: 2px; font-size: 26px;}
</style>
<form class="layui-form">
    <div class="demo-login-container">
        <div class="layui-form-item">
            <div class="layui-input-wrap">
                <div class="layui-input-prefix">
                    <i class="layui-icon layui-icon-username"></i>
                </div>
                <input type="text" name="username" value="" lay-verify="required" placeholder="用户名" lay-reqtext="请填写用户名" autocomplete="off" class="layui-input" lay-affix="clear">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-wrap">
                <div class="layui-input-prefix">
                    <i class="layui-icon layui-icon-password"></i>
                </div>
                <input type="password" name="password" value="" lay-verify="required" placeholder="密   码" lay-reqtext="请填写密码" autocomplete="off" class="layui-input" lay-affix="eye">
            </div>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="demo-login">登录</button>
        </div>

    </div>
</form>
<script src="assets/jquery.js"></script>
<script src="assets/layui/layui.js"></script>
<script>
    layui.use(['form', 'layer'], function() {
        var form = layui.form;
        var layer = layui.layer;
        var $ = layui.$;

        // 提交事件
        form.on('submit(demo-login)', function(data) {
            var field = data.field;  // 获取表单字段值

            // 模拟表单提交后的处理逻辑
            // 你可以在这里进行表单验证或者执行任何其他操作
            var username = field.username;
            var password = field.password;

            $.ajax({
                url: 'result.php?act=login', // 后端验证接口
                type: 'POST',
                data: field, // 提交表单数据
                success: function(response) {
                    if (response.code === 200) {
                        layer.msg('登录成功！', { icon: 1 });
                        // 登录成功后跳转到订单查询页面
                        setTimeout(function(){
                            window.location.href = response.data.redirect_url; // 替换成你的订单查询页面地址
                        }, 1000);
                    } else {
                        layer.msg('登录失败：' + response.message, { icon: 2 });
                    }
                },
                error: function() {
                    layer.msg('请求失败，请稍后再试！', { icon: 2 });
                }
            });
            return false;  // 阻止表单默认提交
        });
    });
</script>
<!--<script>-->
<!--    layui.use(function(){-->
<!--        var form = layui.form;-->
<!--        var layer = layui.layer;-->
<!--        // 提交事件-->
<!--        form.on('submit(demo-login)', function(data){-->
<!--            var field = data.field; // 获取表单字段值-->
<!--            // 显示填写结果，仅作演示用-->
<!--            layer.alert(JSON.stringify(field), {-->
<!--                title: ''-->
<!--            });-->
<!--            // 此处可执行 Ajax 等操作-->
<!--            // …-->
<!--            return false; // 阻止默认 form 跳转-->
<!--        });-->
<!--    });-->
<!--</script>-->


</body>
</html>
