<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if(empty($_POST)){
    echo"it is an empty post";
}
else
{
$Name = $_POST['name'];
$Email = $_POST['email'];
$Mobile = $_POST['mobile'];
$co = $_POST['co'];
        if($co == "no"){
           echo 'order not confimred';
           exit;
        }
        else{
$secret = "TEST2e75eb80d252ce6315734deb99db70c69bb626cc";
$clientid = "327693bf684336fe7dbeabaafb396723";
$url = "https://sandbox.cashfree.com/pg/orders";
$id = date('Y'.'m'.'d'.'H'.'i'.'s');

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("Content-Type: application/json",
                "x-client-id: $clientid",
                  "x-client-secret: $secret",
                  "x-api-version: 2023-08-01"
                ));
                $data = <<<JSON
                {
                    "order_id": "order_$id",
                    "order_amount": 10.12,
                    "order_currency": "INR",
                    "order_note": "Additional order info",
                    "customer_details": {
                        "customer_id": "$id",
                        "customer_name": "$Name",
                        "customer_email": "$Email",
                        "customer_phone": "$Mobile"
                    }
                }
                JSON;
                
// Disable SSL verification (for testing purposes only)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
// curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "Curl Error: " . curl_error($ch);
}else{
echo "$response";
}
$decode = json_decode($response);
// $link = $decode->payments;
$_SESSION['payment_session'] = $decode->payment_session_id;
header('Location:checkout.php');
}
}
?>
    

