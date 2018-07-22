<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
define('MAIN',realpath('../'));
include_once MAIN.'/includes/functions.php';
require_once('vendor/autoload.php');
use \Firebase\JWT\JWT;
define('SECRET_KEY','XC$#%$^&*)BGB$#%$#%#vdfjgdjh4FVDFBFGHRT43535$%$_%()^'); /// secret key can be any random string
define('ALGORITHM','HS512'); // Algorithm used to sign the token,


$link 		= 	open_connection();
// get the HTTP method, path and body of the request
ini_set('display_errors',1);
error_reporting(E_ALL);
$result	=	"";

$input = json_decode(file_get_contents('php://input'),true);

if(empty($input)){
 	$result	=	array('status' => false,
					  'msg_short' => 'invalid post',
					  'msg_long'  => 'you need to send a valid post to server'
	);

 echo  json_encode($result);
 exit();
}


$method	 =	isset($input['method']) ? $input['method'] : "";
if (empty($method) || !ctype_alpha($method)){ // only alpha method allowed
 $result	=	array('status' => false,
					  'msg_short' => 'invalid method called',
					  'msg_long'  => 'you need to called a valid method on server'
	);
 echo  json_encode($result);
 exit();

}

// so we have valid called
#$apikey		=	isset($input['apikey']) ? $input['apikey'] : '';
$jwt_token			=	isset($input['jwt_token']) ? $input['jwt_token'] : '';
$username			=	isset($input['username']) ? $input['username'] : '';
$password			=	isset($input['password']) ? $input['password'] : '';
$verify_password	=	isset($input['verify_password']) ? $input['verify_password'] : '';
$code				=	isset($input['code']) ? $input['code'] : '';
$limit				=	isset($input['limit']) ? $input['limit'] : 10;
$amount				=	isset($input['amount']) ? $input['amount'] : 0;
$confirm 			=	isset($input['confirm']) ? $input['confirm'] : 'no';
$qrcode 			=	isset($input['qrcode']) ? $input['qrcode'] : '';
$device_id 			=	isset($input['device_id']) ? $input['device_id'] : '';
$confirm_sts 		=	isset($input['confirm_sts']) ? strtolower($input['confirm_sts']) : 'no';

if (strcasecmp(strtolower($method), 'login') == 0 ){ // login method call 
  if (!empty($username) && !empty($password) ){
   $sql = "Select * from tbl_users where username = '".FixString($link, $username)."' and password = '".FixString($link, md5($password))."' and status >= 0";
		 $row = RunQuerySingle($link, $sql);
		 	 if( !empty($row)){
				if ( $row['status']  == 1 ){
					 $tokenId    = base64_encode(mcrypt_create_iv(32));
					  $issuedAt   = time();
					  $notBefore  = $issuedAt + 1;  //Adding 1 seconds
					  $expire     = $notBefore + 7200; // Adding 60 seconds
					  $serverName = 'http://127.0.0.1/btc-cash/api/'; /// set your domain name 
				
					  
					  /*
					   * Create the token as an array
					   */
					  $data = array(
						  'iat'  => $issuedAt,         // Issued at: time when the token was generated
						  'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
						  'iss'  => $serverName,       // Issuer
						  'nbf'  => $notBefore,        // Not before
						  'exp'  => $expire,           // Expire
						  'user' => array(                  // Data related to the logged user you can set your required data
								'id'   => $row['id'], // id from the users table
								 'full_name' => $row['full_name'], //  name
							  )
					  );
	
					$secretKey = base64_decode(SECRET_KEY);
					/// Here we will transform this array into JWT:
					$jwt_token = JWT::encode(
							  $data, //Data to be encoded in the JWT
							  $secretKey, // The signing key
							   ALGORITHM 
							 ); 
			
								
								
					$result	=	array('status' => true, 'msg' => 'success',
									 'jwt_token' => $jwt_token,
									  'msg_short' => '',
									  'msg_long'  => ''
							  );
			  
								
							}
							else{
								 $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Your account is not active.',
									  'msg_long'  => 'Your account is block by site admin.'
							     );
					}
					
			 }
			 else{
				 
				  $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Invalid username or passowrd.',
									  'msg_long'  => 'Invalid username or passowrd try again'
							  );
			 }
	
			  
		} else {
		
					 $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Username or passowrd is empty.',
									  'msg_long'  => 'Invalid username or passowrd try again'
							  );
			}


   echo  json_encode($result);

}  


if (strcasecmp(strtolower($method), 'forgetpwd') == 0 ){ // forgetpwd method call 
  if (!empty($username)){
   $sql = "Select * from tbl_users where username = '".FixString($link, $username)."' and status >= 0";
		 $row = RunQuerySingle($link, $sql);
		 	 if( !empty($row)){
				if ( $row['status']  == 1 ){
					  $tokenId    = base64_encode(mcrypt_create_iv(32));
					  $issuedAt   = time();
					  $notBefore  = $issuedAt + 1;  //Adding 1 seconds
					  $expire     = $notBefore + 7200; // Adding 60 seconds
					  $serverName = 'http://127.0.0.1/btc-cash/api/'; /// set your domain name 
					  /*
					   * Create the token as an array
					   */
					  $data = array(
						  'iat'  => $issuedAt,         // Issued at: time when the token was generated
						  'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
						  'iss'  => $serverName,       // Issuer
						  'nbf'  => $notBefore,        // Not before
						  'exp'  => $expire,           // Expire
						  'user' => array(                  // Data related to the logged user you can set your required data
								'id'   => $row['id'], // id from the users table
								 'full_name' => $row['full_name'], //  name
							  )
					  );
	
					$secretKey = base64_decode(SECRET_KEY);
					/// Here we will transform this array into JWT:
					$jwt_token = JWT::encode(
							  $data, //Data to be encoded in the JWT
							  $secretKey, // The signing key
							   ALGORITHM 
							 ); 
		 			// send email to end user for password reset token
					$validate_token = create_email_token($link, $row['id']);
					if (!empty($validate_token)){
					$subject	=	"Password reset request";
					$message	= "Hi ". $row['full_name']. ',<br/> '.$row['email'].' <br/>
									Please use the token below to next step <br/> Token: '.$validate_token.' <br/><br/> Thanks';
					//	if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
						 $result	=	array('status' => true, 'msg' => 'success',
												 'jwt_token' => $jwt_token,
												  'msg_short' => '' ,
												  'msg_long'  => ''
										  );
						 /*
						 if (mail_new_template($row['email'], $subject, $message)){	
								  $result	=	array('status' => true, 'msg' => 'success',
												 'jwt_token' => $jwt_token,
												  'msg_short' => $message ,
												  'msg_long'  => ''
										  );
						 }
						 else{
											 $result	=	array('status' => false, 'msg' => 'failed',
												  'msg_short' => 'Your account is not active.',
												  'msg_long'  => 'Your account is block by site admin.'
											 );
								}
					*/
					}
					else{
						$result	=	array('status' => false, 'msg' => 'failed',
						  'msg_short' => 'API failed to create a verification code.',
						  'msg_long'  => 'API failed to create a verification code.'
					 );
				  }
				}
				else{
					 $result	=	array('status' => false, 'msg' => 'failed',
						  'msg_short' => 'Your account is not active.',
						  'msg_long'  => 'Your account is block by site admin.'
					 );
		}
		
	 }
	 else{
		 
		  $result	=	array('status' => false, 'msg' => 'failed',
							  'msg_short' => 'Invalid username.',
							  'msg_long'  => 'Invalid username  try again'
					  );
	 }

		  
	} else {
	
				 $result	=	array('status' => false, 'msg' => 'failed',
								  'msg_short' => 'Username or passowrd is empty.',
								  'msg_long'  => 'Invalid username or passowrd try again'
						  );
	}


   echo  json_encode($result);

}  

//forgetpwdpin
if (strcasecmp(strtolower($method), 'forgetpwdpin') == 0 ){ // login method call 
  if (!empty($jwt_token) && !empty($code) ){
	 try {
		   $secretKey = base64_decode(SECRET_KEY); 
		   $decodedDataArray = JWT::decode($jwt_token, $secretKey, array(ALGORITHM));
		   $user_id = $decodedDataArray->user->id;
		   $query	= "SELECT * FROM `tbl_auth_token`  WHERE user_id = '".FixString($link, $user_id)."' and token_code = '".FixString($link, $code)."' and status = 1 and type = 1";
	  	   $row = RunQuerySingle($link, $query);
 		   if(!empty($row)){
			  $query = "Update `tbl_auth_token` SET status = 0, updated = now() WHERE user_id = '".FixString($link, $user_id)."' and token_code = '".FixString($link, $code)."' and status = 1 and type = 1";
				  if (MySQLQuery($link, $query)){
					  $result	=	array('status' => true, 'msg' => 'success',
													 'jwt_token' => $jwt_token,
													  'msg_short' => 'All done' ,
													  'msg_long'  => ''
											  );
				  }
			   }
		   
		   } catch (Exception $e) {
			  $result	=	array('status' => false, 'msg' => 'failed',
								  'msg_short' =>   $e->getMessage(),
								  'msg_long'  => 'Unauthorized.'
			  );
          }
			  
		} else {
		
					 $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Verficate Code or token is empty.',
									  'msg_long'  => 'Invalid username or passowrd try again'
							  );
	 }


   echo  json_encode($result);

}  

//changepwd, app submit for change password
if (strcasecmp(strtolower($method), 'changepwd') == 0 ){ // login method call 
  if(!empty($jwt_token)){
	  if (!empty($password) && !empty($verify_password) ){
		  if ( md5($password) == md5($verify_password)){	  
			 try {
				   $secretKey = base64_decode(SECRET_KEY); 
				   $decodedDataArray = JWT::decode($jwt_token, $secretKey, array(ALGORITHM));
				   $user_id = $decodedDataArray->user->id;
				   $query	= "SELECT id FROM `tbl_users` WHERE id = '".FixString($link, $user_id)."' and status = 1 ";
				   $row = RunQuerySingle($link, $query);
				   if(!empty($row)){
					  $query = "Update `tbl_users` SET password = '".FixString($link, md5($password))."', updated = now() WHERE id = '".FixString($link, $user_id)."' and status =1 ";
					  if (MySQLQuery($link, $query)){
						  $result	=	array('status' => true, 'msg' => 'success',
														 'jwt_token' => $jwt_token,
														  'msg_short' => 'Password has been updated successfully.' ,
														  'msg_long'  => 'Password has been updated successfully.'
												  );
					  }
						/*
						 $result	=	array('status' => true, 'msg' => 'success',
																	 'jwt_token' => $jwt_token,
																	  'msg_short' => 'Invalid token' ,
																	  'msg_long'  => ''
															  ); 
							
						*/
					}
					else{
							 $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' =>   'No record found against give token_id.',
									  'msg_long'  => 'Unauthorized.'
				 		 );	
					}
				   } catch (Exception $e) {
					  $result	=	array('status' => false, 'msg' => 'failed',
										  'msg_short' =>   $e->getMessage(),
										  'msg_long'  => 'Unauthorized.'
					  );
				  }
			}
			else{
			 $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' =>   'Passwotd and verify password are not same.',
									  'msg_long'  => 'Unauthorized.'
				  );	
			}
	  
			} else {
		
					 $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Password fields are empty',
									  'msg_long'  => 'Password fields are empty'
							  );
		 }
  }
  else{
	$result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Invalid token, you have to login first.',
									  'msg_long'  => 'Invalid token, you have to login first.'
							  );  
  }


   echo  json_encode($result);

}  


// regsiter a new site and return a unqiue api_key
if (strcasecmp(strtolower($method), 'dashboard') == 0 ){ // post called
 if (!empty($jwt_token)){
	 try {
               $secretKey = base64_decode(SECRET_KEY); 
               $decodedDataArray = JWT::decode($jwt_token, $secretKey, array(ALGORITHM));
               $user_id = $decodedDataArray->user->id;
				if(!empty($user_id)){
				  $query		= "SELECT f.total_balance, f.available_balance, f.currency_code FROM `tbl_user_funds` f 
				  INNER JOIN tbl_users u ON u.id = f.user_id  WHERE u.id = '".FixString($link, $user_id)."' and u.status = 1 ORDER by f.id DESC LIMIT 1";
				  $rowSummary   = RunQuerySingle($link, $query);
			      $summary_arr	 = array();
				  $trans_arr    = array();	
				  if (!empty($rowSummary)){
					 $summary_arr = array('total_balance' => $rowSummary['total_balance'],
					  'available_balance' => $rowSummary['available_balance'],
					  'currency_code' => $rowSummary['currency_code']); 
					  
					  // get latest 3 trans for this user
					  $query		= "SELECT * FROM `tbl_sale_trans` WHERE from_user_id = '".FixString($link, $user_id)."' order by id desc LIMIT 3 ";
				      $rowsTrans    = RunQuery($link, $query);
 					  if (!empty($rowsTrans)){
						foreach ($rowsTrans as $rowTrans){
							$trans_arr[] = array('id' => $rowTrans['id'], 'amount' => $rowTrans['amount'], 'recevier_name' => $rowTrans['rec_user_id'], 'date_time' => $rowTrans['created']);
						}
					  } // end if rowsTrans
					  
				  } // end if rowSummary
				  
				   $result	=	array('status' => true, 'msg' => 'success',
				 					  'data' => $summary_arr,	
									   'trans' => $trans_arr,	
									  'msg_short' => '',
									  'msg_long'  => ''
							  );
				}
				else{
					$result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Invalid/expired token, No user record found against the give token.',
									  'msg_long'  => 'Invalid/expired token, you have to login again and try.'
							  );  
				}
			   
               } catch (Exception $e) {
				  $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' =>   $e->getMessage(),
									  'msg_long'  => 'Unauthorized.'
				  );
			 }
   }
   else{
	$result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Invalid token, you have to login first.',
									  'msg_long'  => 'Invalid token, you have to login first.'
							  );  
  }
	 
	 echo  json_encode($result);

	exit();
	
}

//gettrans
if (strcasecmp(strtolower($method), 'gettrans') == 0 ){ // post called
 if (!empty($jwt_token)){
	 try {
               $secretKey = base64_decode(SECRET_KEY); 
               $decodedDataArray = JWT::decode($jwt_token, $secretKey, array(ALGORITHM));
               $user_id = $decodedDataArray->user->id;
				if(!empty($user_id)){
				  $trans_arr    = array();	
				  $query		= "SELECT s.*, u.full_name FROM `tbl_sale_trans` s INNER JOIN tbl_users u ON u.id = s.from_user_id WHERE u.id = '".FixString($link, $user_id)."' and s.status = 1 order by s.id desc LIMIT $limit ";
				      $rowsTrans    = RunQuery($link, $query);
 					  if (!empty($rowsTrans)){
						foreach ($rowsTrans as $rowTrans){
							$trans_arr[] = array('id' => $rowTrans['id'], 'amount' => $rowTrans['amount'], 'recevier_name' => $rowTrans['full_name'], 'date_time' => $rowTrans['created']);
						}
					  } // end if rowsTrans
					  
				      $result	=	array('status' => true, 'msg' => 'success',
									   'trans' => $trans_arr,	
									  'msg_short' => '',
									  'msg_long'  => ''
							  );
				}
				else{
					$result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Invalid/expired token, No user record found against the give token.',
									  'msg_long'  => 'Invalid/expired token, you have to login again and try.'
							  );  
				}
			   
               } catch (Exception $e) {
				  $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' =>   $e->getMessage(),
									  'msg_long'  => 'Unauthorized.'
				  );
			 }
   }
   else{
	$result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Invalid token, you have to login first.',
									  'msg_long'  => 'Invalid token, you have to login first.'
							  );  
  }
	 
	 echo  json_encode($result);

	exit();
	
}

//setqrcode
if (strcasecmp(strtolower($method), 'setqrcode') == 0 ){ // post called
 if (!empty($jwt_token)){
	  if ($amount > 0 ){ 
	   try {
			   $secretKey = base64_decode(SECRET_KEY); 
               $decodedDataArray = JWT::decode($jwt_token, $secretKey, array(ALGORITHM));
               $user_id = $decodedDataArray->user->id;
				if(!empty($user_id)){
				 $inc_gstTotal  = $amount;
				 $gst_total   = ($inc_gstTotal * .1);
				 $ext_gstTotal = ($inc_gstTotal - $gst_total);
				   $qrcode_token = md5(bin2hex(openssl_random_pseudo_bytes(16).gen_password(16)));
						$query  =  "INSERT INTO `tbl_orders` (`user_id`, `total_ex_gst`, `total_gst`, `total_inc_gst`, qrcode_token, `created`,`status`) 
								 VALUES (".$user_id.", ".FixString($link, clean_amount($ext_gstTotal)).", ".FixString($link, clean_amount($gst_total)).", ".FixString($link, clean_amount($inc_gstTotal)).",'".FixString($link, $qrcode_token)."', now(), 0 )";
								 if(MySQLQuery($link, $query)){
								  $result	=	array('status' => true, 'msg' => 'success',
											   'qrcode' => $qrcode_token,	
											  'msg_short' => '',
											  'msg_long'  => ''
									  );
							 }
							 else{
									 $result	=	array('status' => false, 'msg' => 'failed',
											  'msg_short' => 'API failed to create transcation. please try again',
											  'msg_long'  => 'API failed to create transcation.{'.$query.'}'
									  );
					 
							 }
					}
					else{
						$result	=	array('status' => false, 'msg' => 'failed',
										  'msg_short' => 'Invalid/expired token, No user record found against the give token.',
										  'msg_long'  => 'Invalid/expired token, you have to login again and try.'
								  );  
					}
			   
               } catch (Exception $e) {
				  $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' =>   $e->getMessage(),
									  'msg_long'  => 'Unauthorized.'
				  );
			 }
	  }
	  else{
		  
	  }
   }
   else{
	$result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Invalid token, you have to login first.',
									  'msg_long'  => 'Invalid token, you have to login first.'
							  );  
  }
	 
	 echo  json_encode($result);

	exit();
	
}

// read the qrcode and send a json array to end user
if (strcasecmp(strtolower($method), 'getqrcode') == 0 ){ // post called
if (!empty($jwt_token)){
  if (strlen($qrcode) == 32 ){ 
	   try {
			   $secretKey = base64_decode(SECRET_KEY); 
               $decodedDataArray = JWT::decode($jwt_token, $secretKey, array(ALGORITHM));
               $user_id = $decodedDataArray->user->id;
				if(!empty($user_id)){
					 $query  =  "SELECT * FROM `tbl_orders` WHERE qrcode_token = '".FixString($link, $qrcode)."' and status = 1 LIMIT 1 ";
					  $row   = RunQuerySingle($link, $query);
					  if(!empty($row)){
						 $result	=	array('status' => true, 'msg' => 'success',
												   'recevier_name' => $row['user_id'],
												   'amount' => $row['total_inc_gst'],	
												  'msg_short' => '',
												  'msg_long'  => ''
										  );  
						  
					  }
					  else{
						  $result	=	array('status' => false, 'msg' => 'failed',
										  'msg_short' => 'Invalid/qrcode code, it has been used.',
										  'msg_long'  => 'Invalid/qrcode code, it has been used.'
								  );  
					  }
					}
					else{
						$result	=	array('status' => false, 'msg' => 'failed',
										  'msg_short' => 'Invalid/expired token, No user record found against the give token.',
										  'msg_long'  => 'Invalid/expired token, you have to login again and try.'
								  );  
					}
			   
               } catch (Exception $e) {
				  $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' =>   $e->getMessage(),
									  'msg_long'  => 'Unauthorized.'
				  );
			 }
	  }
	  else{
		  $result	=	array('status' => false, 'msg' => 'failed',
										  'msg_short' => 'Invalid/expreid qrcode  .',
										  'msg_long'  => 'Invalid/expired qrcode.'
								  );  
	  }
   }
   else{
	$result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Invalid token, you have to login first.',
									  'msg_long'  => 'Invalid token, you have to login first.'
							  );  
  }
	 
	 echo  json_encode($result);

	exit();

}

// confirm the trans made by user
if (strcasecmp(strtolower($method), 'conqrcode') == 0 ){ // post called
if (!empty($jwt_token)){
 if (strlen($qrcode) == 32 ){ 
	   try {
			   $secretKey = base64_decode(SECRET_KEY); 
               $decodedDataArray = JWT::decode($jwt_token, $secretKey, array(ALGORITHM));
               $user_id = $decodedDataArray->user->id;
				if(!empty($user_id)){
					 $query  =  "SELECT * FROM `tbl_orders` WHERE qrcode_token = '".FixString($link, $qrcode)."' and status = 0 LIMIT 1 ";
					  $row   = RunQuerySingle($link, $query);
					  if(!empty($row)){
					  // we have a valid token, let cehck user accept the transcation
					  if ( $confirm_sts == "yes"){
						$available_balance = chk_user_balance($link, $user_id);
						$daily_limit = chk_user_daily_limit($link, $user_id);
						 if (($available_balance) >= ($row['total_inc_gst']) ){
							// daily limit check
								if (($daily_limit) >= ($row['total_inc_gst']) ){ 
								  $query = "Update tbl_orders SET status = 1 WHERE id = ".$row["id"]." and qrcode_token = '".FixString($link, $qrcode)."'";
									   if(MySQLQuery($link, $query)){
										   $query = "INSERT into tbl_sale_trans (order_id, from_user_id, rec_user_id, amount, created) values (".FixString($link, $row["id"]).", '".FixString($link, $user_id)."','".FixString($link, $row['user_id'])."','".FixString($link, $row['total_inc_gst'])."', now()) ";   
											 $trans_id =  MySQLInsertQuery($link, $query);
											 if( $trans_id > 0 ){
											 // update user balance, detech the current order amount from the available balance
											  update_user_balance($link, $row['total_inc_gst'],  $user_id );
											  $result	=	array('status' => true, 'msg' => 'success',
														   'trans_id' => $trans_id,	
														  'msg_short' => '',
														  'msg_long'  => ''
												  );
										 }
										 else{
												 $result	=	array('status' => false, 'msg' => 'failed',
															'msg_short' => 'Invalid/qrcode code, sql query error.',
															'msg_long'  => 'Invalid/qrcode code, it has been used.'
												  );
								 
										 }
									 }
								}
								else{
										 $result	=	array('status' => false, 'msg' => 'failed',
													  'msg_short' => 'You have reached your daily limit, please wait for next turn.',
													  'msg_long'  => 'You have reached your daily limit, please wait for next turn'
											  );
								   }	
							
						   } // balance check
						   else{
							    $result	=	array('status' => true, 'msg' => 'failed',
												  'msg_short' => 'Insufficient balance.',
												  'msg_long'  => 'You running a low balance, login and topup more balance.'
										  );
						   }
					   }
					   elseif($confirm_sts == 'no'){ // if user reject the transcation
						   $query = "Update tbl_orders SET status = -1 WHERE id = ".FixString($link, $row["id"])." and qrcode_token = '".FixString($link, $qrcode)."'"; // rejected by end user
							 if(MySQLQuery($link, $query)){
								$query = "INSERT into tbl_sale_trans (order_id, from_user_id, rec_user_id, amount, created, status) values (".FixString($link, $row["id"]).", '".FixString($link, $user_id)."','".FixString($link, $row['user_id'])."','".FixString($link, $row['total_inc_gst'])."', now(), -1) ";   
								   $trans_id =  MySQLInsertQuery($link, $query);
								   if( $trans_id > 0 ){
									$result	=	array('status' => true, 'msg' => 'success',
												 'trans_id' => $trans_id,	
												'msg_short' => 'User has rejected the transction',
												'msg_long'  => ''
										);
							   }
					  	 }
					   }
					  }
					  else{
						  $result	=	array('status' => false, 'msg' => 'failed',
										  'msg_short' => 'Invalid/qrcode code, it has been used.',
										  'msg_long'  => 'Invalid/qrcode code, it has been used.'
								  );  
					  }
					}
					else{
						$result	=	array('status' => false, 'msg' => 'failed',
										  'msg_short' => 'Invalid/expired token, No user record found against the give token.',
										  'msg_long'  => 'Invalid/expired token, you have to login again and try.'
								  );  
					}
			   
               } catch (Exception $e) {
				  $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' =>   $e->getMessage(),
									  'msg_long'  => 'Unauthorized.'
				  );
			 }
	  }
	  else{
		  $result	=	array('status' => false, 'msg' => 'failed',
										  'msg_short' => 'Invalid/expreid qrcode  .',
										  'msg_long'  => 'Invalid/expired qrcode.'
								  );  
	  }
   }
   else{
	$result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Invalid token, you have to login first.',
									  'msg_long'  => 'Invalid token, you have to login first.'
							  );  
  }
	 
	 echo  json_encode($result);

	exit();

}

if (strcasecmp(strtolower($method), 'chkbalance') == 0 ){ // post called
  $balance = chk_user_balance($link, $user_id);
	if ( $balance <= 0 ){ 
	$result	=	array('status' => false,
								'msg_short' => 'Insufficient balance.',
								'msg_long'  => 'You running a low balance, login and topup more balance.'
				);
	}
	elseif ($balance > 0){
		$result	=	array('status' => true,
		'msg_short' => 'Your current balance '. $balance ,
		'msg_long'  => 'You current balance is '. $balance
		);
	}

 echo  json_encode($result);
exit();
}


// setup/register a the devide
if (strcasecmp(strtolower($method), 'setup') == 0 ){ // post called
 // post called
 if (!empty($jwt_token) && !empty($code) ){
		 try {
			   $secretKey = base64_decode(SECRET_KEY); 
			   $decodedDataArray = JWT::decode($jwt_token, $secretKey, array(ALGORITHM));
			   $user_id = $decodedDataArray->user->id;
			   $query	= "SELECT * FROM `tbl_auth_token` WHERE user_id = '".FixString($link, $user_id)."' and token_code = '".FixString($link, $code)."' and status = 1 and type = 2"; // type 2 => for setup code
			   $row = RunQuerySingle($link, $query);
			   if(!empty($row)){
				  $query = "Update `tbl_auth_token` SET status = 0, updated = now() WHERE user_id = '".FixString($link, $user_id)."' and token_code = '".FixString($link, $code)."' and status = 1 and type = 2";
					 if (MySQLQuery($link, $query)){
					   // let register a device to this user, first checkd evice is already register with the user
						$query		=  "SELECT id FROM tbl_user_devices WHERE user_id = ".FixString($link, $user_id)." and device_id =  '".FixString($link, $device_id)."' and status = 1 ";
				  		$row   		=  RunQuerySingle($link, $query);
						if(empty($row)){
						 $query  = "INSERT into tbl_user_devices(user_id, device_id, created) values (".FixString($link, $user_id)." , '".FixString($link, $device_id)."', now()) ";		
						 if (MySQLQuery($link, $query)){
							 $result	=	array('status' => true, 'msg' => 'success', 'jwt_token' => $jwt_token,
									  'msg_short' => 'Device ('.$device_id.') has been register successfully.',
									  'msg_long'  => 'Device has been register successfully.'
							  );
						 }
						 else{
							  $result	=	array('status' => false, 'msg' => 'failed',
										'msg_short' => 'API failed to register the device. please try again',
										'msg_long'  => 'API failed to register the device.{'.$query.'}'
								);
						 }
						}
						else{
							$result	=	array('status' => false, 'msg' => 'failed',
										  'msg_short' => 'Record found against the give token and device id.',
										  'msg_long'  => 'Record found against the give token and devide id.'
								  );  
						}
					  }
				   }
				   else{
					  $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Verficate Code already used  or token is empty.',
									  'msg_long'  => 'Invalid username or passowrd try again'
							  );  
				   }
			   
			   } catch (Exception $e) {
				  $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' =>   $e->getMessage(),
									  'msg_long'  => 'Unauthorized.'
				  );
			  }
 		}
		   else{
			$result	=	array('status' => false, 'msg' => 'failed',
											  'msg_short' => 'Verficate Code/Invalid token, please try again.',
											  'msg_long'  => 'Verficate Code/Invalid token, please try again.'
									  );  
		  }
	 
	 echo  json_encode($result);

	exit();
	


}
// unreigster the devide
if (strcasecmp(strtolower($method), 'remdevice') == 0 ){ // post called
 // post called
 if (!empty($jwt_token)){
	 try {
               $secretKey = base64_decode(SECRET_KEY); 
               $decodedDataArray = JWT::decode($jwt_token, $secretKey, array(ALGORITHM));
               $user_id = $decodedDataArray->user->id;
				if(!empty($user_id)){
				  $query		=  "SELECT id FROM tbl_user_devices WHERE user_id = ".FixString($link, $user_id)." and device_id =  '".FixString($link, $device_id)."' and status = 1 LIMIT 1";
				  $row   		=  RunQuerySingle($link, $query);
				  if (!empty($row)){
				      $query = "UPDATE tbl_user_devices SET status = 0, updated = now() WHERE id  = ".FixString($link, $row['id'])."";	
					    if(MySQLQuery($link, $query)){
					   		$result	=	array('status' => true, 'msg' => 'success',
									  'msg_short' => 'App need to logout the user and remove all local data from app and force to register the device again',
									  'msg_long'  => 'App need to logout the user and remove all local data from app and force to register the device again'
							  );
						}
						 else{
							   $result	=	array('status' => false, 'msg' => 'failed',
										'msg_short' => 'API failed to delete the device. please try again',
										'msg_long'  => 'API failed to delete the device.{'.$query.'}'
								);
					 
					  }
				  } // end if rowsTrans
					else{
						$result	=	array('status' => false, 'msg' => 'failed',
										  'msg_short' => 'Invalid/expired token, No device record found against the give token and devide id.',
										  'msg_long'  => 'Invalid/expired token, No device record found against the give token and devide id.'
								  );  
					 }
				}
			   
               } catch (Exception $e) {
				  $result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' =>   $e->getMessage(),
									  'msg_long'  => 'Unauthorized.'
				  );
			 }
   }
   else{
	$result	=	array('status' => false, 'msg' => 'failed',
									  'msg_short' => 'Invalid token, you have to login first.',
									  'msg_long'  => 'Invalid token, you have to login first.'
							  );  
  }
	 
	 echo  json_encode($result);

	exit();
	


}


?>
