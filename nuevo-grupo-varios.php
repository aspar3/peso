<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
session_start();
$pageCode = "NGR";

include("admin/in_variables.php");
include("in_www.php");
require_once("in_func_mail.php");

include("in_idiom.php");

include_once 'classes/Funciones.php';
include_once 'classes/Grupo.php';
include_once 'classes/GrupoUser.php';
include_once 'classes/Tiempo.php';

if (!isset($_SESSION["sesIduser"]) || $_SESSION["sesIduser"]=="" || $_SESSION["sesStatus"]!=1){
	//rolLog("$pageCode-01", "No session started or not a signedup user -> (".$_SESSION["sesIduser"].")", 1);
	header("Location: /login?new=yes&goUrl=".ltrim($_SERVER['REQUEST_URI'], '/'));
	die();
}

$idiomaTxt = $_SESSION["sesIdmLocale"];
if ($idiomaTxt == "") {
	$idiomaTxt = "es";
}
include_once 'literales/idioma_'.$idiomaTxt.'.php';

$conMsi= crearConexionMysqli();

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$nombreGeneral." - ".sprintf(litMenuNuevoRetoOtros)?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litMenuNuevoRetoOtros)?>">
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 9999999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 9999999)?>" />
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
								
							<div class="row main-row">
								<div class="12u">
									<section>
										<span><?php echo sprintf(litNewGrupoVarios01)?></span>
										<br><br>
										<hr>
										<header>
											<h2><?php echo sprintf(litRetosAccionesTareas)?></h2>
											<span>
													<?php echo sprintf(litRetosAccionesTareas02)?><br>
													<?php echo sprintf(litAyudaNewAccion02)?>
											</span>
										</header>
										<br>
										<input class="button" id="saveForm" name="saveForm" type="submit" onclick="window.location.href='/acciones-nuevo-grupo.php'" value="<?=sprintf(litMenuNuevoRetoAcciones)?>">&nbsp;
										<br><br>
										<hr>
										<br>
										<header>
											<h2><?php echo sprintf(litOtrosRetos)?></h2>
											<p><?php echo sprintf(litOtrosRetos02)?></p>
										</header>
										<input class="button" id="saveForm" name="saveForm" type="submit" onclick="window.location.href='/otros-nuevo-grupo.php'" value="<?=sprintf(litMenuNuevoRetoOtros)?>">&nbsp;
										<br><br>
										<hr>
										<br>
										<input class="button" id="volver" name="volver" type="button" onclick="history.back();" value="<?=sprintf(litVolver)?>">
									</section>
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