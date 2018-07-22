<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('vendor/autoload.php');
use \Firebase\JWT\JWT;
define('SECRET_KEY','Your-Secret-Key'); /// secret key can be any random string
define('ALGORITHM','HS512'); // Algorithm used to sign the token,


$action = isset($_REQUEST['action'])?$_REQUEST['action']:"login";
if ($action == 'login' ) {
  if(true){
                    
                    $tokenId    = base64_encode(mcrypt_create_iv(32));
                    $issuedAt   = time();
                    $notBefore  = $issuedAt + 10;  //Adding 10 seconds
                    $expire     = $notBefore + 7200; // Adding 60 seconds
                    $serverName = 'http://localhost/php-json/'; /// set your domain name 
 
  					
                    /*
                     * Create the token as an array
                     */
                    $data = array(
                        'iat'  => $issuedAt,         // Issued at: time when the token was generated
                        'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
                        'iss'  => $serverName,       // Issuer
                        'nbf'  => $notBefore,        // Not before
                        'exp'  => $expire,           // Expire
                        'data' => array(                  // Data related to the logged user you can set your required data
							  'id'   => 1, // id from the users table
							   'name' => 'Rizi khan', //  name
							)
                    );
                  $secretKey = base64_decode(SECRET_KEY);
                  /// Here we will transform this array into JWT:
                  $jwt = JWT::encode(
                            $data, //Data to be encoded in the JWT
                            $secretKey, // The signing key
                             ALGORITHM 
                           ); 
                 $unencodedArray = array('jwt' => $jwt);
                  echo  json_encode (array('status' => 'success', 'result' => $unencodedArray));
           } else {
 
                  echo  "{'status' : 'error','msg':'Invalid email or passowrd'}";
 
                  }
     
     }  
	 
if ( $action == 'authenticate'){
	  try {
               $secretKey = base64_decode(SECRET_KEY); 
               $DecodedDataArray = JWT::decode($_REQUEST['token'], $secretKey, array(ALGORITHM));
 
               echo  "{'status' : 'success' ,'data':".json_encode($DecodedDataArray)." }";die();
 
               } catch (Exception $e) {
                echo "{'status' : 'fail' ,'msg':'Unauthorized'}";die();
               }
	
}
?>