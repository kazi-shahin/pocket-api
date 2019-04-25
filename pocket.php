<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed


if(isset($_SERVER['HTTPS'])){
    $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
}
else{
    $protocol = 'http';
}
$baseurl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$pocket_app = '';
$pocket_key = '';
$pocket_token = '';

$linkedIn = new Happyr\LinkedIn\LinkedIn($lnkdn_id, $lnkdn_secret);

if($_GET['auth-status'] == 'success' &&  $_GET['auth-from'] == 'pocket'){
	echo 'Linkedin successfully!';
}elseif ($segments[1] == 'pocket') {

	$request_token = $_GET['request_token'];

	$url = 'https://getpocket.com/v3/oauth/authorize';
	$data = array(
		'consumer_key' => $pocket_key, 
		'code' => $request_token
	);
	$q = http_build_query($data);
	$options = array(
		'http' => array(
			'method'  => 'POST',
			'content' => $q,
			'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                "Content-Length: ".strlen($q)."\r\n"
		)
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	
	$access_token = explode('&',$result);
	$accessToken = str_replace('access_token=', '', $access_token[0]);
	header('Location: '.$baseurl.'networks?auth-status=success&auth-from=pocket');
}


	$url = 'https://getpocket.com/v3/oauth/request';
	$redirect_uri = $baseurl.'pocket';
	$data = array(
		'consumer_key' => $pocket_key, 
		'redirect_uri' => $redirect_uri
	);
	
	$options = array(
		'http' => array(
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);
	
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	$code = explode('=',$result);
	$request_token = $code[1];

	$pocket_login = 'https://getpocket.com/auth/authorize?request_token=%s&redirect_uri=%s?request_token=%s',$request_token,$redirect_uri,$request_token;
	header('Location: '.$pocket_login);
