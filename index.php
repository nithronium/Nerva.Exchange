<?php session_start(); ?>
<html>
<head>
<link rel="shortcut icon" href="/favicon.ico" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
window.onload = setInterval(function() {
  $.get('https://tradeogre.com/api/v1/orders/BTC-XNV', function(data) {
  obj = JSON.parse(data);
	var nervaprice = Object.keys(obj.sell);
	var nervaprice = nervaprice[0]*1.05;
	var nervaprice = nervaprice.toFixed(8);
	  
	document.getElementById("nerva_price").innerHTML = "1 Nerva = " + Math.round(nervaprice*100000000) + " Satoshi*";
	
	});
},3000);



</script>
  <meta charset="utf-8">
  <meta name="viewport" content="initial-scale=1.0">
  <title>Nerva.Exchange</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,400|Raleway:100,400,700|Open+Sans:400,300" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="css/standardize.css">
  <link rel="stylesheet" href="css/index-grid.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body class="body page-index clearfix">
  <div class="wholebody clearfix">
    <div class="header clearfix">
      
      <div class="logo">
        <a href="/"><p class="website-name"><strong>Nerva.Exchange</strong></p></a>
      </div>
      <div class="header-links clearfix">
        <a href="/"><button id="home_button" class="_button _button-1">Home</button></a>
        <p class="text">|</p>
        <a href="about-us.html"><button id="aboutus_button" class="_button _button-2">About Us</button></a>
        <p class="text">|</p>
        <a href="fees.html"><button id="fees_button" class="_button _button-3">Fees</button></a>
      </div>
    </div>
    <div class="container container-1 clearfix">
      <div class="container container-2 clearfix">
        <p class="text text-4">Nerva.Exchange</p>
        <p class="text text-5">© 2018 All Rights Reserved</p>
      </div>
      <div class="container container-3 clearfix">
        <a href="about-us.html"><button class="_button">About Us</button></a>
        <p class="text text-6">|</p>
        <a href="fees.html"><button class="_button">Fees</button></a>
        <p class="text text-7">|</p>
        <a href="terms-conditions.html"><button class="_button">Terms &amp; Conditions</button></a>
        <p class="text text-8">|</p>
        <a href="faq.html"><button class="_button">F.A.Q.</button></a>
      </div>
    </div>
    <div class="body-container">
      <div class="left-container clearfix">
        <p class="text text-9">Exchanging Nerva Made Easy!</p>
        <p class="text text-10"><span>Completely anonym, no registrations, simple exchange</span></p>
        <p class="text text-11"><span>Automatic backend, receive your Nerva in seconds</span></p>
        <p class="text text-12"><font color="#ffffff">No IP restrictions, no KYC requirement</font></p>
		<a href ="changelog.txt"><p class="text text-12"><font color="#ffffff">***Click here to see changelog (Updated with every change)</font></p></a>
		<a href ="v.0.0.1.txt"><p class="text text-12"><font color="#ffffff">Beta Version 0.0.1 - Click to read release notes and roadmap.</font></p></a>
        <p class="text text-13"><font color="#ffffff"><span>* Price is estimated and for information purposes only. The final exchange rate will be determined at the moment the exchange occurs. The final rate could be higher or lower than what it shows on main page.</span></font></p>
        <p class="text text-14"><font color="#ffffff"><span>Currently for Bitcoin transactions, 2 confirmations from the network is required. Exchange will occur after the second confirmation.&nbsp;</span></font></p>
        <p class="text text-15"><font color="#ffffff">Current exchange and transaction fees are; 0.00003 BTC fixed and 4% conversion and Nerva network fee. The fee will be lowered after a week of testing.</font></p>
      </div>
      <div class="right-container clearfix">
        <p id="nerva_price" class="nerva-price">Hold on, I'm getting the price!</p>
		
        <button onclick="location.href='start-order.php'" class="_button _button-8" type="button">Start an Order</button>
        <p class="text text-16"><b>By starting an order, you agree with our terms and conditions of operation.</b></p>
		
		<form action="order-status.php" method="POST" id="check_order_status">
        <p class="text text-17"><?php if ($_SESSION["captcha"] == "false") { echo "Please verify you are human."; $_SESSION["captcha"] = "valid"; }
									elseif($_SESSION["error_status"] == "no_order_with_given_id") { echo "There is no order with given ID."; $_SESSION["error_status"] = "no_error";}
									else { echo "Already have an order?"; } ?>
									
									
									
									
									</p>
        <input class="_input" placeholder="Enter your Order ID here" type="text" name="order_id_check">
        <div class="re_captcha_box"><center><div class="g-recaptcha" data-sitekey="6LfNi3MUAAAAAB5FnElGDVxn6O8yuKZeFFxOBuVJ"></div></center></div>
		<input hidden type="text" name="form-type" value="check-order">
        <button class="_button _button-9" type="submit" form="check_order_status" value="Track Order Status">Track Order Status</button>
		</form>
      </div>
    </div>
  </div>
</body>
</html>