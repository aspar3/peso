<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
	
	$pageCode = "LRE";

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
	
	
	$mail= $password = "";

	$showInput=true;
	if ($_POST["accion"]=="save"){
		$conMsi= crearConexionMysqli();
		$mail = $_POST["mail"];
		if ($mail!=""){
			$user = new User();
			$user->setUseMail($mail);
			if ($user->setUserWithMail($conMsi, $pageCode)){
				$codValida = $user->getNewCode();
				$user->setUseReminderCode($codValida);
				if ($user->updateReminderCode($conMsi, $pageCode)){
					$bodyUser = sprintf(litEstimado, $userInvitador->getUseName())."\n\n".
							sprintf(litCambioPassMail01)."\n".
							$accesoHttp.$rootURL.$urlIdm."/res/".$user->getUseIduser()."/".$codValida."\n\n".
							sprintf(litAtentamente)."\n".
							$nombreGeneral.": ".$accesoHttp.$rootURL;
					$subjectUser = $nombreGeneral.": ".sprintf(litCambioPassMailSubject);
					
					if ($enviarMails) enviarMailSMTP($mailAdmin, $user->getUseMail(), "", "", $subjectUser, $bodyUser, $idUser);
					
					$mensaje1=sprintf(litEnlaceEnviado01);
					$mensaje2=sprintf(litEnlaceEnviado02);
					$classMsgBox = "msgBox bgGreen txtBlack";
					$showInput=false;
				}

			}else{
				$mensaje1=sprintf(litError1);
				$mensaje2=sprintf(litError2, $mailAdmin);
				$classMsgBox = "msgBox bgRed txtWhite";
			}
		}
		mysqli_close($conMsi);
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$nombreGeneral." - ".sprintf(litCambiarPass01)?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litCambiarPass01)?>">
		<meta name="verify-v1" content="iktchguQVSJTd8nwo6NGXdZ0nuE1URIv9bJN/OODK8E=" />
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
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
									<?php	if ($mensaje1!=""){?>
												<div class='<?=$classMsgBox?>'><span><?=$mensaje1?></span><br/><?=$mensaje2?></div><br/><br/>
									<?php 	}?>
									<?php	if ($showInput){?>
											<header>
												<h2><?=sprintf(litCambiarPass01)?></h2>
												<div><?=sprintf(litCambiarPass02)?></div>
											</header>
											<div>
												<label class="desc" for="mail"><?=sprintf(litMail)?> <span class="txtRed">*</span></label>
												<div>
													<input id="mail" name="mail" type="email" spellcheck="false" value="<?=$mail?>" maxlength="255"> 
												</div>
											</div>
											<div>
												<div>
											  		<br><input class="button" id="saveForm" name="saveForm" type="submit" value="<?=sprintf(litReinicarPass)?>">
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