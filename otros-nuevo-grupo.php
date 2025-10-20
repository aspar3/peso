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
include_once 'classes/Respuesta.php';
include_once 'classes/Tiempo.php';

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

// 1: Peso. 2: Otros
$gruTipo = "2";

$grupo = new Grupo();

$editar = false; 
$idGrupo = $_GET["idGrupo"];
if ($idGrupo != "") {
	$editar = true;
	$grupo->setGruIdgrupo($idGrupo);
	$grupo->setGruIduser($_SESSION["sesIduser"]);
	if (!$grupo->getGrupo($conMsi, $pageCode)) {
		die;
	}
}

$accion = $_POST["accion"];
if ($accion == "save"){
	$grupo->setGruIduser($_SESSION["sesIduser"]);
	$grupo->setGruNombre($_POST["nombre"]);
	$grupo->setGruFecini($_POST["fecini"]);
	$grupo->setGruFecfin($_POST["fecfin"]);
	$grupo->setGruMostrarPeso("S");
	$grupo->setGruPregunta($_POST["pregunta"]);
	$grupo->setGruIdrespuesta($_POST["idrespuesta"]);
	if ($grupo->getGruIdrespuesta() == 1) {
		$grupo->setGruGanador($_POST["ganador"]);
	} else {
		$grupo->setGruGanador("0");
	}
	$grupo->setGruIdtiempo($_POST["idtiempo"]);
	$grupo->setGruReto($_POST["reto"]);
	$grupo->setGruTipo($gruTipo);
	
	if (!$editar) {		
		if ($grupo->insertGrupo($conMsi, $pageCode)){
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIdgrupo($grupo->getGruIdgrupo());
			$grupoUser->setGusIduser($_SESSION["sesIduser"]);
			$grupoUser->setGusIdrol(1);
			$grupoUser->setGusUsucre($_SESSION["sesIduser"]);
			if ($grupoUser->insertGrupoUser($conMsi, $pageCode)) {
				$mensaje1=sprintf(litCambiosOk);
				$classMsgBox = "msgBox bgGreen txtBlack";
				
				header("Location: /otros-mis-grupos.php");
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
		$grupo->setGruIdgrupo($idGrupo);
		if ($grupo->updateGrupo($conMsi, $pageCode)){
			$mensaje1=sprintf(litCambiosOk);
			$classMsgBox = "msgBox bgGreen txtBlack";
			
			header("Location: /otros-mis-grupos.php");
			die();
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
		<title><?=$nombreGeneral." - ".sprintf(litNuevoGrupo)?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litNuevoGrupo)?>">
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
		<script type="text/javascript">
			function saveData(formulario){
				if (formulario.nombre.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litNombre))?>");
					formulario.nombre.focus();
				} else if (formulario.fecini.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litFechaInicio))?>");
					formulario.fecini.focus();
				} else if (formulario.idrespuesta.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litTipoRespuesta))?>");
					formulario.idrespuesta.focus();
				} else if (formulario.idtiempo.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litPeriodoPesajes))?>");
					formulario.idtiempo.focus();
				} else formulario.submit();
			}
			
			function visibilidadDivGanador(){
				if (document.getElementById("idrespuesta").value=='1') {
					document.getElementById("divGanador").style.display = "block";
				} else {
					document.getElementById("divGanador").style.display = "none";
				}
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
										<h2><?=sprintf(litCrearNuevoGrupoTitle)?></h2>
									</header>
									  
									<div>
										<label class="desc" for="nombre"><?=sprintf(litNombre)?> <span class="txtRed">*</span></label>
										<div>
											<input id="nombre" name="nombre" type="text" maxlength="100" value="<?=$grupo->getGruNombre()?>">
										</div>
									</div>
									<div>
										<label class="desc" for="fecini"><?=sprintf(litFechaInicio)?> <span class="txtRed">*</span></label>
										<div>
											<input id="fecini" name="fecini" type="date" value="<?=($editar?Funciones::fechaFormateadaInput($grupo->getGruFecini()):date('Y-m-d'))?>">
										</div>
									</div>
									<div>
										<label class="desc" for="fecfin"><?=sprintf(litFechaFin)?></label>
										<div>
											<input id="fecfin" name="fecfin" type="date" value="<?=($editar?Funciones::fechaFormateadaInput($grupo->getGruFecfin()):"")?>">
										</div>
									</div>
									<div>
										<label class="desc" for="pregunta"><?=sprintf(litPreguntaIntro)?></label>
										<div>
											<input id="pregunta" name="pregunta" type="text" maxlength="255" value="<?=$grupo->getGruPregunta()?>">
										</div>
									</div>
									<div>
										<label class="desc" for="idrespuesta"><?=sprintf(litTipoRespuesta)?> <span class="txtRed">*</span></label>
										<div>
											<select id="idrespuesta" name="idrespuesta" onchange="visibilidadDivGanador()">
												<?php
												$respuesta = new Respuesta();
												foreach ($respuesta->getRespuestas($conMsi, $pageCode) as $objRespuesta) {
													echo "<option ".($objRespuesta->getResIdrespuesta() == $grupo->getGruIdrespuesta()?"selected":"")." value = '".$objRespuesta->getResIdrespuesta()."'>".$objRespuesta->getResNombre()."</option>";
												}
												?>
											</select>
										</div>
									</div>
									<div id="divGanador">
										<label class="desc" for="ganador"><?=sprintf(litQuienGana)?> <span class="txtRed">*</span></label>
										<div>
											<select id="ganador" name="ganador">
												<option value="0" <?=($grupo->getGruGanador()=="0"?"selected":"")?>><?=sprintf(litQuienGanaMas)?></option>
												<option value="1" <?=($grupo->getGruGanador()=="1"?"selected":"")?>><?=sprintf(litQuienGanaMenos)?></option>
												<option value="2" <?=($grupo->getGruGanador()=="2"?"selected":"")?>><?=sprintf(litQuienGanaMediaMas)?></option>
												<option value="3" <?=($grupo->getGruGanador()=="3"?"selected":"")?>><?=sprintf(litQuienGanaMediaMenos)?></option>
											</select>
										</div>
									</div>

									<div>
										<label class="desc" for="idtiempo"><?=sprintf(litPeriodoDatos)?> <span class="txtRed">*</span></label>
										<div>
											<select id="idtiempo" name="idtiempo">
												<?php
												$tiempo = new Tiempo();
												foreach ($tiempo->getTiempos($conMsi, $pageCode) as $objTiempo) {
													echo "<option ".($objTiempo->getTieIdtiempo() == $grupo->getGruIdtiempo()?"selected":"")." value = '".$objTiempo->getTieIdtiempo()."'>".constant($objTiempo->getTieNombre())."</option>";
												}
												?>
											</select>
										</div>
									</div>
									<div>
										<label class="desc" for="reto"><?=sprintf(litDescriReto)?></label>
										<div>
											<input id="reto" name="reto" type="text" maxlength="255" value="<?=$grupo->getGruReto()?>">
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
			<script type="text/javascript">visibilidadDivGanador()</script>
		
		<?php include("in-footer.php");?>
		
	</body>
</html>	

<?php
	mysqli_close($conMsi);
	die();
?>