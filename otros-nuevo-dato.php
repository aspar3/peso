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
include_once 'classes/Grupo.php';
include_once 'classes/GrupoUser.php';
include_once 'classes/GrupoUserDato.php';

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

// 1: Peso. 2: Otros
$gruTipo = "2";

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
	
	if (!$editar) {
		$grupoUserDato->setGudFecha($_POST["fecha"]." ".$_POST["hora"]);
		$grupoUserDato->setGudComent($_POST["coment"]);
		$grupoUserDato->setGudDato($_POST["dato"]);
		
		if ($grupoUserDato->insert($conMsi, $pageCode)){
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIduser($_SESSION["sesIduser"]);
			$grupoUser->setGusAvisoRetraso("N");
			if ($grupoUser->updateAvisoRetrasoUser($conMsi, $pageCode)) {
				$mensaje1=sprintf(litCambiosOk);
				$classMsgBox = "msgBox bgGreen txtBlack";
				if ($enviarMails) { 
					enviarMailAlert($mailAdmin, $mailAlertasAdmin, "", $nombreGeneral." : ".$_SESSION["sesName"]." ha metido un nuevo peso", "Nuevo peso");
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
		$grupoUserDato->setGudComent($_POST["coment"]);
		$grupoUserDato->setGudDato($_POST["dato"]);

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
		<title><?=$nombreGeneral." - ".sprintf(litNuevoDato)?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litNuevoDato)?>">
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
		<script type="text/javascript">
			function saveData(formulario){
				if (formulario.fecha.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litFecha))?>");
					formulario.fecha.focus();
				} else if (formulario.dato.value==""){
					alert("<?=$grupo->getGruPregunta()?>");
					formulario.dato.focus();
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
										<h2><?=sprintf(litIntroNuevoDato)?></h2>
									</header>

									<div>
										<label class="desc" for="idgrupo"><?=($idGrupo==""?sprintf(litPrimeroGrupo):sprintf(litGrupo))?> <span class="txtRed">*</span></label>
										<div>
											<select id="idgrupo" name="idgrupo" onchange="window.location.href='/otros-nuevo-dato.php?idGrupo=' + this.value">
												<option value=""></option>
												<?php
													$grupoSelect = new Grupo();
													$grupoSelect->setGruTipo($gruTipo);
													$grupoSelect->setGruIduser($_SESSION["sesIduser"]);
													foreach ($listGruposAceptados as $objGrupo) {
														echo "<option ".($objGrupo->getGruIdgrupo() == $idGrupo?"selected":"")." value = '".$objGrupo->getGruIdgrupo()."'>".$objGrupo->getGruNombre()."</option>";
													}
												?>
											</select>
										</div>
									</div>
									<?php if ($idGrupo != "") {?>
											<div>
												<label class="desc" for="fecha"><?=sprintf(litFecha)?> <span class="txtRed">*</span></label>
												<div>
													<input id="fecha" class="mitad" name="fecha" type="date" value="<?=($editar?Funciones::fechaFormateadaInput($grupoUserDato->getGudFecha()):"")?>" <?=($editar?" disabled ":"")?>>
													<input id="hora" class="mitad" name="hora" type="time" value="<?=($editar?Funciones::horaFormateadaInput($grupoUserDato->getGudFecha()):"")?>" <?=($editar?" disabled ":"")?>>
												</div>
											</div>
											<div>
												<label class="desc" for="dato"><?=$grupo->getGruPregunta()?> <span class="txtRed">*</span></label>
												<div>
													<?php if ($grupo->getGruIdrespuesta() == 1) { ?>
															<input id="dato" name="dato" type="number" maxlength="8" value="<?=$grupoUserDato->getGudDato()?>">
													<?php } else if ($grupo->getGruIdrespuesta() == 2) { ?>
															<select id="dato" name="dato">
																<option value="1"><?=sprintf(litSi)?></option>
															</select>
													<?php } else if ($grupo->getGruIdrespuesta() == 3) { ?>
															<select id="dato" name="dato">
																<option value="1"><?=sprintf(litNo)?></option>
															</select>
													<?php } ?>
												</div>
											</div>
											<div>
												<label class="desc" for="coment"><?=sprintf(litComentario)?></label>
												<div>
													<input id="coment" name="coment" type="text" maxlength="100" value="<?=$grupoUserDato->getGudComent()?>">
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
									<?php }?>
									  
								</form>
								
								
									</section>
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