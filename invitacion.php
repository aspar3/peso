<?php
	session_start();
	$pageCode = "INV";

	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	
	include("admin/in_variables.php");

	require_once("in_func_mail.php");
	
	include("in_idiom.php");

	include_once 'classes/User.php';
	include_once 'classes/Grupo.php';
	include_once 'classes/GrupoUser.php';
	include_once 'classes/Idioma.php';
		
	if ($_GET["invitacion"]=="ok"){
		$conMsi= crearConexionMysqli();
		
		$userInvitado = new User();
		$userInvitado->setUseIduser($_GET["idUser"]);
		if (!$userInvitado->setUserWithIdNoStatus($conMsi, $pageCode)) {
			header("Location: /not-found");
			die;
		}
		$idioma = new Idioma();
		$idioma->setIdmIdidioma($userInvitado->getUseIdidioma());
		$idioma->getIdioma($conMsi, $pageCode);
		$idiomaTxt = $idioma->getIdmLocale();
		if ($idiomaTxt == "") {
			$idiomaTxt = "es";
		}
		include_once 'literales/idioma_'.$idiomaTxt.'.php';
		
		$grupoUser = new GrupoUser();
		$grupoUser->setGusIdgrupo($_GET["idGrupo"]);
		$grupoUser->setGusIduser($_GET["idUser"]);
		$grupoUser->setGusVerifyCode($_GET["verifyCode"]);
		if (!$grupoUser->aceptarInvitacion($conMsi, $pageCode)) {
			$mensaje1=sprintf(litError1);
			$mensaje2=sprintf(litError2, $mailAdmin);
			$classMsgBox = "msgBox bgRed txtWhite";
		} else {

			$grupoUser->getGrupoUser($conMsi, $pageCode);
			$userInvitador = new User();
			$userInvitador->setUseIduser($grupoUser->getGusUsucre());
			$userInvitador->setUserWithIdNoStatus($conMsi, $pageCode);
			
			$grupo = new Grupo();
			$grupo->setGruIdgrupo($grupoUser->getGusIdgrupo());
			$grupo->setGruIduser($grupoUser->getGusIduser());
			$grupo->getGrupo($conMsi, $pageCode);
			
			$bodyUser = sprintf(litEstimado, $userInvitador->getUseName())."\n\n".
					sprintf(litAceptInvitacion, $userInvitado->getUseName(), $grupo->getGruNombre())."\n\n".
					sprintf(litAtentamente)."\n".
					$nombreGeneral.": ".$accesoHttp.$rootURL;
			$subjectUser = $nombreGeneral.": ".sprintf(litInvitacionSubject, $userInvitado->getUseName());
			
			if ($enviarMails) enviarMailSMTP($mailAdmin, $userInvitador->getUseMail(), "", "", $subjectUser, $bodyUser, $userInvitador->getUseIduser());
					
			$user = new User();
			$user->setUseIduser($_GET["idUser"]);
			if ($user->checkUserPendiente($conMsi, $pageCode)) {
				mysqli_close($conMsi);
				header("Location: /sign-up?invitacion=ok&idUser=".$_GET["idUser"]);
				die();
			} else {
				mysqli_close($conMsi);
				header("Location: /login?invitacion=ok&idUser=".$_GET["idUser"]);
				die();
			}
		}
	} else if ($_GET["invitacion"]=="ko"){
		$conMsi= crearConexionMysqli();
		
		$userInvitado = new User();
		$userInvitado->setUseIduser($_GET["idUser"]);
		$userInvitado->setUserWithIdNoStatus($conMsi, $pageCode);
		$idioma = new Idioma();
		$idioma->setIdmIdidioma($userInvitado->getUseIdidioma());
		$idioma->getIdioma($conMsi, $pageCode);
		$idiomaTxt = $idioma->getIdmLocale();
		if ($idiomaTxt == "") {
			$idiomaTxt = "es";
		}
		include_once 'literales/idioma_'.$idiomaTxt.'.php';
		
		$grupoUser = new GrupoUser();
		$grupoUser->setGusIdgrupo($_GET["idGrupo"]);
		$grupoUser->setGusIduser($_GET["idUser"]);
		$grupoUser->setGusVerifyCode($_GET["verifyCode"]);
		if ($grupoUser->rechazarInvitacion($conMsi, $pageCode)) {
			$mensaje1=sprintf(litInvitacionRechazada);
			$classMsgBox = "msgBox bgGreen txtBlack";
			
			$grupoUser->getGrupoUser($conMsi, $pageCode);
			$userInvitador = new User();
			$userInvitador->setUseIduser($grupoUser->getGusUsucre());
			$userInvitador->setUserWithIdNoStatus($conMsi, $pageCode);
			
			$grupo = new Grupo();
			$grupo->setGruIdgrupo($grupoUser->getGusIdgrupo());
			$grupo->getGrupo($conMsi, $pageCode);
			
			$bodyUser = sprintf(litEstimado, $userInvitador->getUseName())."\n\n".
					sprintf(litRechazadoInvitacion, $userInvitado->getUseName(), $grupo->getGruNombre())."\n\n".
					sprintf(litAtentamente)."\n".
					$nombreGeneral.": ".$accesoHttp.$rootURL;
			$subjectUser = $nombreGeneral.": ".sprintf(litInvitacionSubject, $_SESSION["sesName"]);
					
			if ($enviarMails) enviarMailSMTP($mailAdmin, $userInvitador->getUseMail(), "", "", $subjectUser, $bodyUser, $userInvitador->getUseIduser());
					
		} else {
			$mensaje1=sprintf(litError1);
			$mensaje2=sprintf(litError2, $mailAdmin);
			$classMsgBox = "msgBox bgRed txtWhite";
		}
		mysqli_close($conMsi);
	}
?>
<!DOCTYPE HTML>
<html lang="es" translate="no">
	<head>
		<title><?=$nombreGeneral?> - <?=litRechazarInvitacionTitle?></title>
		<meta name="title" content="<?=$nombreGeneral?> - <?=litRechazarInvitacionTitle?>">
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
								</form>

						</div>
					</div>
					<br>
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