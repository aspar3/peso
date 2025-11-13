<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
	session_start();

	$pageCode = "LTG";
	
	//Include Google client library
	require_once 'vendor/autoload.php';
		
	include("admin/in_variables.php");
	
	include("in_idiom.php");
	
	include_once 'classes/User.php';
	include_once 'classes/Funciones.php';
	include_once 'classes/Unidad.php';
	include_once 'classes/Idioma.php';
	
	/*
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
		$_SESSION["sesStatus"]="1";
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
	*/
		
		$client = new Google_Client();
		$client->setClientId('806600875143-ji40vpge8tvu8knbthj8q98c3fooqf20.apps.googleusercontent.com');
		$client->setClientSecret('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');
		$client->setRedirectUri('https://challenges.group/login-third-google.php');
		
		if (isset($_GET['code'])) {
			$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
			$client->setAccessToken($token);
				
			$oauth2 = new Google_Service_Oauth2($client);
			$userInfo = $oauth2->userinfo->get();
// 			echo '<pre>'; print_r($userInfo); echo '</pre>';
// 			echo "Hola, " . $userInfo->givenName;
// 			echo "<br>Correo: " . $userInfo->email;
// 			echo "<br>".$userInfo->familyName; // Last name

			$conMsi= crearConexionMysqli();
			
			// Revisar si el usuario ya existe para saber si luego se redirige al profile (para que elija el idioma)
			// o al sitio desde el que venia
			$userCheck = new User();
			$userCheck->setUseMail($userInfo->email);
			$userYaExiste = $userCheck->checkUserMailAlreadyExistsSet($conMsi, $pageCode);
			
			$user = new User();
			$user->setUseAuthProvider("google");
			$user->setUseAuthUid($userInfo->id);
			$user->setUseName($userInfo->givenName);
			$user->setUseLastname($userInfo->familyName);
			$user->setUseMail($userInfo->email);
			$user->setUseIdidioma(1);
			$user->setUseIdunidad(1);
			$user->setUseIdstatus(1);
			$user->checkUserGoogle($conMsi, $pageCode);
			
			$_SESSION["sesIduser"] = $user->getUseIduser();
			$_SESSION["sesIdidioma"] = $user->getUseIdidioma();
			$_SESSION["sesIdunidad"] = $user->getUseIdunidad();
			$_SESSION["sesStatus"] = $user->getUseIdstatus();
			$_SESSION["sesName"] = $user->getUseName();
			$_SESSION["sesLastname"] = $user->getUseLastname();
			$_SESSION["sesMail"] = $user->getUseMail();
			$_SESSION["sesAvisosMail"] = $user->getUseAvisosMail();

			$unidad = new Unidad();
			$unidad->setUniIdunidad($user->getUseIdunidad());
			$unidad->getUnidad($conMsi, $pageCode);
			$_SESSION["sesUniAbreviatura"] = $unidad->getUniAbrev();
			$_SESSION["sesUniMultipli"] = $unidad->getUniMultipli();
			
			$idioma = new Idioma();
			$idioma->setIdmIdidioma($user->getUseIdidioma());
			$idioma->getIdioma($conMsi, $pageCode);
			$_SESSION["sesIdmLocale"] = $idioma->getIdmLocale();
			
			mysqli_close($conMsi);
			echo "URL: ".$_SESSION["goUrl"];
			//Render google profile data
			if($_SESSION["sesIduser"]!=""){
				if ($userYaExiste) {
					if ($_SESSION["goUrl"] != "") {
						header("Location: ".$_SESSION["goUrl"]);
					} else {
						header("Location: /nuevo-peso");
					}
				} else {
					header("Location: /my-profile");
					die;
				}
			}else{
				$output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
			}
		}
	die();
	
	function funUrlRedir() {
		if ($_GET["goUrl"] != "") {
			return "/".$_GET["goUrl"];
		} else {
			$conMsi= crearConexionMysqli();
			$grupo = new Grupo();
			$grupo->setGruIduser($_SESSION["sesIduser"]);
			//				$listGruposPeso = $grupo->getGruposByTipo($conMsi, $pageCode, "1");
			$listGruposOtros = $grupo->getGruposByTipo($conMsi, $pageCode, "2");
			
			mysqli_close($conMsi);
			
			if (count($listGruposOtros) > 0) {
				return "/otros-mis-grupos.php";
			} else {
				return "/mis-grupos";
			}
		}
	}
?>