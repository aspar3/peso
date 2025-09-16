<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
	session_start();

	$pageCode = "LTG";
	
	//Include Google client library
	include_once 'lib/Google/src/Google_Client.php';
	include_once 'lib/Google/src/contrib/Google_Oauth2Service.php';
	
	include("admin/in_variables.php");
	
	include("in_idiom.php");
	
	include_once 'classes/User.php';
	include_once 'classes/Funciones.php';
	
	$conMsi = crearConexionMysqli();
	
	$nombreGeneral = "Daviganismo";
	$googleLogClitId = "32932556296-m17mm59fu5sak8rmjk7apn7dq28imeci.apps.googleusercontent.com";
	$googleLogCliSecret = "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";
	//Call Google API
	$gClient = new Google_Client();
	$gClient->setApplicationName($nombreGeneral);
	$gClient->setClientId($googleLogClitId);
	$gClient->setClientSecret($googleLogCliSecret);
	$gClient->setRedirectUri($accesoHttp.$rootURL."/login-third-google.php");
	
	$google_oauthV2 = new Google_Oauth2Service($gClient);
	
	if(isset($_GET['code'])){
		$gClient->authenticate($_GET['code']);
		$_SESSION['token'] = $gClient->getAccessToken();
		header("Location: " . filter_var($redirectURL, FILTER_SANITIZE_URL));
	}
	
	if (isset($_SESSION['token'])) {
		$gClient->setAccessToken($_SESSION['token']);
	}
	
	if ($gClient->getAccessToken()) {
		//Get user profile data from google
		$gpUserProfile = $google_oauthV2->userinfo->get();
		
		$user = new User();
		$user->setUseAuthProvider("google");
		$user->setUseAuthUid($gpUserProfile['id']);
		$user->setUseName(Funciones::just_clean($gpUserProfile['given_name']));
		$user->setUseMail($gpUserProfile['email']);
		if ($gpUserProfile['gender']=="male")
			$user->setUseSex("M");
		else $user->setUseSex("F");
// 		$user->setUsePicture($gpUserProfile['picture']);
// 		$user->setUseLink($gpUserProfile['link']);
		
		$user->checkUserGoogle($conMsi, $pageCode);
		
		$_SESSION["sesIduser"]=$user->getUseIduser();
		$_SESSION["sesType"]="1";
		$_SESSION["sesName"]=$user->getUseName()." ".$user->getUseLastname();
		$_SESSION["sesMail"] = $user->getUseMail();
		
		//Render google profile data
		if($_SESSION["sesIduser"]!=""){
			header("Location: /my-profile");
		}else{
			$output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
		}
	} else {
		$authUrl = $gClient->createAuthUrl();
		header("Location: ".filter_var($authUrl, FILTER_SANITIZE_URL));
		die();
	}
	die();
?>