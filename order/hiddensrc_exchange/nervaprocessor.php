<?php 

include('btcrpc.php');
include('walletRPC.php');

$servername = "localhost";
$username = "DB USERNAME";
$password = "DB PASSWORD";
$db_name = "DB NAME";

$conn = new mysqli($servername, $username, $password, $db_name);

$bitcoin = new BitCoin('BTC RPC Username','BTC RPC Password');

$sql = "SELECT * FROM exchange";
$results = mysqli_query($conn,$sql);


while($row = mysqli_fetch_array($results)) {
	
	//check for each order
$order_id = $row["order_id"];
$btcwallet = $row["btc_deposit"];
$btc_deposit_status = $row["btc_deposit_status"];
$nerva_deposit_addr = $row["nerva_deposit"];

	//Confirmation Counter
$confcounterallowed = 1;
if ($confcounterallowed == 1) {
// Get confirmation counter
$confirmation_counter = 0;
$btc_confirmations = -1;
	while ($confirmation_counter < 4) {
$btcbalance_unconfirmed = $bitcoin->getreceivedbyaddress($btcwallet,$confirmation_counter);
if ($btcbalance_unconfirmed != 0) { $btc_confirmations++; }
$confirmation_counter++;	
	}

	if ($btc_confirmations == -1) {
	// Waiting for transaction 
	}
	else if ($btc_confirmations == 0) {
	$sql_btc_confirmation = "UPDATE exchange SET btc_deposit_status='0' WHERE btc_deposit='" . $btcwallet . "'"; 
	if (mysqli_query($conn,$sql_btc_confirmation)) { };
	}
	else if ($btc_confirmations == 1) {
	$sql_btc_confirmation = "UPDATE exchange SET btc_deposit_status='3' WHERE btc_deposit='" . $btcwallet . "'"; 
	if (mysqli_query($conn,$sql_btc_confirmation)) { };
	}
	else if ($btc_confirmations == 2) {
	$sql_btc_confirmation = "UPDATE exchange SET btc_deposit_status='3' WHERE btc_deposit='" . $btcwallet . "'"; 
	if (mysqli_query($conn,$sql_btc_confirmation)) { };
	}
	else if ($btc_confirmations == 3) {
	// Transaction got 3 confirmations, processing your order 
	}
}




$btcbalance = $bitcoin->getreceivedbyaddress($btcwallet,1);


	//if transaction got confirmed
		if ($btcbalance != 0 && $btc_deposit_status != 3) {
			
			//update btc deposit status
			$sql_btc_deposit_status = "UPDATE exchange SET btc_deposit_status='3', btc_deposit_amount='" . $btcbalance . "' WHERE btc_deposit='" . $btcwallet . "'";
			if (mysqli_query($conn,$sql_btc_deposit_status)) { echo "database success"; }
			else { echo "database problem" . mysqli_error($conn);}
			
			
			$balance_limit = 0.005;
			if ($btcbalance >= $balance_limit) {
			echo "exceeding the limit"; 
			}
			
			else {
			// get nerva price from exchange
				
				$curlgetxnvprice = curl_init();
				curl_setopt_array($curlgetxnvprice, array(
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_URL => 'https://tradeogre.com/api/v1/orders/BTC-XNV',
					CURLOPT_USERAGENT => 'Codular Sample cURL Request'
											));
				
				$res=json_decode(curl_exec($curlgetxnvprice),true);
				$satoshiprices = array_keys($res["sell"]);
				$btcdeposit = ($btcbalance - 0.00003) * 0.96;
				$ordertotal = 0;
				$ordercounter = 0;
				$totalxnv = 0;
				while ( $ordertotal < $btcdeposit ) {
					$orderprice = floatval($satoshiprices[$ordercounter]) * floatval($res["sell"][$satoshiprices[$ordercounter]]);
					$totalxnv = $totalxnv + floatval($res["sell"][$satoshiprices[$ordercounter]]);
					$ordercounter++;
					$ordertotal = $ordertotal + $orderprice;	
				}
				$price = floatval($satoshiprices[$ordercounter]);
				$averageprice = $ordertotal / $totalxnv;
				$request_amount = $btcdeposit / $averageprice;
				$request_amount = round($request_amount , 4);
				$order_price = $price * 1.1;	
				curl_close($curlgetxnvprice);

			
			//Check BTC Balance on exchange
					$curlgetbalance = curl_init();
					curl_setopt_array($curlgetbalance, array(
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_URL => 'https://tradeogre.com/api/v1/account/balance',
							CURLOPT_USERPWD => 'TRADEOGRE API USERNAME'.':'.'TRADEOGRE API PASS',
							CURLOPT_USERAGENT => 'Codular Sample cURL Request',
							CURLOPT_POST => 1,
							CURLOPT_POSTFIELDS => array(
														'currency' => 'BTC'
														)
));
					$result_btc_balance = json_decode(curl_exec($curlgetbalance),true);
					$result_btc_balance = $result_btc_balance["available"];
					$result_btc_balance = floatval($result_btc_balance);
					curl_close($curlgetbalance);
				
					$order_request_amount = number_format($request_amount,10);
					$order_order_price = number_format($order_price,10);
				//XNV Buy
					$curlbuy = curl_init();
					curl_setopt_array($curlbuy, array(
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_URL => 'https://tradeogre.com/api/v1/order/buy',
							CURLOPT_USERPWD => 'TRADEOGRE API USERNAME'.':'.'TRADEOGRE API PASS',
							CURLOPT_USERAGENT => 'Codular Sample cURL Request',
							CURLOPT_POST => 1,
							CURLOPT_POSTFIELDS => array(
														'market' => 'BTC-XNV',
														'quantity' => $order_request_amount,
														'price' => $order_order_price
														)
));

					
					$result_marketbuy = json_decode(curl_exec($curlbuy),true);
					print_r($result_marketbuy);
					$result_buy = $result_marketbuy["success"];		
					curl_close($curlbuy); 
					
					//check if buy was successfull
					
					if ($result_buy != "1") {
						
						echo "buy failed";
						// buy problem occured 
												}
					
					else if ($result_buy == "1") {
						 $result_btc_balance_after = json_decode(curl_exec($curlgetbalance),true);
						$result_btc_balance_after = floatval($result_btc_balance_after["available"]);
						curl_close($curlgetbalance);
						$total_btc_spent = $result_btc_balance - $result_btc_balance_after;
						$price_per_xnv = $total_btc_spent / $request_amount; 
						
						$sql_nerva_status = "UPDATE exchange SET nerva_sent_amount = '" . $request_amount . "' WHERE btc_deposit='" . $btcwallet . "'";
						if (mysqli_query($conn,$sql_nerva_status)) { echo "database success"; }
						else { echo "database problem" . mysqli_error($conn);}
						
						// send XNV to given wallet, total $request_amount
						$walletRPC = new walletRPC('IP OF XNV RPC WALLET', PORT);
						$transferxnv = $walletRPC->transfer(($request_amount),$nerva_deposit_addr);
						$sql_nerva_sent = "UPDATE exchange SET nerva_sent_status = '1' WHERE btc_deposit='" . $btcwallet . "'";
						if (mysqli_query($conn,$sql_nerva_sent)) {
						print_r($transferxnv); }
						
						
													}
					
					
	
										
						} 
											} 
}




mysqli_close($conn);

?>