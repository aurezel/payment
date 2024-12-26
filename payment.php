<?php 
require 'vendor/autoload.php';
require 'config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/layui-src/dist/css/layui.css" />
  <link rel="stylesheet" href="./assets/css/checkout.css" />
  
</head>
<body>
<?php 
    $indexedData = [];
    
    
    $orders = readOrders();
    foreach ($orders as $order) {
        $indexedData[$order['orderId']] = $order;
    }
    $order_id = $_GET['order_id'];
    if(empty($order_id) || !isset($indexedData[$order_id])){
        header("Location: index.html");
    }
   
   $limitCurrency = unserialize(CURRENCY_LIMIT);
$amountText = '';
if(in_array($indexedData[$order_id]['currency'],$limitCurrency)){
	$amountText = $indexedData[$order_id]['amount'];
}else{
	$amountText = number_format($indexedData[$order_id]['amount'],2);
}
$amountText .= " " . $indexedData[$order_id]['currency'];	    
?>
 
 
 
 <style>
    
    
    @font-face {  font-family: 'Jost';  font-style: normal;  font-weight: 400;  font-display: swap;  src: url(https://fonts.gstatic.com/s/jost/v6/92zPtBhPNqw79Ij1E865zBUv7myjJTVBNIg.woff2) format('woff2');}
    

    
    @font-face {  font-family: 'Jost';  font-style: normal;  font-weight: 800;  font-display: swap;  src: url(https://fonts.gstatic.com/s/jost/v6/92zPtBhPNqw79Ij1E865zBUv7mwjIjVBNIg.woff2) format('woff2');}
    


    :root {
        
        --popup_border_radius: 0px;
        --general_layout_width: 1200px;
        --general_layout_spacing: 80px;
        --button_border_radius: 0px;
        --full_container_padding: 50px;
        --page_background_color: #fff;
        --title_color: #232323;
        --main_color: #232323;
        --detail_color: #666666;
        --buying_parice_color: #FF0000;
        --original_price_color: #999;
        --discount_tag_bg: #BC0404;
        --discount_tag_color: #fff;
        --main_button_bg: #232323;
        --main_button_color: #fff;
        --secondary_button_bg: #ffffff;
        --secondary_button_color: #232323;
        --title_font_family: Jost;
        --general_font_family: Jost;
        --title_font_size: 20px;
        --general_font_size: 14px;
        --product_font_size: 14px;
        --big_product_font_size: 28px;
        --title_letter_spacing: 1pxpx;
        --nav_letter_spacing: 1pxpx;
        --product_img_cut: cover;

        --title_font_style: normal;
        --title_font_weigth: 800;


        --general_font_style: normal;
        --general_font_weigth: 400;

        --general_line_height: 1.5;
        --wap_title_scale: 0.75;
        --title_margin_bottom_scale: 0.75;
    }
</style>



<style>
  .coupons-record-box {
    position: relative;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #D9D9D9;
  }
  .coupons-record-title{
    font-weight: bold;
    color: #000018;
  }
  .coupons-record-more {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 76px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0.95));
    border-radius: 0px 0px 0px 0px;
    opacity: 1;
  }
  .coupons-record-more-btn {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    text-align: center;
    text-decoration: underline;
    cursor: pointer;
  }
  .coupons-record-wrap {
    margin-top: 20px;
    overflow: hidden;
    display: grid;
    grid-row-gap: 20px;
    grid-column-gap: 20px;
    grid-template-columns: repeat(2, minmax(50px, 1fr));
  }
  .coupons-record-item {
    display: flex;
    cursor: pointer;
  }
  .coupons-record-item-riado {
    width: 18px;
    height: 18px;
    background: #ffffff;
    border: 1px solid #cccccc;
    border-radius: 50%;
    box-sizing: border-box;
  }
  .coupons-record-item-body {
    flex: 1;
  }
  .coupons-record-item-header{
    display: flex;
    align-items: center;
  }
  .coupons-record-item-code {
    display: inline-block;
    font-weight: 400;
    color: #1e1d29;
    border-radius: 4px 4px 4px 4px;
    opacity: 1;
    font-size: 14px;
    line-height: 18px;
    padding: 5px 10px;
    margin-left:10px;    
    border: 1px dashed #d9d9d9;
    text-align: center;
  }
  .coupons-record-item-label {
    font-weight: 400;
    color: #999999;
    font-size: 12px;
    margin-top: 10px;
    margin-left: 26px;
  }
  .coupons-record-item-active .coupons-record-item-riado {
    border: 6px solid var(--coupons-color);
  }
  .coupons-record-item-active .coupons-record-item-code {
    border: 1px dashed var(--coupons-color);
    color: var(--coupons-color);
  }
</style>
<style>.coupons-record-more {
    background: linear-gradient(180deg, rgba(250, 250, 250 ,0.6) 0%, #fafafa);
  }</style>
  
 
 <body class="--body-style checkout-layout-card">
    <main class="main-content checkout-main" >
    <style>
  .custom_second_party_card {
    padding: 20px 16px !important;
  }
  .custom_second_party_card .accept_box {
    border: none !important;
    padding: 0 !important;
    margin-bottom: 0 !important;
  }
  .custom_second_party_card .accept_top {
    display: none !important;
  }.custom_second_party_card .accept_box {
  height: auto !important;
}
.custom_second_party_card .accept_con .cardno_date {
  text-align: left !important;
  display: flex;
  justify-content: space-between;
}
.custom_second_party_card .cardno_con {
  margin-bottom: 16px !important;
}
input::-webkit-input-placeholder {
  color: #737373;
  font-size: 13px;
}
.credit_card_2017 .accept_con .cardno_con img {
  top: 50% !important;
  margin-top: -15px;
}
.custom_second_party_card .accept_con .accept_txt {
  margin-bottom: 0 !important;
  float: initial !important;
  height: 48px !important;
  line-height: 48px !important;
  border: 1px solid #ddd !important;
}
.custom_second_party_card .credit_card_2017 .accept_con #cardno_cover {
  height: 48px !important;
  line-height: 48px !important;
  border: 1px solid #ddd;
}
.custom_second_party_card .valid-err .err {
  position: static !important;
}
.second_party_card_load {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.second_party_card_load .cell-item {
  height: 48px;
  background: #ddd;
}

.valid-err .accept_txt {
  border: 1px solid red !important
}
.valid-err .err {
  color: red;
  position: absolute;
  left: 0;
  bottom: -5px;
  text-align: left;
}

#three_nested_paypal_warp,
#three_nested_warp,
#paypal-checkout-button-custom {
  min-width: 200px;
  width: 100%;
  display: none;
}
.paypal-checkout-button-custom {
  display: none;
}
.order_payment_method .order_contact_information_btn .return_cart {
  flex-shrink: 0;
}



.custom_second_party_card .err {
  margin-top: 4px;
  color: #eb1c26 !important;
}
.custom_second_party_card .valid-err .accept_txt {
  border: 2px solid #eb1c26 !important;
  color: #eb1c26 !important;
}
.custom_second_party_card .accept_con .cardno_con img {
  top: 10px !important;
  margin-top: 0 !important;
}
.custom_second_party_card .cardno_date .valid-date {
  width: calc(50% - 10px) !important;
}
.custom_second_party_card .cardno_date .valid-date {
  width: calc(50% - 10px) !important;
}
.custom_second_party_card .cardno_con {
  margin-bottom: 20px !important;
}
.custom_second_party_card .accept_box svg {
  position: absolute;
  top: 8px;
  right: 12px;
}
.custom_second_party_card .valid-date {
  position: relative;
}

.custom_second_party_card .cardno_con .default-svg {}
.custom_second_party_card .cardno_con .err-svg {
  display: none
}


@media screen and (max-width: 896px) {
  .custom_second_party_card .cardno_con {
    margin-bottom: 10px !important;
  }
  .custom_second_party_card .accept_con .cardno_date {
    flex-wrap: wrap;
    gap: 10px;
  }
  .custom_second_party_card .cardno_date .valid-date {
    width: 100% !important;
  }
}
</style>
<style>
.accept_btn{
    display: none !important;
}
.three_nested_paypal_warp #paypal-button-container{
     padding:  0px !important;
    width: 100% !important;
}

</style>
  
<div class="plugin-container-checkout-header"></div>
 
<div id="three_party_form_box"></div>
<div class="order_payment_method" id="order_payment_method">
	<form class="cart_form" id="order_payment_method_form" action="#" method="post" >
    <input type="hidden" name="previous_step" id="previous_step" value="payment_method" autocomplete="off">
    <input type="hidden" name="step" value="payment_gateway" autocomplete="off">
    
	 <div class="order_contact_information_wrapper">
	 	<div class="order_contact_information_left">
	  
            <div class="plugin-container-header"></div>
	 		<div class="order_payment_method_form">
               	<div class="order_payment_method_wrapper">
               		<div class="change_address">
                        <ul>
                            
                            <li>
                                <div class="name">Name:</div>
                                <div class="value" style="font-weight:bold"><?php echo $indexedData[$order_id]['name'];?></div>
                            </li>
                            <li>
                                <div class="name">Total:</div>
				<div class="value" style="font-weight:bold"> <?php echo $amountText;?> </div>
                            </li>
                            
                             
                        </ul>
                    </div>
                     
               		<div class="payment_method_box checkout-card" id="payment_method_box">
                        <div class="payment_method_title card-title" >Pay With Debt/Credit Card</div>
                        
                        <div class="payment_method_content">
                            <ul>
                                

                                <li>
                                    <div class="order_radio_box">
                                         
                                        
                                        <div class="accepted_payment_list">
                                            
                                            <span class="accepted_payment_image"><img src="https://cdn.prshopimg.com/statics/cart/accepted_payment_brand/stripe.svg"></span>
                                            
                                            <span class="accepted_payment_image"><img src="https://cdn.prshopimg.com/statics/cart/accepted_payment_brand/mastercard.svg"></span>
                                            
                                            <span class="accepted_payment_image"><img src="https://cdn.prshopimg.com/statics/cart/accepted_payment_brand/visa.svg"></span>
                                            
                                            <span class="accepted_payment_image"><img src="https://cdn.prshopimg.com/statics/cart/accepted_payment_brand/klarna.svg"></span>
                                            
                                            <span class="accepted_payment_image"><img src="https://cdn.prshopimg.com/statics/cart/accepted_payment_brand/googlepay.svg"></span>
                                            
                                            <span class="accepted_payment_image"><img src="https://cdn.prshopimg.com/statics/cart/accepted_payment_brand/apple_pay.svg"></span>
                                            
                                            <span class="accepted_payment_image"><img src="https://cdn.prshopimg.com/statics/cart/accepted_payment_brand/american_express.svg"></span>
                                            
                                            <span class="accepted_payment_image"><img src="https://cdn.prshopimg.com/statics/cart/accepted_payment_brand/discover.svg"></span>
                                             
                                        </div>
                                        
                                    </label>
                                    </div>
                                    <div class="payment_methods_down custom_second_party_card">
                                        
                                            
                                            <p>After clicking “Continue to Payment”, you will be redirected to next step to complete your purchase securely.</p>
                                            
                                            
                                        

                                    </div>
                                </li>

                                

                                

                                

                            </ul>
                        </div>
                    </div>


                      

                    <div id='clause-box'></div>

               	</div>
                <div class="order_contact_information_btn"> 
								<a class="return_cart"></a>

                    <div class="">
                        <div id="wap_three_nested_paypal_warp" class="three_nested_paypal_warp"></div>
                        
                        <a class="order_btn save_address_btn control-checkout-pay_btn" style="background: #F13A3A;display: block;" id="save_payment_btn" href="<?php echo DOMAIN_PATH.'paying.php?order_id='. $_GET['order_id'];?>">
                            <div class="save_address_btn_text">
                                <span>Continue to Payment </span>
                            </div>
                            <div class="save_address_btn_img" style="display:none">
                            </div>
                            <svg t="1592222422836" class="order_btn_icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="6126" width="20" height="20"><path d="M512.606821 958.898283c-246.319012 0-446.698738-200.379727-446.698738-446.698738S266.287809 65.479317 512.606821 65.479317c17.73185 0 32.117488 14.385639 32.117488 32.117488s-14.385639 32.117488-32.117488 32.117488c-210.897268 0-382.463762 171.58696-382.463762 382.484228s171.566494 382.463762 382.463762 382.463762 382.484228-171.566494 382.484228-382.463762c0-106.013499-42.384319-204.603935-119.332852-277.558503-12.859889-12.211113-13.403265-32.536021-1.212618-45.416376 12.190647-12.901845 32.536021-13.403265 45.416376-1.212618 89.870844 85.229127 139.365094 200.35926 139.365094 324.187497C959.327048 758.518556 758.925832 958.898283 512.606821 958.898283z" p-id="6127"></path></svg>
                        </a>
                    </div>
                </div>
	 		</div>
            <div class="plugin-container-footer"></div>
	 		<footer class="order_footer">
    
     

</footer>
 
	 	</div>
	 	<div class="order_contact_information_right">
	 		<div class="order_product_info">
	 			<div class="product_info_list">
                  

                    <div class="order_product_list_tip">Scroll for more items <svg t="1591928747155" class="icon" viewBox="0 0 1025 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3887" width="14" height="14"><path d="M467.985218 937.525717c0.122866 0.124914 0.24778 0.248804 0.372694 0.370646 11.174684 10.873662 25.852081 16.831651 41.412066 16.831651 0.277473 0 0.554946-0.001024 0.832419-0.005119 15.837458-0.217064 30.643864-6.574368 41.702849-17.905707l334.741901-339.769178c9.525205-9.667525 9.408482-25.226487-0.259043-34.750668-9.666501-9.524181-25.227511-9.408482-34.750668 0.259043l-315.80719 320.551874L536.230246 88.475979c0-13.5716-11.001648-24.573248-24.573248-24.573248-13.5716 0-24.573248 11.001648-24.573248 24.573248l0 798.397108L166.360888 561.132161c-9.52111-9.670597-25.080071-9.791415-34.750668-0.269282-9.670597 9.52111-9.791415 25.080071-0.270306 34.750668L467.985218 937.525717z" p-id="3888"></path></svg></div>
	 			</div>






  
    
  




 
                
                <div id="pc_three_nested_paypal_warp" class="three_nested_paypal_warp"></div>
	 		</div>
             <div class="plugin-checkout-right_bottom"></div>

	 	</div>

	 </div>
    </form>


</div> 
    </main>
     
</html>
