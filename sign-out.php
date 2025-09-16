<?php 
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
session_start();
if (isset($_SESSION["sesIduser"])){

	$pageCode = "SGO";
	
	include("admin/in_variables.php");
	include("in_www.php");

	include("in_idiom.php");
	
	$conMsi= crearConexionMysqli();
	
	/*
	// INICIO Cerrar sesion de facebook si esta abierta
	if ($fbLoginPermitido){
		require_once 'facebook.php';
	
		$facebook = new Facebook(array(
		  'appId'  => $fbLoginAppId,
		  'secret' => $fbLoginSecret,
		));
	
		// Get User ID
		$user = $facebook->getUser();

		if ($user) {
		  try {
		    // Proceed knowing you have a logged in user who's authenticated.
		    $user_profile = $facebook->api('/me');
		  } catch (FacebookApiException $e) {
		    error_log($e);
		    $user = null;
		  }
		}

		if ($user){
			//echo $facebook->getLogoutUrl();
			$useragent = 'Mozilla/5.0 (X11; Linux i686; rv:10.0) Gecko/20100101 Firefox/10.0';
			$c = curl_init();
			curl_setopt($c, CURLOPT_USERAGENT, $useragent);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c, CURLOPT_URL, $facebook->getLogoutUrl());
	        echo curl_exec($c);
	        curl_close($c);
		}
	}
	// FIN    Cerrar sesion de facebook si esta abierta
	*/
	
	/*
	// INICIO Cerrar sesion Google
	include_once 'lib/Google/src/Google_Client.php';
	include_once 'lib/Google/src/contrib/Google_Oauth2Service.php';
	unset($_SESSION['token']);
	unset($_SESSION['userData']);
	$gClient = new Google_Client();
	$gClient->setApplicationName('Login to CodexWorld.com');
	$gClient->setClientId($googleLogClitId);
	$gClient->setClientSecret($googleLogCliSecret);
	$gClient->setRedirectUri("");
	$gClient->revokeToken();
	// FIN    Cerrar sesion Google
	*/
	
	
	foreach($_SESSION as $key => $value) {
		$_SESSION[$key] = NULL;
		unset($_SESSION[$key]);
	}
	//session_regenerate_id();
	session_regenerate_id(TRUE);
 	session_unset();
	session_destroy();

	mysqli_close($conMsi);
}

header("Location: /");
die();
?>