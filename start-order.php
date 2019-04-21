<?php session_start(); ?>
<html>
<head>
<link rel="shortcut icon" href="/favicon.ico" />
<script src='https://www.google.com/recaptcha/api.js'></script>
  <meta charset="utf-8">
  <meta name="viewport" content="initial-scale=1.0">
  <title>Nerva.Exchange | Start Order</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,400|Open+Sans:400,300|Raleway:400,100,700" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="css/standardize.css">
  <link rel="stylesheet" href="css/start-order-grid.css">
  <link rel="stylesheet" href="css/start-order.css">
</head>
<body class="body page-start-order clearfix">
  <div class="wholebody clearfix">
    <div class="header clearfix">
      <div class="logo">
        <p class="website-name"><strong>Nerva.Exchange</strong></p>
      </div>
      <div class="header-links clearfix">
        <button id="home_button" class="_button _button-1">Home</button>
        <p class="text">|</p>
        <button id="aboutus_button" class="_button _button-2">About Us</button>
        <p class="text">|</p>
        <button id="fees_button" class="_button _button-3">Fees</button>
      </div>
    </div>
    <div class="container container-1 clearfix">
      <div class="container container-2 clearfix">
        <p class="text text-3">Nerva.Exchange</p>
        <p class="text text-4">© 2018 All Rights Reserved</p>
      </div>
      <div class="container container-3 clearfix">
        <button class="_button">About Us</button>
        <p class="text text-5">|</p>
        <button class="_button">Fees</button>
        <p class="text text-6">|</p>
        <button class="_button">Terms &amp; Conditions</button>
        <p class="text text-7">|</p>
        <button class="_button">F.A.Q.</button>
      </div>
    </div>
    <div class="body-container">
      <div class="center-container clearfix">
        <p class="text text-8">Please enter your Nerva wallet for receiving XNV</p>
		<form action="order-status.php" method="post" id="xnv-new-order">
		<input hidden type="text" name="form-type" value="start-order">
        <input class="_input" type="text" name="xnvwallet" required>
        <p class="text text-9">Please enter your Bitcoin wallet for any possible refund</p>
        <input class="_input _input-2" type="text" name="btcrefund" required>
        <div class="re_captcha_container"><center><div class="g-recaptcha" data-sitekey="6LfNi3MUAAAAAB5FnElGDVxn6O8yuKZeFFxOBuVJ"></div></center></div>
		
        <p class="text text-10"><?php if ($_SESSION["captcha"] == "false") { echo "Please verify you are human!"; } 
									else { echo "Please double check the wallet addresses you have entered. Any mistake on the addresses might cause you to lose your funds."; }
									  if ($_SESSION["databasestatus"] == "error") { echo "Database connection failed. Please try again later."; $_SESSION["databasestatus"] = "refreshed"; }
		?></p>
		
        <button class="_button" type="submit" form="xnv-new-order">Start Order</button>
      </div>
    </div>
  </div>
</body>
</html>