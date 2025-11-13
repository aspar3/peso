<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
session_start();
$pageCode = "AND";

include("admin/in_variables.php");
include("in_www.php");
require_once("in_func_mail.php");

include("in_idiom.php");

include_once 'classes/Funciones.php';
include_once 'classes/Grupo.php';
include_once 'classes/GrupoUser.php';
include_once 'classes/GrupoUserDato.php';
include_once 'classes/GrupoAccion.php';

if (!isset($_SESSION["sesIduser"]) || $_SESSION["sesIduser"]=="" || $_SESSION["sesStatus"]!=1){
	//rolLog("$pageCode-01", "No session started or not a signedup user -> (".$_SESSION["sesIduser"].")", 1);
	header("Location: /login?new=yes&goUrl=".ltrim($_SERVER['REQUEST_URI'], '/'));
	die();
}

$idiomaURL = "";
$idiomaTxt = $_SESSION["sesIdmLocale"];
if ($idiomaTxt == "") {
	$idiomaTxt = "es";
} else {
	$idiomaURL = "/".$idiomaTxt;
}
include_once 'literales/idioma_'.$idiomaTxt.'.php';

// 1: Peso. 2: Otros 3: Acciones
$gruTipo = "3";

$conMsi= crearConexionMysqli();

$idGrupo = $_GET["idGrupo"];
$grupo = new Grupo();
if ($idGrupo != "") {
	$grupo->setGruIdgrupo($idGrupo);
	$grupo->setGruIduser($_SESSION["sesIduser"]);
	if (!$grupo->getGrupo($conMsi, $pageCode)) {
		die;
	}
}
if ($idGrupo == "") {
	die;
}

$grupoSelect = new Grupo();
$grupoSelect->setGruTipo($gruTipo);
$grupoSelect->setGruIduser($_SESSION["sesIduser"]);
$listGruposAceptados = $grupoSelect->getGruposAceptados($conMsi, $pageCode);
// Si solo esta en un grupo, que directamente salga seleccionado
if ($idGrupo == "" && count($listGruposAceptados) == 1) {
	$idGrupo = $listGruposAceptados[0]->getGruIdgrupo();
	$grupo->setGruIdgrupo($idGrupo);
	$grupo->setGruIduser($_SESSION["sesIduser"]);
	$grupo->getGrupo($conMsi, $pageCode);
}

$grupoUserDato = new GrupoUserDato();

$editar = false; 
$idGud = $_GET["idGud"];
if ($idGud != "") {
	$editar = true;
	$grupoUserDato->setGudIduser($_SESSION["sesIduser"]);
	$grupoUserDato->setGudIdgrupo($grupo->getGruIdgrupo());
	$grupoUserDato->setGudIdgud($idGud);
	$grupoUserDato->getGrupoUserDato($conMsi, $pageCode);
}

$accion = $_POST["accion"];
if ($accion == "save"){
	$grupoUserDato->setGudIdgrupo($grupo->getGruIdgrupo());
	$grupoUserDato->setGudIduser($_SESSION["sesIduser"]);
	$grupoUserDato->setGudComent($_POST["coment"]);
	
	$datos = [];
	foreach ($_POST as $clave => $valor) {
		if (strpos($clave, 'dato') === 0) {
			$datos[$clave] = $valor;
		}
	}
	if (count($datos) == 0) {
		die;
	}
	
	$grupoAccionCalculo = new GrupoAccion();
	$grupoAccionCalculo->setGacIdgrupo($grupo->getGruIdgrupo());
	$grupoUserDato->setGudDato($grupoAccionCalculo->calcularValor($conMsi, $pageCode, $datos));
	
	if (!$editar) {
		$grupoUserDato->setGudFecha($_POST["fecha"]." ".$_POST["hora"]);
		
		if ($grupoUserDato->insert($conMsi, $pageCode)){
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIduser($_SESSION["sesIduser"]);
			$grupoUser->setGusAvisoRetraso("N");
			if ($grupoUser->updateAvisoRetrasoUser($conMsi, $pageCode)) {
				$mensaje1=sprintf(litCambiosOk);
				$classMsgBox = "msgBox bgGreen txtBlack";
 				if ($enviarMails) { 
					//enviarMailAlert($mailAdmin, $mailAlertasAdmin, "", $nombreGeneral." : ".$_SESSION["sesName"]." -> Nuevo dato para ".$grupo->getGruNombre(), "Se ha metido un nuevo dato para ".$grupo->getGruNombre());
					$grupoUser = new GrupoUser();
 					$grupoUser->setGusIdgrupo($grupo->getGruIdgrupo());
 					$grupoUser->setGusIduser($_SESSION["sesIduser"]);
 					foreach ($grupoUser->getEnvioNotificacionesOtros($conMsi, $pageCode) as $objUser) {
 						$subjectUser = $nombreGeneral.": ".sprintf(litMailAvisoDatoSubject, $_SESSION["sesName"], $grupo->getGruNombre());
 						$bodyUser = sprintf(litEstimado, $objUser["useName"])."\n\n".
 	 						sprintf(litMailAvisoDatoBody01, $_SESSION["sesName"], $grupo->getGruNombre())."\n\n".
 	 						sprintf(litMailAvisoDatoBody02)."\n".
 	 						$accesoHttp.$rootURL."/otros-mis-grupos-estadisticas/".$grupo->getGruIdgrupo()."\n\n".
 	 						"\n\n".
 	 						"\n\n".
 	 						sprintf(litMailAvisoDatoBody03)."\n".
 	 						$accesoHttp.$rootURL."/stopNotif/".$grupo->getGruIdgrupo()."/".$objUser["useIduser"]."/".$objUser["gusAvisosCode"]."\n\n".
 	 						sprintf(litMailAvisoDatoBody04)."\n".
 	 						$accesoHttp.$rootURL."/stopNotifAll/".$objUser["useIduser"]."/".$objUser["useAvisosCode"]."\n\n".
 	 						sprintf(litAtentamente)."\n".
 	 						$nombreGeneral.": ".$accesoHttp.$rootURL.$idiomaURL;
 	 					enviarMailSMTP($mailAdmin, $objUser["useMail"], "", "", $subjectUser, $bodyUser, $objUser["useIduser"]);
 					} 
 				}
				header("Location: /otros-mis-datos.php?idGrupo=".$idGrupo);
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

		if ($grupoUserDato->update($conMsi, $pageCode)){
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIduser($_SESSION["sesIduser"]);
			$grupoUser->setGusAvisoRetraso("N");
			if ($grupoUser->updateAvisoRetrasoUser($conMsi, $pageCode)) {
				$mensaje1=sprintf(litCambiosOk);
				$classMsgBox = "msgBox bgGreen txtBlack";
				header("Location: /otros-mis-datos.php?idGrupo=".$idGrupo);
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
		<title><?=$nombreGeneral." - ".sprintf(litNuevoDatoGrupo, $grupo->getGruNombre())?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litNuevoDatoGrupo, $grupo->getGruNombre())?>">
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
		<script type="text/javascript">
			function saveData(formulario){
				const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="dato"], input[type="checkbox"][id^="dato"]');
				const algunoMarcado = Array.from(checkboxes).some(cb => cb.checked);
								
				if (formulario.fecha.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litFecha))?>");
					formulario.fecha.focus();
				} else if (!algunoMarcado){
					alert("<?=sprintf(litMarcaAccionesOblig)?>");
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
											echo "<br/>";
										}
									?>
									<header>
										<h2><?=sprintf(litNuevoDatoGrupo, $grupo->getGruNombre())?></h2>
									</header>
									<div>
										<label class="desc" for="fecha"><?=sprintf(litFecha)?> <span class="txtRed">*</span></label>
										<div>
											<input id="fecha" class="mitad" name="fecha" type="date" value="<?=($editar?Funciones::fechaFormateadaInput($grupoUserDato->getGudFecha()):"")?>" <?=($editar?" disabled ":"")?>>
											<input id="hora" class="mitad" name="hora" type="time" value="<?=($editar?Funciones::horaFormateadaInput($grupoUserDato->getGudFecha()):"")?>" <?=($editar?" disabled ":"")?>>
										</div>
									</div>
									<div>
										<label class="desc"><?=sprintf(litMarcaAcciones)?></label>
									</div>
									<?php
										$grupoAccion = new GrupoAccion();
										$grupoAccion->setGacIdgrupo($grupo->getGruIdgrupo());
										foreach ($grupoAccion->getGrupoAcciones($conMsi, $pageCode, $ascDesc) as $objAccion){
									?>
											<div class="checkbox">
												<div>
													<input type="checkbox" name="dato<?=$objAccion->getGacIdaccion()?>" id="dato<?=$objAccion->getGacIdaccion()?>" value="<?=$objAccion->getGacIdaccion()?>">
												</div>
												<label class="descCheck" for="dato<?=$objAccion->getGacIdaccion()?>"><?=$objAccion->getGacNombre()?></label>
											</div>
									<?php }?>
									<div>
										<label class="desc" for="coment"><?=sprintf(litComentario)?></label>
										<div>
											<input id="coment" name="coment" type="text" maxlength="100" value="<?=$grupoUserDato->getGudComent()?>">
										</div>
									</div>
									<div>
										<div>
									  		<br><input class="button" id="saveForm" name="saveForm" type="submit" onclick="saveData(this.form);return false;" value="<?=sprintf(litEnviarDatos)?>">
									  		&nbsp;<input class="button" id="volver" name="volver" type="button" onclick="window.location.href='/otros-mis-grupos.php';" value="<?=sprintf(litVolver)?>">
									    </div>
									</div>
									  
								</form>
								
								
									</section>
								</div>
						</div>
					</div>
					<br>
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
		<?php if ($idGrupo != "") {?>
			<script type="text/javascript">
				const now = new Date();
				const yyyy = now.getFullYear();
				const mm = String(now.getMonth() + 1).padStart(2, '0'); // Months start at 0
				const dd = String(now.getDate()).padStart(2, '0');
				const hh = String(now.getHours()).padStart(2, '0');
				const mi = String(now.getMinutes()).padStart(2, '0');
				
				const formattedDate = `${yyyy}-${mm}-${dd}`; // "YYYY-MM-DD"
				document.getElementById('fecha').value = formattedDate;
				const currentTime = `${hh}:${mi}`; // Format: "HH:MM"
				document.getElementById('hora').value = currentTime;
			</script>
		<?php } ?>

	</body>
</html>	

<?php
	mysqli_close($conMsi);
	die();
?>