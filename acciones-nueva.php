<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
session_start();
$pageCode = "AGE";

include("admin/in_variables.php");
include("in_www.php");
require_once("in_func_mail.php");

include("in_idiom.php");

include_once 'classes/Funciones.php';
include_once 'classes/Grupo.php';
include_once 'classes/GrupoUser.php';
include_once 'classes/GrupoAccion.php';

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

// 1: Peso. 2: Otros 3: Acciones
$gruTipo = "3";

$conMsi= crearConexionMysqli();

$idGrupo = $_GET["idGrupo"];
$grupo = new Grupo();
if ($idGrupo == "") {
	die;
}
$grupo->setGruIdgrupo($idGrupo);
$grupo->setGruIduser($_SESSION["sesIduser"]);
if (!$grupo->getGrupo($conMsi, $pageCode)) {
	die;
}
$grupoUser = new GrupoUser();
$grupoUser->setGusIdgrupo($grupo->getGruIdgrupo());
$grupoUser->setGusIduser($_SESSION["sesIduser"]);
$grupoUser->getGrupoUser($conMsi, $pageCode);
if ($grupoUser->getGusIdrol() == "1") {
	$esAdmin = true;
} else {
	die;
}

$ayudaInicial = true;
$grupoAccionCount = new GrupoAccion();
$grupoAccionCount->setGacIdgrupo($grupo->getGruIdgrupo());
if (count($grupoAccionCount->getGrupoAcciones($conMsi, $pageCode, $ascDesc)) > 0) {
	$ayudaInicial = false;
}

$grupoAccion = new GrupoAccion();
$editar = false; 
$idGac = $_GET["idGac"];
if ($idGac != "") {
	$editar = true;
	$grupoAccion->setGacIdaccion($idGac);
	$grupoAccion->setGacIdgrupo($grupo->getGruIdgrupo());
	$grupoAccion->getGrupoAccion($conMsi, $pageCode);
}

$accion = $_POST["accion"];
if ($accion == "save"){
	$grupoAccion->setGacIdaccion($_POST["idGac"]);
	$grupoAccion->setGacIdgrupo($grupo->getGruIdgrupo());
	$grupoAccion->setGacNombre($_POST["nombre"]);
	$grupoAccion->setGacValor($_POST["valor"]);
	
	if ($_POST["valor"] > 100) {
		$mensaje1=sprintf(litErrorMas100);
		$classMsgBox = "msgBox bgRed txtWhite";
	} else {
		if (!$editar) {
			$grupoAccion->setGacUsucre($_SESSION["sesIduser"]);
			
			if ($grupoAccion->insert($conMsi, $pageCode)){
				$mensaje1=sprintf(litCambiosOk);
				$classMsgBox = "msgBox bgGreen txtBlack";
				header("Location: /acciones-listado.php?idGrupo=".$idGrupo);
				die();
			} else {
				$mensaje1=sprintf(litError1);
				$mensaje2=sprintf(litError2, $mailAdmin);
				$classMsgBox = "msgBox bgRed txtWhite";
			}
		} else {
			if ($grupoAccion->update($conMsi, $pageCode)){
				header("Location: /acciones-listado.php?idGrupo=".$idGrupo);
				die();
			} else {
				$mensaje1=sprintf(litError1);
				$mensaje2=sprintf(litError2, $mailAdmin);
				$classMsgBox = "msgBox bgRed txtWhite";
			}
		}
	}
}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$nombreGeneral." - ".sprintf(litNuevaAccionGrupo, $grupo->getGruNombre())?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litNuevaAccionGrupo, $grupo->getGruNombre())?>">
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 9999999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 9999999)?>" />
		<script type="text/javascript">
			function saveData(formulario){
				if (formulario.nombre.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litNombreAccion))?>");
					formulario.nombre.focus();
				} else if (formulario.valor.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litImportanciaAccion))?>");
					formulario.valor.focus();
				} else if (formulario.valor.value > 100){
					alert("<?=sprintf(litErrorMas100)?>");
					formulario.valor.focus();
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
									<input type="hidden" name="idGac" value="<?php echo $idGac?>"/>
									<?php
										if ($accion == "save"){
											echo "<div class='$classMsgBox'><span>$mensaje1</span><br/>$mensaje2</div><br/>";
											if ($mensaje2_1 != "") echo "<div class='$classMsgBox2'><span>$mensaje2_1</span><br/>$mensaje2_2</div><br/>";
											echo "<br/>";
										}
									?>
									<header>
										<h2><?=sprintf(litNuevaAccionGrupo, $grupo->getGruNombre())?></h2>
										<?php if ($ayudaInicial) {?>
												<span><?php echo sprintf(litAyudaNewAccion01)?><br><?php echo sprintf(litAyudaNewAccion02)?></span>
										<?php }?>		
									</header>
									<div>
										<label class="desc" for="nombre"><?=sprintf(litNombreAccion)?></label>
										<div>
											<input id="nombre" name="nombre" type="text" maxlength="100" value="<?=$grupoAccion->getGacNombre()?>">
										</div>
									</div>
									<div>
										<label class="desc" for="valor"><?=sprintf(litImportanciaAccion)?><span class="txtRed">*</span> <a class="noUnderlined" href="javascript:alert('<?php echo sprintf(litAyudaImportancia)?>')"><img src="/images/infoLeft.gif"></a></label>
										<div>
											<input id="valor" name="valor" type="number" maxlength="3" value="<?=$grupoAccion->getGacValor()?>">
										</div>
									</div>
									<div>
										<div>
									  		<br><input class="button" id="saveForm" name="saveForm" type="submit" onclick="saveData(this.form);return false;" value="<?=sprintf(litEnviarDatos)?>">
								  			&nbsp;<input class="button" id="volver" name="volver" type="button" onclick="history.back();" value="<?=sprintf(litVolver)?>">
									    </div>
									</div>
									  
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
	</body>
</html>	

<?php
	mysqli_close($conMsi);
	die();
?>