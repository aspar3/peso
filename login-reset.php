<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

	$pageCode = "LRS";
                                                                                                                     
	include("admin/in_variables.php");
	include("in_www.php");
	require_once("in_func_mail.php");
	
	include("in_idiom.php");

	include_once 'classes/User.php';
	include_once 'classes/Idioma.php';
	
	$idiomaTxt = $_GET["idioma"];
	if ($idiomaTxt == "") {
		$idiomaTxt = "es";
	}
	include_once 'literales/idioma_'.$idiomaTxt.'.php';
	
	$conMsi = crearConexionMysqli();
	
	$user = new User();
	$user->setUseIduser($_GET["idUser"]);
	$user->setUseReminderCode($_GET["codValida"]);

	$showInput=true;
	$showError=false;
	$showOk=false;
	if (is_numeric($user->getUseIduser()) && $user->getUseReminderCode()!= ""){
		if ($user->checkResetUserPassword($conMsi, $pageCode)){
			if ($_POST["accion"]=="save"){
				$newPassword = trim($_POST["newPassword"]);
				$newPassword2 = trim($_POST["newPassword2"]);
				if ($newPassword!="" && $newPassword==$newPassword2){
					$user->setUsePassword($newPassword);
					if ($user->updatePassword($conMsi, $pageCode)){
						$mensaje1=sprintf(litCambiosOk);
						$mensaje2=sprintf(litPassChangeOk);
						$classMsgBox = "msgBox bgGreen txtBlack";
					}else{
						$mensaje1=sprintf(litError1);
						$mensaje2=sprintf(litError2, $mailAdmin);
						$classMsgBox = "msgBox bgRed txtWhite";
					}
				}
				$showInput=false;
				$showOk=true;
			}
		}else{
			$mensaje1=sprintf(litEnlaceKo01);
			$mensaje2=sprintf(litEnlaceKo02);
			$classMsgBox = "msgBox bgRed txtWhite";
			$showInput=false;
			$showError=true;
		}
	}
	mysqli_close($conMsi);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$nombreGeneral." - ".sprintf(litCambiarPass01)?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litCambiarPass01)?>">
		<meta name="verify-v1" content="iktchguQVSJTd8nwo6NGXdZ0nuE1URIv9bJN/OODK8E=" />
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?878" />
		<script type="text/javascript">
			function valForm(formulario){
				if (formulario.newPassword.value == "")
					alert("<?=sprintf(litCampoOblig, sprintf(litPassword))?>");
				else if (formulario.newPassword.value != formulario.newPassword2.value)
					alert("<?=sprintf(litPaswwordKo)?>");
				else formulario.submit();
			}
		</script>
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

								<form name="formulario" method="post">
									<input type="hidden" name="accion" value="save"/>
									<?php if ($mensaje1!=""){?>
											<div class='<?=$classMsgBox?>'><span><?=$mensaje1?></span><br/><?=$mensaje2?></div><br/><br/>
									<?php }?>
									<?php if ($showInput){?>
											<header>
												<h2><?=sprintf(litCambiarPass01)?></h2>
												<div><?=sprintf(litCambiarPass02)?>.</div>
											</header>
											<div>
											    <label class="desc" for="newPassword"><?=sprintf(litNuevaPass)?> <span class="txtRed">*</span></label>
											    <div>
													<input id="newPassword" name="newPassword" type="password" spellcheck="false" value="" maxlength="255"> 
												</div>
											</div>
											<div>
												<label class="desc" for="newPassword2"><?=sprintf(litNuevaPass)?> <span class="txtRed">*</span></label>
											    <div>
													<input id="newPassword2" name="newPassword2" type="password" spellcheck="false" value="" maxlength="255"> 
												</div>
											</div>
											<div>
												<div>
											  		<input class="button" id="saveForm" name="saveForm" type="submit" onclick="valForm(this.form);return false;" value="<?=sprintf(litReinicarPass)?>">
											    </div>
											</div>
									<?php }?>
									<?php if ($showOk){?>
											<div>
												<div>
											  		<br><input class="button" id="saveForm" name="saveForm" type="button" onclick="window.location.href='/login'" value="<?=sprintf(litIrInicioSesion)?>">
											    </div>
											</div>
									<?php }?>
									<?php if ($showError){?>
											<div>
												<div>
											  		<br><input class="button" id="saveForm" name="saveForm" type="button" onclick="window.location.href='/login-reminder'" value="<?=sprintf(litVuelvaIntentar)?>">
											    </div>
											</div>
									<?php }?>
									  
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