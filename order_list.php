<?php
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'session.php';
if(!$_SESSION['username']){
    header("Location: login.php");  // 跳转到登录页面
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订单列表</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/layui@2.8.1/dist/css/layui.css">
</head>
<body>
<div class="table-container" style="margin-top:20px;">
    <div class="layui-table-view layui-table-view-3 layui-form layui-border-box" lay-filter="LAY-TABLE-FORM-DF-3" style="" lay-table-id="test">
        <div class="layui-table-tool">
            <div class="layui-table-tool-temp"><div class="layui-btn layui-btn-primary layui-border-green">订单列表</div></div>
            <div class="layui-table-tool-self">
                <div class="layui-inline" id="exportBtn" title="导出" lay-event="LAYTABLE_EXPORT"><a href="<?php echo 'result.php?act=download';?>"><i class="layui-icon layui-icon-export"></i></a></div>
                <div class="layui-inline" title="创建" lay-event="LAYTABLE_PRINT"><a href="merchant.html"><i class="layui-icon layui-icon-add-1"></i></a></div>
            </div>
        </div>
        <div class="layui-table-box">
            <div class="layui-table-init layui-hide">
                <div class="layui-table-loading-icon"><i class="layui-icon layui-icon-loading layui-anim layui-anim-rotate layui-anim-loop"></i></div>
            </div>
            <div class="layui-table-header">  </div>
        </div>
        <div class="layui-table-column layui-table-page layui-hide" style="">
            <div class="layui-inline layui-table-pageview layui-hide-v" id="layui-table-page3"></div>
        </div>
        <style id="LAY-STYLE-DF-table-3">.laytable-cell-3-0-0{width: 80px}.laytable-cell-3-0-1{width: 80px}.laytable-cell-3-0-2{width: 80px}.laytable-cell-3-0-3{width: 80px}</style>
    </div>
<!-- 搜索表单 -->
<div class="layui-form" style="margin: 20px;">
    <div class="layui-form-item">
        <div class="layui-inline">
        <label class="layui-form-label">订单号</label>
        <div class="layui-input-inline">
            <input type="text" id="orderId" class="layui-input" placeholder="请输入订单号">
        </div>
        </div>
        <div class="layui-inline">
        <label class="layui-form-label">用户名</label>
        <div class="layui-input-inline">
            <input type="text" id="userName" class="layui-input" placeholder="请输入用户名">
        </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">订单时间</label>
            <div class="layui-input-inline" style="width: 120px;">
                <div class="layui-input-wrap"><input type="text" id="startTime" class="layui-input" placeholder="请选择时间" style="width:120px;"></div>
            </div>
            <div class="layui-form-mid">-</div>
            <div class="layui-input-inline" style="width: 120px;">
                <div class="layui-input-wrap"><input type="text" id="endTime" class="layui-input" placeholder="请选择时间" style="width:120px;"></div>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">订单状态</label>
            <div class="layui-input-inline">
                <select id="orderStatus">
                    <option value="">请选择状态</option>
                    <option value="0">待支付</option>
                    <option value="1">已支付</option>
                    <option value="2">已取消</option>
                </select>
            </div>
        </div>
        <div class="layui-inline">
        <div class="layui-input-inline">
            <button class="layui-btn" id="searchBtn">搜索</button>
        </div>
        </div>
    </div>
</div>

<!-- 订单列表表格 -->
<table id="orderTable" class="layui-table" lay-filter="orderTable">
    <!-- 操作栏模板 -->
    <script type="text/html" id="actionBar">
        <a class="layui-btn layui-btn-xs" lay-event="view">查看</a>
        <a class="layui-btn layui-btn-xs layui-btn-primary" lay-event="edit">编辑</a>
    </script>
</table>

<!-- 分页区域 -->
<div id="pagination" class="layui-laypage"></div>
</div>
<!-- 引入 Layui JS -->
<script src="https://cdn.jsdelivr.net/npm/layui@2.8.1/dist/layui.js"></script>

<script>
    layui.use(['table', 'laypage', 'jquery', 'form', 'laydate'], function() {
        var table = layui.table;
        var laypage = layui.laypage;
        var laydate = layui.laydate;
        var $ = layui.jquery;
        var form = layui.form;
        var requrl = '<?php echo DOMAIN_PATH;?>' + 'result.php?act=list'
        // 获取外部JSON数据
        $.ajax({
            // url: 'test.json', // 外部JSON文件路径
            url: requrl, // 外部JSON文件路径
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                var allData = response.data; // 获取JSON文件中的数据

                // 渲染表格
                function renderTable(data) {
                    table.render({
                        elem: '#orderTable',
                        data: data.content, // 渲染的订单数据
                        cols: [[
                            {field: 'orderId', title: '订单号'},
                            {field: 'name', title: '用户名'}, // 新增的列
                            {field: 'paymentTime', title: '订单时间'},
                            {field: 'amount', title: '订单金额',
                                templet: function(d) {
                                    return (d.currency=='' || d.currency==undefined) ? d.amount+" "+'USD' : d.amount+" "+d.currency
                                }
                            },
                            {field: 'status', title: '订单状态',
                                templet: function(d) {
                                    return d.status === 0 ? '<div class="layui-btn layui-btn-warm">待支付</div>' :
                                        d.status === 1 ? '<div class="layui-btn layui-btn-normal">已支付</div>' :
                                            d.status === 2 ? '已发货' : '已完成';
                                }
                                },
                            {field: 'note', title: '订单备注'},
                            // {fixed: 'right', title: '操作', toolbar: '#actionBar'}
                        ]],
                        page: true // 开启分页功能
                    });
                }

                // 初始渲染所有数据
                renderTable(allData);
                // 监听搜索按钮点击事件
                $('#searchBtn').on('click', function() {
                    console.log("time:",allData);
                    var originalData = JSON.parse(JSON.stringify(allData));
                    var orderId = $('#orderId').val() ? $('#orderId').val().toLowerCase() : ''; // 获取订单号
                    var username = $('#userName').val() ? $('#userName').val().toLowerCase() : ''; // 获取用户名
                    var orderStatus = $('#orderStatus').val(); // 获取订单状态
                    var startTime = $('#startTime').val(); // 获取订单时间
                    var endTime = $('#endTime').val(); // 获取订单时间

                    var filteredData = originalData['content'].filter(function(item) {
                        // 根据订单号、用户名、订单状态和订单时间进行过滤
                        var isOrderNoMatch = orderId ? item.orderId.toLowerCase().includes(orderId) : true;
                        var isUsernameMatch = username ? item.name.toLowerCase().includes(username) : true;
                        var isOrderStatusMatch = orderStatus !='' ? item.status == orderStatus : true;

                        var startDate = 0, endDate = 0;
                        var isOrderTimeMatch = true; // 初始化匹配状态
                        var orderTime = item.paymentTime;
                        if(orderTime){
                            var orderDate = new Date(orderTime.replace(/-/g, '/')).getTime();
                        }

                        if (startTime) {
                            startDate = new Date(startTime.replace(/-/g, '/')).getTime();
                        }

                        if (endTime) {
                            endDate = new Date(endTime.replace(/-/g, '/')).getTime() + (24 * 60 * 60 * 1000 - 1);
                        }

                        if (startDate > 0 && endDate > 0) {
                            isOrderTimeMatch = orderDate >= startDate && orderDate <= endDate;
                        } else if (startDate > 0) {
                            isOrderTimeMatch = orderDate >= startDate;
                        } else if (endDate > 0) {
                            isOrderTimeMatch = orderDate <= endDate;
                        }
                        // console.log('test',isOrderTimeMatch,orderDate,startDate);
                        // var isOrderTimeMatch = orderTime ?
                        //     layui.util.toDateString(item.orderTime, "yyyy-MM-dd").includes(orderTime) : true;
                        return isOrderNoMatch && isUsernameMatch && isOrderStatusMatch && isOrderTimeMatch;
                        // return isOrderNoMatch;
                    });
                    originalData.content = filteredData

                    // 渲染过滤后的表格
                    renderTable(originalData);

                });

                // 监听操作栏的事件
                table.on('tool(orderTable)', function(obj) {
                    var data = obj.data;
                    var event = obj.event;
                    if (event === 'view') {
                        // 执行查看操作
                        alert('查看订单号：' + data.orderId);
                    } else if (event === 'edit') {
                        // 执行编辑操作
                        alert('编辑订单号：' + data.orderId);
                    }
                });

                // 初始化 Laydate 时间选择器
                laydate.render({
                    elem: '#startTime', // 绑定元素
                    type: 'date' // 日期类型
                });
                laydate.render({
                    elem: '#endTime', // 绑定元素
                    type: 'date' // 日期类型
                });
            },
            error: function() {
                console.log('加载数据失败');
            }
        });


    });
</script>

</body>
</html>
