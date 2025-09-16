<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
	session_start();
	$pageCode = "MLI";
	
	include("admin/in_variables.php");
	include("in_www.php");
	require_once("in_func_mail.php");

	include("in_idiom.php");
	
	include_once 'classes/Unidad.php';
	include_once 'classes/Grupo.php';
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
	
	$order=$_GET["order"]==""?"1":$_GET["order"];
	$asc=$_GET["asc"]==""?"1":$_GET["asc"];

	$idGrupo = $_GET["idGrupo"];
	
	$grupo = new Grupo();
	$grupo->setGruIdgrupo($idGrupo);
	$grupo->setGruIduser($_SESSION["sesIduser"]);
	if (!$grupo->getGrupo($conMsi, $pageCode)) {
		die;
	}
	if ($grupo->getEsAdmin() == 1) {
		$esAdmin = true;
	}
	
	$accion = $_POST["accion"];
	if ($esAdmin && $accion == "delete"){
		$idUser = $_POST["idUser"];
		$borrarseASiMismo = false;
		if ($idUser == $_SESSION["sesIduser"]) {
			$borrarseASiMismo = true;
		}
		
		$grupoUser = new GrupoUser();
		$grupoUser->setGusIdgrupo($idGrupo);
		$grupoUser->setGusIduser($idUser);

		if ($grupoUser->deleteGrupoUser($conMsi, $pageCode)) {
			if ($grupoUser->quedanUsuarios($conMsi, $pageCode)) {				
				if ($borrarseASiMismo){
					if (!$grupoUser->quedaAlgunAdministrador($conMsi, $pageCode)) {
						$grupoUser->ponerNuevoAdministrador($conMsi, $pageCode);
					}
					header("Location: /mis-grupos");
					die();
				} else {
					$mensaje1=sprintf(litCambiosOk);
					$classMsgBox = "msgBox bgGreen txtBlack";
				}
			} else {
				// no quedan usuarios, se borra el grupo y se vuelve atras
				$grupo = new Grupo();
				$grupo->setGruIduser($_SESSION["sesIduser"]);
				$grupo->setGruIdgrupo($idGrupo);
				if ($grupo->deleteGrupo($conMsi, $pageCode)){
					header("Location: /mis-grupos");
					die();
				}else{
					$mensaje1=sprintf(litError1);
					$mensaje2=sprintf(litError2, $mailAdmin);
					$classMsgBox = "msgBox bgRed txtWhite";
				}
			}
			
		} else {
			$mensaje1=sprintf(litError1);
			$mensaje2=sprintf(litError2, $mailAdmin);
			$classMsgBox = "msgBox bgRed txtWhite";
		}
	}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$nombreGeneral." - ".sprintf(litMiembrosGrupo, $grupo->getGruNombre())?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litMiembrosGrupo, $grupo->getGruNombre())?>">
		<meta name="verify-v1" content="iktchguQVSJTd8nwo6NGXdZ0nuE1URIv9bJN/OODK8E=" />
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
		<script type="text/javascript">
			function borrar(id, nombre) {
				if (confirm("<?=sprintf(litSeguroEliminar)?>" + nombre + "?")){
					document.formularioBorrar.idUser.value = id;
					document.formularioBorrar.accion.value = "delete";
					document.formularioBorrar.submit();
				}
			}
			
			function ordenFiltro(order, asc) {
				document.formulario.action = "";
				document.formulario.target = "";
				document.formulario.order.value = order;
				document.formulario.asc.value = asc;
				document.formulario.submit();
			}
		</script>
	</head>
	<body class="homepage is-preload">
		<div id="loading"><span></span><img src="/images/loading.gif"></div>
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
										<?php
											if ($accion == "delete"){
												echo "<div class='$classMsgBox'><span>$mensaje1</span><br/>$mensaje2</div><br/>";
												echo "<br/>";
											}
										?>
										<header>
											<h2><?=sprintf(litMiembrosGrupo, $grupo->getGruNombre())?> <?php if ($esAdmin) {?><input class="button" id="saveForm" name="saveForm" type="button" onclick="window.location.href='/nuevo-miembro?idGrupo=<?=$idGrupo?>'" value="<?=sprintf(litNuevoMiembro)?>"><?php }?></h2>
											<div><?=sprintf(litReordenarColumnas)?></div>
										</header>
										<div>
											<div>
										  		
										    </div>
										</div>
										<form name="formulario" method="get">
											<input type="hidden" name="accion" value="save"/>
											<input type="hidden" name="idGrupo" value="<?=$idGrupo?>"/>
											<input type="hidden" name="order" value="<?=$order?>"/>
											<input type="hidden" name="asc" value="<?=$asc?>"/>
										</form>
										<form name="formularioBorrar" method="post">
											<input type="hidden" name="accion" value="delete"/>
											<input type="hidden" name="idUser" value="save"/>
										</form>
										
										<div class="scroll">
											<table class="gen">
												<thead>
													<tr>
														<th <?=Funciones::getArrow("1", $order, $asc)?> onclick="ordenFiltro(1, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litNombre)?></th>
														<th <?=Funciones::getArrow("2", $order, $asc)?> onclick="ordenFiltro(2, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litApellidos)?></th>
														<th <?=Funciones::getArrow("3", $order, $asc)?> onclick="ordenFiltro(3, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litRol)?></th>
														<?php if ($esAdmin) {?>
															<th></th>
														<?php }?>
													</tr>
												</thead>
												<tbody>
													<?php
														$i = 0;
														$grupoUser = new GrupoUser();
														$grupoUser->setGusIdgrupo($idGrupo);
														$grupoUser->setOrder($order);
														$grupoUser->setAsc($asc);
														foreach ($grupoUser->getGrupoUsers($conMsi, $pageCode) as $objGrupoUser){
															$clase="";
															if ($objGrupoUser->getGusVerifyCode() != "") {
																$clase="bgLightRed";
															}
													?>
														    <tr class="<?=$clase?>">
														      <td><?=$objGrupoUser->getUseName()?></td>
														      <td><?=$objGrupoUser->getUseLastname()?></td>
														      <td><?=constant($objGrupoUser->getRolNombre())?></td>
																<?php if ($esAdmin) {?>
																      <td class="centered">
														      			<input type="image" class="tdIcon" src="/images/delete.png" id="imageButton" title="<?=sprintf(litBorrar)?>" alt="<?=sprintf(litBorrar)?>" onClick="borrar('<?=$objGrupoUser->getGusIduser()?>', '<?=$objGrupoUser->getUseName()?>');return false;"/>
																      </td>
													      		<?php }?>
														    </tr>
													<?php
															$i++;
														}
													?>
												</tbody>
											</table>
											<div>
												<div>
											  		<br><input class="button" id="volver" name="volver" type="button" onclick="history.back();" value="<?=sprintf(litVolver)?>">
											    </div>
											</div>
										</div>
										
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