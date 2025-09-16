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
include_once 'classes/User.php';
include_once 'classes/Grupo.php';
include_once 'classes/GrupoUser.php';
include_once 'classes/Rol.php';

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

$idGrupo = $_GET["idGrupo"];

$grupoUser = new GrupoUser();
$grupoUser->setGusIdgrupo($idGrupo);
$grupoUser->setGusIduser($_SESSION["sesIduser"]);
if (!$grupoUser->checkEsAdministrador($conMsi, $pageCode)) {
	die();
}

$grupo = new Grupo();
$grupo->setGruIdgrupo($idGrupo);
$grupo->setGruIduser($_SESSION["sesIduser"]);
$grupo->getGrupo($conMsi, $pageCode);

$accion = $_POST["accion"];
if ($accion == "save"){
	
	$grupoUserAlta = new GrupoUser();
	$grupoUserAlta->setGusIdgrupo($idGrupo);
	
	$userExiste = new User();
	$userExiste->setUseName($_POST["name"]);
	$userExiste->setUseMail($_POST["mail"]);
	if (!$userExiste->checkUserMailAlreadyExistsSet($conMsi, $pageCode)) {
		$userExiste->insertProfilePendiente($conMsi, $pageCode);
	}
	$grupoUserAlta->setGusIduser($userExiste->getUseIduser());
	$grupoUserAlta->setGusIdrol($_POST["idrol"]);
	$grupoUserAlta->setGusUsucre($_SESSION["sesIduser"]);
	$grupoUserAlta->setGusVerifyCode(Funciones::getNewCode());

	if ($grupoUserAlta->insertGrupoUser($conMsi, $pageCode)) {
		$mensaje1=sprintf(litCambiosOk);
		$classMsgBox = "msgBox bgGreen txtBlack";
		
		$bodyUser = sprintf(litEstimado, $userExiste->getUseName())."\n\n".
				sprintf(litMailBienvenido01)."\n".
				sprintf(litMailBienvenido02, $_SESSION["sesName"].($_SESSION["sesLastname"]!=" ".$_SESSION["sesLastname"]?"":""), $grupo->getGruNombre())."\n\n".
				sprintf(litMailBienvenido03)."\n".
				$accesoHttp.$rootURL."/gruok/".$grupoUserAlta->getGusIduser()."/".$grupoUserAlta->getGusIdgrupo()."/".$grupoUserAlta->getGusVerifyCode()."\n\n".
				sprintf(litMailBienvenido04)."\n".
				$accesoHttp.$rootURL."/gruko/".$grupoUserAlta->getGusIduser()."/".$grupoUserAlta->getGusIdgrupo()."/".$grupoUserAlta->getGusVerifyCode()."\n\n".
				sprintf(litAtentamente)."\n".
				$nombreGeneral.": ".$accesoHttp.$rootURL;
		$subjectUser = $nombreGeneral.": ".sprintf(litMailBienvenidoSubject, $_SESSION["sesName"]);
				
		if ($enviarMails) enviarMailSMTP($mailAdmin, $userExiste->getUseMail(), "", "", $subjectUser, $bodyUser, $userExiste->getUseIduser());
				
		header("Location: /mis-grupos-miembros?idGrupo=".$idGrupo);
		die();
	}else{
		$mensaje1=sprintf(litError1);
		$mensaje2=sprintf(litError2, $mailAdmin);
		$classMsgBox = "msgBox bgRed txtWhite";
	}
}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$nombreGeneral." - ".sprintf(litNuevoMiembroGrupo, $grupo->getGruNombre())?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litNuevoMiembroGrupo, $grupo->getGruNombre())?>">
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
		<script src="/js/sign.js"></script>
		<script type="text/javascript">
			function saveData(formulario){
				if (formulario.name.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litNombre))?>");
					formulario.name.focus();
				} else if (formulario.mail.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litMail))?>");
					formulario.mail.focus();
				} else if (formulario.idrol.value==""){
					alert("<?=sprintf(litCampoOblig, sprintf(litRol))?>");
					formulario.idrol.focus();
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
										<h2><?=sprintf(litAnadirMiembroGrupo, $grupo->getGruNombre())?></h2>
									</header>
									  
									<div>
										<label class="desc" for="name"><?=sprintf(litNombre)?> <span class="txtRed">*</span></label>
										<div>
											<input id="name" name="name" type="text" value="" maxlength="50"> 
										</div>
									</div>
									<div>
										<label class="desc" for="mail"><?=sprintf(litMail)?> <span class="txtRed">*</span></label>
										<div>
											<input id="mail" name="mail" type="email" spellcheck="false" value="" maxlength="255"> 
										</div>
									</div>
									<div>
										<label class="desc" for="idrol"><?=sprintf(litRol)?> <span class="txtRed">*</span></label>
										<div>
											<select id="idrol" name="idrol">
												<?php
												$rol = new Rol();
												foreach ($rol->getRoles($conMsi, $pageCode) as $objRol) {
													echo "<option value = '".$objRol->getRolIdrol()."'>".constant($objRol->getRolNombre())."</option>";
												}
												?>
											</select>
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