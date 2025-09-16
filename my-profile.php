<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
session_start();
$pageCode = "MPR";

include("admin/in_variables.php");
include("in_www.php");
require_once("in_func_mail.php");

include("in_idiom.php");

include_once 'classes/Funciones.php';
include_once 'classes/User.php';
include_once 'classes/Idioma.php';
include_once 'classes/Unidad.php';

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

if ($_GET["msj"] = "ok") {
	$mensaje1=sprintf(litCambiosOk);
	$mensaje2=sprintf(litPerfilOk);
	$classMsgBox = "msgBox bgGreen txtBlack";
}

$user = new User();
$user->setUseIduser($_SESSION["sesIduser"]);
$user->setUserWithId($conMsi, $pageCode);

$accion = $_POST["accion"];
if ($accion == "save"){
	
	$user->setUseMail($_POST["mail"]);
	$user->setUseName($_POST["name"]);
	$user->setUseLastname($_POST["lastname"]);
	$user->setUseIdidioma($_POST["ididioma"]);
	$user->setUseIdunidad($_POST["idunidad"]);
	$user->setUseMostrarPeso($_POST["mostrarPeso"]);
	$user->setUsePassword($_POST["password"]);
	
	if ($user->checkMailAlreadyExists($conMsi, $pageCode, $user->getUseMail())){
		$mensaje2_1=sprintf(litMailExiste);
		$mensaje2_2=$user->getUseMail().": ".sprintf(litError2, $mailAdmin);
		$classMsgBox2 = "msgBox bgRed txtWhite";
	} else {
		
		if ($user->updateProfile($conMsi, $pageCode)){
			$_SESSION["sesIdidioma"] = $user->getUseIdidioma();
			$_SESSION["sesIdunidad"] = $user->getUseIdunidad();
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
			
			header("Location: my-profile?msj=ok");
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
		<title><?=$nombreGeneral." - ".sprintf(litMiPerfil)?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litMiPerfil)?>">
		<meta name="verify-v1" content="iktchguQVSJTd8nwo6NGXdZ0nuE1URIv9bJN/OODK8E=" />
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
		<script src="/js/sign.js"></script>
		<script type="text/javascript">
			function saveData(formulario){
				if (formulario.mail.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litMail))?>");
					formulario.mail.focus();
				} else if (formulario.name.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litNombre))?>");
					formulario.name.focus();
				} else if (formulario.ididioma.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litIdioma))?>");
					formulario.ididioma.focus();
				} else if (formulario.idunidad.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litUnidad))?>");
					formulario.idunidad.focus();
				} else if (formulario.mostrarPeso.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litMostrarPeso))?>");
					formulario.mostrarPeso.focus();
				} else if (formulario.password.value!="" && formulario.password.value!=formulario.password2.value){
					alert("<?=sprintf(litPaswwordKo)?>");
					formulario.password2.focus();
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
										if ($accion == "save" || $_GET["msj"] = "ok"){
											echo "<div class='$classMsgBox'><span>$mensaje1</span><br/>$mensaje2</div><br/>";
											if ($mensaje2_1 != "") echo "<div class='$classMsgBox2'><span>$mensaje2_1</span><br/>$mensaje2_2</div><br/>";
											echo "<br/>";
										}
									?>
									<header>
										<h2><?=sprintf(litCambiarPerfil)?></h2>
										<div><?=sprintf(litNoCambiarPass)?></div>
									</header>
									  
									<div>
										<label class="desc" id="title3" for="mail"><?=sprintf(litMail)?> <span class="txtRed">*</span></label>
										<div>
											<input id="mail" name="mail" type="email" spellcheck="false" value="<?=$user->getUseMail()?>" maxlength="255"> 
										</div>
									</div>
									<div>
										<label class="desc" for="nombre"><?=sprintf(litNombre)?> <span class="txtRed">*</span></label>
										<div>
											<input id="name" name="name" type="text" value="<?=$user->getUseName()?>" maxlength="50">
										</div>
									</div>
									<div>
										<label class="desc" for="nombre"><?=sprintf(litApellidos)?></label>
										<div>
											<input id="lastname" name="lastname" type="text" value="<?=$user->getUseLastname()?>" maxlength="200">
										</div>
									</div>
									<div>
										<label class="desc" for="ididioma"><?=sprintf(litIdioma)?> <span class="txtRed">*</span></label>
										<div>
											<select id="ididioma" name="ididioma">
												<?php
												$idioma = new Idioma();
												foreach ($idioma->getIdiomasActive($conMsi, $pageCode) as $objIdioma) {
													echo "<option ".($objIdioma->getIdmIdidioma() == $user->getUseIdidioma()?"selected":"")." value = '".$objIdioma->getIdmIdidioma()."'>".$objIdioma->getIdmName()."</option>";
												}
												?>
											</select>
										</div>
									</div>
									<div>
										<label class="desc" for="idunidad"><?=sprintf(litUnidad)?> <span class="txtRed">*</span></label>
										<div>
											<select id="idunidad" name="idunidad">
												<?php
												$unidad = new Unidad();
												foreach ($unidad->getUnidades($conMsi, $pageCode) as $objUnidad) {
													echo "<option ".($objUnidad->getUniIdunidad() == $user->getUseIdunidad()?"selected":"")." value = '".$objUnidad->getUniIdunidad()."'>".$objUnidad->getUniNombre()."</option>";
												}
												?>
											</select>
										</div>
									</div>
									<div>
										<label class="desc" for="idunidad"><?=sprintf(litMostrarPesoDemas)?> <span class="txtRed">*</span></label>
										<div>
											<select id="mostrarPeso" name="mostrarPeso">
												<option <?=($user->getUseMostrarPeso() == "S"?" selected ":"")?> value="S"><?=sprintf(litSi)?></option>
												<option <?=($user->getUseMostrarPeso() == "N"?" selected ":"")?> value="N"><?=sprintf(litNoPorcen)?></option>
											</select>
										</div>
									</div>
									<div>
									    <label class="desc" id="title3" for="password"><?=sprintf(litPasswordNew)?></label>
									    <div>
											<input id="password" name="password" type="password" spellcheck="false" placeholder="<?=sprintf(litPasswordVacioNoCambio)?>" value="" maxlength="255"> 
										</div>
									</div>
									<div>
										<label class="desc" id="title3" for="password2"><?=sprintf(litPasswordNewRepetir)?></label>
									    <div>
											<input id="password2" name="password2" type="password" spellcheck="false" placeholder="<?=sprintf(litPasswordVacioNoCambio)?>" value="" maxlength="255"> 
										</div>
									</div>
									<div>
										<div>
									  		<br><input class="button" id="saveForm" name="saveForm" type="submit" onclick="saveData(this.form);return false;" value="<?=sprintf(litEnviarDatos)?>">
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