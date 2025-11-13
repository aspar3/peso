<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
session_start();
$pageCode = "STP";
echo "hola";
include("admin/in_variables.php");
include("in_www.php");
require_once("in_func_mail.php");

include("in_idiom.php");

include_once 'classes/Funciones.php';
include_once 'classes/User.php';
include_once 'classes/GrupoUser.php';

$idiomaTxt = "es";
include_once 'literales/idioma_'.$idiomaTxt.'.php';

$conMsi= crearConexionMysqli();

$idGrupo = $_GET["idGrupo"];
$idUser = $_GET["idUser"];
$code = $_GET["code"];

$mensaje="";
if ($idGrupo == "" && $idUser != "" && $code != "") {
	$user = new User();
	$user->setUseIduser($idUser);
	$user->setUseAvisosCode($code);
	if ($user->desactivarNotifAll($conMsi, $pageCode)) {
		$mensaje = sprintf(litNotifNoAll);
	} else {
		$mensaje = sprintf(litError1)."br".sprintf(litError2);
	}
} else if ($idUser != "" && $code != "") {
	$grupoUser = new GrupoUser();
	$grupoUser->setGusIdgrupo($idGrupo);
	$grupoUser->setGusIduser($idUser);
	$grupoUser->setGusAvisosCode($code);
	if ($grupoUser->desactivarNotifOne($conMsi, $pageCode)) {
		$mensaje = sprintf(litNotifNo);
	} else {
		$mensaje = sprintf(litError1)."br".sprintf(litError2);
	}
} else {
	mysqli_close($conMsi);
	die;
}
?>
<!DOCTYPE HTML>
<html lang="es" translate="no">
	<head>
		<title><?=$nombreGeneral?> <?=sprintf(litRecibirNotificaciones)?></title>
		<meta name="title" content="<?=$nombreGeneral?> <?=sprintf(litRecibirNotificaciones)?>">
		<meta name="verify-v1" content="iktchguQVSJTd8nwo6NGXdZ0nuE1URIv9bJN/OODK8E=" />
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?878" />
		<link rel="stylesheet" href="/css/extra.css" />
		<script src="/js/index.js"></script>
	</head>
	<body class="homepage is-preload">
		<div id="page-wrapper">

			<!-- Header -->
				<section id="header">
					<div class="container">

						<!-- Logo -->
							<h1 id="logo"><a href="index.php"><img src="/images/logo.png" alt="<?=$nombreGeneral?>"></a></h1>
							<p><?=litFormaVida?></p>

						<?php include("in-menu.php");?>

					</div>
				</section>
			
			<!-- Main -->
				<section id="main">
					<div class="container">
						<div id="content">
							<!-- Post -->
								<article class="box post">
									<header>
										<h2><?=sprintf(litBienvenido, $nombreGeneral)?></strong>!</h2>
									</header>
									<p><?=$mensaje?></p>

								</article>
								<div>
									<div>
								  		<br><input class="button" id="saveForm" name="saveForm" type="submit" onclick="window.location.href='/login<?=$idiomaURL?>'" value="<?=sprintf(litIniciarSesion)?>">
								    </div>
								</div>
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
	mysqli_close($conMsi);
	die();
?>