# php-coffee-shop-api

This api is build for the coffee shop (coupon system), this app communicate with the mobile app (future development).

This is still beta version and requires more functions and more security.

<h4> Find below the sample code to call the methods.</h4>

define('API_CALL_URL', 'http://'.$_SERVER['HTTP_HOST'].'/api/api.php');

Method: login

$username =  isset($_POST["username"]) ? $_POST["username"]: "";

$password =  isset($_POST["passowrd"]) ? $_POST["passowrd"]: "";

$api_data = array('method' => 'login', 'username' => $username, 'password' => $password);

$api_result = curl_call($api_data);

// do anything you want with your response

#$json_token =  $json_decoded['result']['jwt'];

echo $api_result;	

Method: forgetpwd

$username =  isset($_POST["username"]) ? $_POST["username"]: "";

$api_data = array('method' => 'forgetpwd', 'username' => $username);

$api_result = curl_call($api_data);

// do anything you want with your response

#$json_token =  $json_decoded['result']['jwt'];

echo $api_result;	

//
Method: forgetpwdpin

$code =  isset($_POST["code"]) ? $_POST["code"]: "";

$jwt_token =  isset($_POST["jwt_token"]) ? $_POST["jwt_token"]: "";

$api_data = array('method' => 'forgetpwdpin', 'code' => $code, 'jwt_token' => $jwt_token);

$api_result = curl_call($api_data);

// do anything you want with your response

#$json_token =  $json_decoded['result']['jwt'];

echo $api_result;	

Method: changepwd

$jwt_token =  isset($_POST["jwt_token"]) ? $_POST["jwt_token"]: "";

$password =  isset($_POST["password"]) ? $_POST["password"]: "";

$verify_password =  isset($_POST["verify_password"]) ? $_POST["verify_password"]: "";

$api_data = array('method' => 'changepwd', 'password' => $password, 'verify_password' => $verify_password, 'jwt_token' => $jwt_token);

$api_result = curl_call($api_data);

// do anything you want with your response

#$json_token =  $json_decoded['result']['jwt'];

echo $api_result;	


Method: dashboard

$jwt_token =  isset($_POST["jwt_token"]) ? $_POST["jwt_token"]: "";

$api_data = array('method' => 'dashboard', 'jwt_token' => $jwt_token);

$api_result = curl_call($api_data);

// do anything you want with your response

#$json_token =  $json_decoded['result']['jwt'];

echo $api_result;	

// get last 10 trans for the user with limit

Method: gettrans

$jwt_token =  isset($_POST["jwt_token"]) ? $_POST["jwt_token"]: "";

$limit     =  isset($_POST["limit"]) ? $_POST["limit"]: 10;

$api_data = array('method' => 'gettrans', 'jwt_token' => $jwt_token, 'limit' => $limit);

$api_result = curl_call($api_data);

// do anything you want with your response

#$json_token =  $json_decoded['result']['jwt'];

echo $api_result;	

// post api to get qrcode 32 char data string

Method: setqrcode

$jwt_token =  isset($_POST["jwt_token"]) ? $_POST["jwt_token"]: "";

$amount     =  isset($_POST["amount"]) ? $_POST["amount"]: 0;

$api_data = array('method' => 'setqrcode', 'jwt_token' => $jwt_token, 'amount' => $amount);

$api_result = curl_call($api_data);

// do anything you want with your response

#$json_token =  $json_decoded['result']['jwt'];

echo $api_result;	

// confirmed qr code by end user, Yes presses

Method: conqrcode

$jwt_token =  isset($_POST["jwt_token"]) ? $_POST["jwt_token"]: "";

$qrcode     =  isset($_POST["qrcode"]) ? $_POST["qrcode"]: "";

$confirm_sts = isset($_POST["confirm_sts"]) ? $_POST["confirm_sts"]: "no";

$api_data = array('method' => 'conqrcode', 'jwt_token' => $jwt_token, 'qrcode' => $qrcode, 'confirm_sts' => $confirm_sts);

$api_result = curl_call($api_data);

// do anything you want with your response

#$json_token =  $json_decoded['result']['jwt'];

echo $api_result;	

// register a device

Method: setup

$jwt_token =  isset($_POST["jwt_token"]) ? $_POST["jwt_token"]: "";

$code =  isset($_POST["code"]) ? $_POST["code"]: "";

$device_id	 =	md5('12345'); 

$api_data = array('method' => 'setup', 'jwt_token' => $jwt_token, 'code' => $code, 'device_id' => $device_id);

$api_result = curl_call($api_data);

// do anything you want with your response

#$json_token =  $json_decoded['result']['jwt'];

echo $api_result;	

// remove the user device

Method: remdevice

$jwt_token =  isset($_POST["jwt_token"]) ? $_POST["jwt_token"]: "";

$confirm_sts = isset($_POST["confirm_sts"]) ? $_POST["confirm_sts"]: "no";

$device_id	 =	md5('12345'); 

$api_data = array('method' => 'remdevice', 'jwt_token' => $jwt_token, 'confirm_sts' => $confirm_sts, 'device_id' => $device_id);

$api_result = curl_call($api_data);

// do anything you want with your response

#$json_token =  $json_decoded['result']['jwt'];

echo $api_result;	
	
function curl_call($data){

$data_string = json_encode($data);               

$ch = curl_init(API_CALL_URL);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     

curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);   
                                                               
//set the content type to application/json

curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

//return response instead of outputting

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
                                                                                                                
$result = curl_exec($ch);

 #$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

#$curl_errno  = curl_errno($ch);

#$json_decoded = json_decode($result, true); 

// close the connection, release resources used

curl_close($ch);

return $result;	
	
}

