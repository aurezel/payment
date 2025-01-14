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
<style>.content-container{width:660px;margin:20px auto 0;}</style>
<div class="content-container">
<div class="layui-form" style="margin: 20px;">
    <div class="layui-form-item">
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
        var requrl = '<?php echo DOMAIN_PATH;?>' + 'result.php?act=stats'
        // 获取外部JSON数据
        $.ajax({
            // url: 'test.json', // 外部JSON文件路径
            url: requrl, // 外部JSON文件路径
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                var allData = response.data; // 获取JSON文件中的数据
	//	allData = Object.values(allData);
                // 渲染表格
                function renderTable(data) {
                    table.render({
                        elem: '#orderTable',
                        data: data.content, // 渲染的订单数据
                        cols: [[
                            {field: 'createTime', title: '日期',align: 'center', width: 150},
                            {field: 'status1Count', title: '已支付订单数',align:'center'},
                            {field: 'status1Amount', title: '已支付订单金额',align:'center',
                                templet: function(d) {
				return (parseFloat(d.status1Amount) || 0).toFixed(2) + " " + d.currency;
				}
                            },
                        ]],
                        page: true, // 开启分页功能
			done: function(res, curr, count) {
                            // 计算汇总数据
                            var totalPrice = 0;
                            var totalQuantity = 0;

                            res.data.forEach(function(item) {
                                totalPrice += item.status1Amount;
                                totalQuantity += item.status1Count;
                            });

                            // 在表格的最后添加一行汇总数据
                            var footer = `<tr><td colspan="2">汇总</td><td><strong>${totalPrice.toFixed(2)} USD</strong></td></tr>`;
                             
                            // 将汇总行插入到表格的末尾
//                            var tableBody = $('#orderTable').next('.layui-table-view').find('.layui-table-body table');
  //                          tableBody.append(footerHtml);
                        }
                    });
                }

                // 初始渲染所有数据
                renderTable(allData);
                // 监听搜索按钮点击事件
                $('#searchBtn').on('click', function() {
                    var originalData = JSON.parse(JSON.stringify(allData));
                    var endTime = $('#endTime').val(); // 获取订单时间
		    var startTime = $('#startTime').val();
                    var filteredData = originalData['content'].filter(function(item) {

                        var startDate = 0, endDate = 0;
                        var isOrderTimeMatch = true; // 初始化匹配状态
                        var orderTime = item.createTime;
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
				console.log('start--end date');
                        } else if (startDate > 0) {
				isOrderTimeMatch = orderDate >= startDate;
				console.log('startDate');
                        } else if (endDate > 0) {
				isOrderTimeMatch = orderDate <= endDate;
				console.log('endDate');
                        }
                        return isOrderTimeMatch;

                    });
                    originalData.content = filteredData

                    // 渲染过滤后的表格
                    renderTable(originalData);

                });

                // 监听操作栏的事件
                // table.on('tool(orderTable)', function(obj) {
                //     var data = obj.data;
                //     var event = obj.event;
                //     if (event === 'view') {
                //         // 执行查看操作
                //         alert('查看订单号：' + data.orderId);
                //     } else if (event === 'edit') {
                //         // 执行编辑操作
                //         alert('编辑订单号：' + data.orderId);
                //     }
                // });

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

