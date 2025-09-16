<?php
	session_start();
	$pageCode = "LOG";

// 	error_reporting(E_ALL);
// 	ini_set("display_errors", 1);
	
	include("admin/in_variables.php");

	require_once("in_func_mail.php");
	
	include("in_idiom.php");

	include_once 'classes/User.php';
	include_once 'classes/Unidad.php';
	include_once 'classes/Idioma.php';
	
	$idiomaTxt = $_GET["idioma"];
	if ($idiomaTxt == "") {
		$idiomaTxt = "es";
	}
	include_once 'literales/idioma_'.$idiomaTxt.'.php';
	
	if (isset($_SESSION["sesIduser"]) && $_SESSION["sesIduser"] != ""){
		if ($_GET["invitacion"] == "ok") {
			header("Location: mis-grupos");
		} else {
			header("Location: nuevo-peso");
		}
		die();
	}
		
	$mail= $password = "";

	if ($_POST["accion"]=="save"){		
		$conMsi= crearConexionMysqli();
		$user = new User();
		// se hace el post dos veces. Los 2 primeros son por si no va bien, para escribirlos de nuevo en la pantalla
		$mail = $_POST["mail"];
		$password = $_POST["password"];
		$user->setUseMail($_POST["mail"]);
		$user->setUsePassword($_POST["password"]);
		if ($user->getUseMail()!="" && $user->getUsePassword()!=""){
			
			if ($user->validateUser($conMsi, $pageCode)){
				// se crea la sesion y se redirecciona a la url para modificar los datos
				session_start();
				$_SESSION["sesIduser"] = $user->getUseIduser();
				$_SESSION["sesIdidioma"] = $user->getUseIdidioma();
				$_SESSION["sesIdunidad"] = $user->getUseIdunidad();
				$_SESSION["sesType"] = $user->getUseIdstatus();
				$_SESSION["sesName"] = $user->getUseName();
				$_SESSION["sesLastname"] = $user->getUseLastname();
				$_SESSION["sesMail"] = $user->getUseMail();

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
				
				if ($_POST["remember"]=="1"){
					$number_of_days = 365 ;
					$date_of_expiry = time() + 60 * 60 * 24 * $number_of_days ;
					setcookie( "cookie[1]", $mail, $date_of_expiry, "/" ) ;
					setcookie( "cookie[2]", $password, $date_of_expiry, "/" ) ;
				}else{
					unset($_COOKIE['cookie'][1]);
					setcookie("cookie[1]", "", time() - 3600, "/");
					unset($_COOKIE['cookie'][2]);
					setcookie("cookie[2]", "", time() - 3600, "/");
					unset($_COOKIE["cookie"]);
					setcookie("cookie", "", time() - 3600, "/");
				}
				
				header("Location: /nuevo-peso");
				die();
			}
		}
		mysqli_close($conMsi);
		$mensaje1=sprintf(litInicioSesionKo01);
		$mensaje2=sprintf(litInicioSesionKo02);
		$classMsgBox = "msgBox bgRed txtWhite";
	} else 	if ($_GET["verify"]=="S"){
		$idUser= $codValida = "";
		
		$idUser= $_GET["idUser"];
		$codValida = $_GET["codValida"];
		$result = false;
		
		if (is_numeric($idUser) && $codValida != ""){
			$conMsi = crearConexionMysqli();
			// TODO: primero validar que haya un registro sin verificar y con esos datos
			$user = new User();
			$user->setUseIduser($idUser);
			$user->setUseVerifyCode($codValida);
			if ($user->updateVerifyUser($conMsi, $pageCode)){
				$user->setUserWithId($conMsi, $pageCode);
				$result = true;
				$mensaje1=sprintf(litEnhorabuena);
				$mensaje2=sprintf(litVerifMailOk);
				$mensaje2.="<br><a href='".$accesoHttp.$rootURL."/login'>".sprintf(litMenuIniciarSesion)."</a>";
				$classMsgBox = "msgBox bgGreen txtBlack";
			}else{
				$mensaje1=sprintf(litEnlaceKo01);
				$mensaje2=sprintf(litEnlaceKo02, $mailAdmin);
				$classMsgBox = "msgBox bgRed txtWhite";
			}
			mysqli_close($conMsi);
		}
	}else if ($_GET["new"]=="yes"){
		$mensaje1=sprintf(litInicioSesion01);
		$mensaje2=sprintf(litInicioSesion02);
		$classMsgBox = "msgBox bgRed txtWhite";
	}else if ($_GET["guest"]=="false"){
		$mensaje1=sprintf(litEnlaceKo01);
		$mensaje2=sprintf(litEnlaceKo02, $mailAdmin);
		$classMsgBox = "msgBox bgRed txtWhite";
	}else if ($_GET["open"]=="false"){
		$mensaje1=sprintf(litEnlaceCaducado01);
		$mensaje2=sprintf(litEnlaceCaducado02);
		$classMsgBox = "msgBox bgRed txtWhite";
	}
	
	$mail= $password = "";
	
	if ($_GET["invitacion"] == "ok") {
		$conMsi= crearConexionMysqli();
		$user = new User();
		$user->setUseIduser($_GET["idUser"]);
		$user->setUserWithId($conMsi, $pageCode);
		$mail = $user->getUseMail();
		mysqli_close($conMsi);
	}	
?>
<!DOCTYPE HTML>
<html lang="es" translate="no">
	<head>
		<title><?=$nombreGeneral?> - <?=sprintf(litLogin)?></title>
		<meta name="title" content="<?=$nombreGeneral?> - <?=sprintf(litLogin)?>">
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
	</head>
	<body class="homepage is-preload">
		<div id="page-wrapper">

			<!-- Header -->
				<section id="header">
					<div class="container sinLogo">

						<?php include("in-menu.php");?>

					</div>
				</section>
			
			<!-- Main -->
				<section id="main">
					<div class="container sinLogo">
						<div id="content">
								
								<form name="formulario" method="post">
									<input type="hidden" name="accion" value="save"/>
									<?php
											if ($mensaje1!="")
												echo "<div class='$classMsgBox'><span>$mensaje1</span><br/>$mensaje2</div><br/><br/>";
									?>
									<header>
										<h2><?=litIntroDatosAcceso?></h2>
									</header>
									<div>
										<label class="desc" id="title3" for="mail"><?=sprintf(litMail)?> <span class="txtRed">*</span></label>
										<div>
											<input id="mail" name="mail" type="email" spellcheck="false" value="<?=($mail==""?$_COOKIE["cookie"][1]:$mail)?>" maxlength="255"> 
										</div>
									</div>
									<div>
									    <label class="desc" id="title3" for="password"><?=sprintf(litPassword)?> <span class="txtRed">*</span></label>
									    <div>
											<input id="password" name="password" type="password" spellcheck="false" value="<?=($password==""?$_COOKIE["cookie"][2]:$password)?>" maxlength="255"> 
										</div>
									</div>
									<div>
									    <div>
											<label class="desc" id="title3" for="remember"><input id="remember" name="remember" type="checkbox" value="1" <?=($_COOKIE["cookie"][1]!=""?'checked="checked"':'')?>> <?=sprintf(litRecordarme)?></label> 
										</div>
									</div>
									<div>
										<div>
									  		<input class="button" id="saveForm" name="saveForm" type="submit" value="<?=sprintf(litEntrar)?>">
										</div>
									</div>
									<div>
									    <label class="desc" id="title3"></label>
									    <div>
											<a href="/login-reminder"><?=sprintf(litOlvidoPass)?></a>
										</div>
									</div>
									<div>
									    <label class="desc" id="title3"></label>
									    <div>
											<a href="/sign-up"><?=sprintf(litRegistrarseNuevo)?></a>
										</div>
									</div>
								</form>

						</div>
					</div>
				</section>
		</div>

		<!-- Scripts -->
			<script src="/assets/js/jquery.min.js"></script>
			<script src="/assets/js/jquery.dropotron.min.js"></script>
			<script src="/assets/js/browser.min.js"></script>
			<script src="/assets/js/breakpoints.min.js"></script>
			<script src="/assets/js/util.js"></script>
			<script src="/assets/js/main.js"></script>
		
		<?php include("in-footer.php");?>
		
	</body>
</html>							

<?php
	die();
?>