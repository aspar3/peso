<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
session_start();
$pageCode = "NPE";

include("admin/in_variables.php");
include("in_www.php");
require_once("in_func_mail.php");

include("in_idiom.php");

include_once 'classes/Funciones.php';
include_once 'classes/Peso.php';
include_once 'classes/GrupoUser.php';

if (!isset($_SESSION["sesIduser"]) || $_SESSION["sesIduser"]=="" || $_SESSION["sesType"]!=1){
	//rolLog("$pageCode-01", "No session started or not a signedup user -> (".$_SESSION["sesIduser"].")", 1);
	header("Location: /login?new=yes");
	die();
}

$idiomaTxt = $_SESSION["sesIdmLocale"];
if ($idiomaTxt == "") {
	$idiomaTxt = "es";
}
include_once 'literales/idioma_'.$idiomaTxt.'.php';

$conMsi= crearConexionMysqli();

$peso = new Peso();

$editar = false; 
$idPeso = $_GET["idPeso"];
if ($idPeso != "") {
	$editar = true;
	$peso->setPesIduser($_SESSION["sesIduser"]);
	$peso->setPesIdpeso($idPeso);
	$peso->getPeso($conMsi, $pageCode);
}

$accion = $_POST["accion"];
if ($accion == "save"){
	$peso->setPesIduser($_SESSION["sesIduser"]);
	
	if (!$editar) {
		$peso->setPesFecha($_POST["fecha"]);
		$peso->setPesComent($_POST["coment"]);
		$peso->setPesPeso($_POST["peso"] * 1000 / $_SESSION["sesUniMultipli"]);
		
		if ($peso->insert($conMsi, $pageCode)){
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIduser($_SESSION["sesIduser"]);
			$grupoUser->setGusAvisoRetraso("N");
			if ($grupoUser->updateAvisoRetrasoUser($conMsi, $pageCode)) {
				$mensaje1=sprintf(litCambiosOk);
				$classMsgBox = "msgBox bgGreen txtBlack";
				if ($enviarMails) { 
					enviarMailAlert($mailAdmin, $mailAlertasAdmin, "", $nombreGeneral." : ".$_SESSION["sesName"]." ha metido un nuevo peso", "Nuevo peso");
				}
				header("Location: /mis-pesos");
				die();
			} else {
				$mensaje1=sprintf(litError1);
				$mensaje2=sprintf(litError2, $mailAdmin);
				$classMsgBox = "msgBox bgRed txtWhite";
			}
		}else{
			$mensaje1=sprintf(litError1);
			$mensaje2=sprintf(litError2, $mailAdmin);
			$classMsgBox = "msgBox bgRed txtWhite";
		}
	} else {
		$peso->setPesIdpeso($idPeso);
		$peso->setPesComent($_POST["coment"]);
		$peso->setPesPeso($_POST["peso"] * 1000 / $_SESSION["sesUniMultipli"]);
		
		if ($peso->update($conMsi, $pageCode)){
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIduser($_SESSION["sesIduser"]);
			$grupoUser->setGusAvisoRetraso("N");
			if ($grupoUser->updateAvisoRetrasoUser($conMsi, $pageCode)) {
				$mensaje1=sprintf(litCambiosOk);
				$classMsgBox = "msgBox bgGreen txtBlack";
				header("Location: /mis-pesos");
				die();
			} else {
				$mensaje1=sprintf(litError1);
				$mensaje2=sprintf(litError2, $mailAdmin);
				$classMsgBox = "msgBox bgRed txtWhite";
			}
		}else{
			$mensaje1=sprintf(litError1);
			$mensaje2=sprintf(litError2, $mailAdmin);
			$classMsgBox = "msgBox bgRed txtWhite";
		}
	}
}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$nombreGeneral." - ".sprintf(litNuevoPeso)?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litNuevoPeso)?>">
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
		<script src="/js/sign.js"></script>
		<script type="text/javascript">
			function saveData(formulario){
				if (formulario.fecha.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litFecha))?>");
					formulario.fecha.focus();
				} else if (formulario.peso.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litPeso))?>");
					formulario.peso.focus();
				} else formulario.submit();
			}
		</script>
	</head>
	<body class="homepage is-preload">
		<div id="page-wrapper">

			<!-- Header -->
				<section id="header">
					<div class="container">
						<!-- Logo -->
						<h1 id="logo"><a href="index.php"><img src="/images/logo.png" alt="<?=$nombreGeneral?>"></a></h1>

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

								<form name="formulario" method="post">
									<input type="hidden" name="accion" value="save"/>
									<?php
										if ($accion == "save"){
											echo "<div class='$classMsgBox'><span>$mensaje1</span><br/>$mensaje2</div><br/>";
											if ($mensaje2_1 != "") echo "<div class='$classMsgBox2'><span>$mensaje2_1</span><br/>$mensaje2_2</div><br/>";
											echo "<br/>";
										}
									?>
									<header>
										<h2><?=sprintf(litIntroNuevoPeso)?></h2>
									</header>
									  
									<div>
										<label class="desc" for="fecha"><?=sprintf(litFecha)?> <span class="txtRed">*</span></label>
										<div>
											<input id="fecha" name="fecha" type="date" value="<?=($editar?Funciones::fechaFormateadaInput($peso->getPesFecha()):date('Y-m-d'))?>" <?=($editar?" disabled ":"")?>>
										</div>
									</div>
									<div>
										<label class="desc" for="peso"><?=sprintf(litPeso)?> (<?=$_SESSION["sesUniAbreviatura"]?>)<span class="txtRed">*</span></label>
										<div>
											<input id="peso" name="peso" type="number" maxlength="8" value="<?=Funciones::pesoConvertidoParaInput($peso->getPesPeso(), $_SESSION["sesIdunidad"], $_SESSION["sesUniMultipli"])?>">
										</div>
									</div>
									<div>
										<label class="desc" for="coment"><?=sprintf(litComentario)?></label>
										<div>
											<input id="coment" name="coment" type="text" maxlength="100" value="<?=$peso->getPesComent()?>">
										</div>
									</div>
									<div>
										<div>
									  		<br><input class="button" id="saveForm" name="saveForm" type="submit" onclick="saveData(this.form);return false;" value="<?=sprintf(litEnviarDatos)?>">
									  		<?php if ($editar) {?>
									  			&nbsp;<input class="button" id="volver" name="volver" type="button" onclick="history.back();" value="<?=sprintf(litVolver)?>">
									  		<?php }?>
									    </div>
									</div>
									  
								</form>
								
								
									</section>
								</div>
							</div>
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
	mysqli_close($conMsi);
	die();
?>