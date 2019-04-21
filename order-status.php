<?php session_start(); 

$post_data = http_build_query(
    array(
        'secret' => "RECAPTCHA SECRET",
        'response' => $_POST['g-recaptcha-response'],
        'remoteip' => $_SERVER['REMOTE_ADDR']
    )
);
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $post_data
    )
);
$context  = stream_context_create($opts);
$response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
$result = json_decode($response);

if (!$result->success) {
	$_SESSION["captcha"] = "false";
	
	if ($_POST["form-type"] == "check-order") { header( "Location: index.php" ); } 
	elseif ($_POST["form-type"] == "start-order") { header( "Location: start-order.php" ); }
	else { header ( "Location: index.php" ); }
}

else { $_SESSION["captcha"] = "valid"; }



include('order/hiddensrc_exchange/btcrpc.php');

$servername = "localhost";
$username = "USERNAME";
$password = "PASSWORD";
$db_name = "DB NAME";


$conn = new mysqli($servername, $username, $password, $db_name);

if ($conn->connect_error) {
$_SESSION["databasestatus"] = "error";
header( "Location: start-order.php" );
} 

$bitcoin = new BitCoin('BTC RPC USERNAME','PASSWORD');

if ($_POST["form-type"] == "check-order") { 
$orderid = $_POST["order_id_check"];
$sql = "SELECT * FROM exchange WHERE order_id = \"". $orderid . "\"";
$results = mysqli_query($conn,$sql);
if (mysqli_num_rows($results) == 0) {
$order_status = 3; 
$_SESSION["error_status"] = "no_order_with_given_id";
header ( "Location: index.php" );
}
else { $order_status = 2; 
$inforow = mysqli_fetch_array($results);
$btcwallet = $inforow[3];
$btcrefund = $inforow[4];
$nervadeposit = $inforow[5];
$btc_status = $inforow[6];
$nerva_status = $inforow[7];
$nerva_sent_amount = $inforow[8];
$btc_deposit_amount = $inforow[9];
$nervatxhash = $inforow[10];}

 }
else if ($_SESSION["captcha"] == "valid" ) {
$orderid = substr(md5(microtime()),rand(0,26),12); 
$btcrefund = $_POST["btcrefund"];
$nervadeposit = $_POST["xnvwallet"];
$btcwallet = $bitcoin->getaccountaddress($orderid);
$sql = "INSERT INTO exchange (order_id, btc_deposit, btc_refund, nerva_deposit, btc_deposit_status, nerva_sent_status, nerva_sent_amount, btc_deposit_amount, nerva_tx_hash)
VALUES ('$orderid','$btcwallet','$btcrefund','$nervadeposit','-1','0','0','0','0')";
$btc_status = -1;
if ($conn->query($sql) === TRUE) {
    $order_status = 1;
} else {
    $order_status = 0;
	$error_status = "order_creation_failed_database_error";
}}





$qrurl = "https://api.qrserver.com/v1/create-qr-code/?data=" . $btcwallet . "&size=150x150";
?>
<html>
<head>
<link rel="shortcut icon" href="/favicon.ico" />
  <meta charset="utf-8">
  <meta name="viewport" content="initial-scale=1.0">
  <title>Nerva.Exchange | Order Status</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300|Montserrat:400,400|Raleway:400,100,700" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="css/standardize.css">
  <link rel="stylesheet" href="css/order-status-grid.css">
  <link rel="stylesheet" href="css/order-status.css">
</head>
<body class="body page-order-status clearfix">
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
    <div class="body-container clearfix">
      <div class="container container-1 clearfix">
        <div class="container container-2 clearfix">
          <p class="text text-3">Order ID : <?php echo $orderid; ?></p>
          <p class="text text-4">Order ID is important for us to track your order in case of any problem. If you lose your Order ID and have a problem, you might lose your funds.</p>
          <p class="text text-5">Nerva (XNV) Deposit Address</p>
          <p class="text text-6"><?php echo $nervadeposit; ?></p>
          <p class="text text-7">Bitcoin (BTC) Refund Address</p>
          <p class="text text-8"><?php echo $btcrefund;;?></p>
        </div>
        <div class="container container-3 clearfix">
          <p class="text text-9">You have a problem with the exchange?</p>
          <button class="_button _button-4">REACH US</button>
        </div>
      </div>
      <div class="container container-4 clearfix">
        <p class="text text-10">Order Status</p>
		<?php if ($btc_status == -1) { 
        echo "<p class=\"text text-11\">Waiting for Bitcoin Deposit</p>";
        echo "<div class=\"new-order clearfix\">";
          echo "<div class=\"qr-code\"><img style=\"-webkit-user-select: none;\" src=\"" . $qrurl . "\"></div>";
          echo "<p class=\"text text-12\">" . $btcwallet . "</p>";
          echo "<p class=\"text text-13\">Please deposit Bitcoin to the address above</p>";
          echo "<p class=\"text text-14\">Minimum Deposit Amount is : 0.00015</p>";
          echo "<p class=\"text text-15\">Maximum Deposit Amount is : 0.00200</p>";
        echo "</div>"; }
		
			else if ($btc_status == 0 || $btc_status == 1 || $btc_status == 2 ) { 
			echo "<p class=\"text text-11\">Waiting for confirmations from network</p>"; }
			
			else if ($btc_status == 3) {
			echo "<p class=\"text text-11\">Your exchange has been completed!</p>";
			echo "<div class=\"new-order clearfix\">";
				echo "<p class=\"text text-12\">" . "Your final exchange rate was : " . number_format($btc_deposit_amount / $nerva_sent_amount, 10) . "</p>";
				echo "<p class=\"text text-13\">Total ". $nerva_sent_amount . " XNV sent</p>";
				echo "<p class=\"text text-14\">The exchange rate includes all conversion and transfer fees.</p>";
				echo "<p class=\"text text-15\">TX Hash : XXXXXXX</p>";
			echo "</div>"; }
	  ?>
	  </div>
    </div>
	<div class="container container-5 clearfix">
        <div class="container container-6 clearfix">
          <p class="text text-16">Nerva.Exchange</p>
          <p class="text text-17">© 2018 All Rights Reserved</p>
        </div>
        <div class="container container-7 clearfix">
          <button class="_button _button-5">About Us</button>
          <p class="text text-18">|</p>
          <button class="_button _button-6">Fees</button>
          <p class="text text-19">|</p>
          <button class="_button _button-7">Terms &amp; Conditions</button>
          <p class="text text-20">|</p>
          <button class="_button _button-8">F.A.Q.</button>
        </div>
      </div>
  </div>
</body>
</html>