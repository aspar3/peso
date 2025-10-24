<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
	session_start();
	$pageCode = "ALI";
	
	include("admin/in_variables.php");
	include("in_www.php");
	require_once("in_func_mail.php");

	include("in_idiom.php");
	
	include_once 'classes/Unidad.php';
	include_once 'classes/User.php';
	include_once 'classes/Grupo.php';
	include_once 'classes/GrupoUser.php';
	include_once 'classes/GrupoAccion.php';
	
	if (!isset($_SESSION["sesIduser"]) || $_SESSION["sesIduser"]=="" || $_SESSION["sesStatus"]!=1){
		//rolLog("$pageCode-01", "No session started or not a signedup user -> (".$_SESSION["sesIduser"].")", 1);
		header("Location: /login?new=yes&goUrl=".ltrim($_SERVER['REQUEST_URI'], '/'));
		die();
	}

	// 1: Peso. 2: Otros 3: Acciones
	$gruTipo = "3";
	
	$idiomaTxt = $_SESSION["sesIdmLocale"];
	if ($idiomaTxt == "") {
		$idiomaTxt = "es";
	}
	include_once 'literales/idioma_'.$idiomaTxt.'.php';
	
	$order=$_GET["order"]==""?"1":$_GET["order"];
	$asc=$_GET["asc"]==""?"1":$_GET["asc"];
	
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
	
	$accion = $_POST["accion"];
	if ($accionGet == "aceptar"){
		$grupoUser = new GrupoUser();
		$grupoUser->setGusIdgrupo($_GET["idGrupo"]);
		$grupoUser->setGusIduser($_SESSION["sesIduser"]);
		$grupoUser->aceptarInvitacion($conMsi, $pageCode);
		header("Location: /mis-grupos");
	} else if ($accionGet == "rechazar"){
		$grupoUser = new GrupoUser();
		$grupoUser->setGusIdgrupo($_GET["idGrupo"]);
		$grupoUser->setGusIduser($_SESSION["sesIduser"]);
		$grupoUser->deleteGrupoUser($conMsi, $pageCode);
		header("Location: /mis-grupos");
	}
	
	if ($accion == "delete"){
		$grupoAccionDelete = new GrupoAccion();
		$grupoAccionDelete->setGacIdgrupo($idGrupo);
		$grupoAccionDelete->setGacIdaccion($_POST["idAccion"]);
		if ($grupoAccionDelete->delete($conMsi, $pageCode)) {
			$mensaje1=sprintf(litCambiosOk);
			$classMsgBox = "msgBox bgGreen txtBlack";
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
		<title><?=$nombreGeneral." - ".sprintf(litAccionesGrupo, $grupo->getGruNombre())?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litAccionesGrupo, $grupo->getGruNombre())?>">
		<meta name="verify-v1" content="iktchguQVSJTd8nwo6NGXdZ0nuE1URIv9bJN/OODK8E=" />
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 9999999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 9999999)?>" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.3.0/chart.min.js"></script>
		<script src="/js/funciones.js?<?=rand(0, 9999999)?>"></script>
		<script type="text/javascript">
			function borrar(id) {
				if (confirm("<?=sprintf(litSeguroElimAccion)?>")){
					formularioBorrar.idAccion.value = id;
					formularioBorrar.accion.value = "delete";
					formularioBorrar.submit();
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
											<h2><?=sprintf(litAccionesGrupo, $grupo->getGruNombre())?> <input class="button" id="saveForm" name="saveForm" type="button" onclick="window.location.href='/acciones-nueva.php?idGrupo=<?php echo $idGrupo?>'" value="<?=sprintf(litNuevaAccion)?>"></h2>
											<br>
											<div><?=sprintf(litReordenarColumnas)?></div>
										</header>
										<form name="formulario" method="get">
											<input type="hidden" name="accion" value="save"/>
											<input type="hidden" name="id" value=""/>
											<input type="hidden" name="order" value="<?=$order?>"/>
											<input type="hidden" name="asc" value="<?=$asc?>"/>
										</form>
										<form name="formularioBorrar" method="post">
											<input type="hidden" name="accion" value="delete"/>
											<input type="hidden" name="idAccion" value=""/>
										</form>
										
										<div class="scroll">
											<table class="gen">
												<thead>
													<tr>
														<th <?=Funciones::getArrow("1", $order, $asc)?> onclick="ordenFiltro(1, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litNombre)?></th>
														<th <?=Funciones::getArrow("2", $order, $asc)?> onclick="ordenFiltro(2, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litImportanciaAccion)?></th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
														$i = 0;
														$grupoAccion = new GrupoAccion();
														$grupoAccion->setGacIdgrupo($grupo->getGruIdgrupo());
														$grupoAccion->setOrder($order);
														$grupoAccion->setAsc($asc);
														foreach ($grupoAccion->getGrupoAcciones($conMsi, $pageCode, $ascDesc) as $objAccion){
													?>
														    <tr <?php if ($esAdmin) { echo 'onclick="detalleLinea('.$objAccion->getGacIdaccion().')"';}?>>
														      <td><?=$objAccion->getGacNombre()?></td>
														      <td class="number"><?=$objAccion->getGacValor()?></td>
														      <td class="centered">
														      		<?php if ($esAdmin) {?>
																			<input type="image" class="tdIcon" src="/images/flechaDetalle.gif" id="imageButton" title="<?=sprintf(litVerOpciones)?>" alt="<?=sprintf(litVerOpciones)?>" onClick="detalleLinea(<?=$objAccion->getGacIdaccion()?>)';return false;"/><br>
																	<?php }?>
														      </td>
														    </tr>
														    <tr class="oculto"></tr> <!-- para mantener los estilos de las filas de las tablas pares e impares -->
														    <tr class="ocultoFila" id="linea_<?=$objAccion->getGacIdaccion()?>">
														    	<td colspan="3">
																	<?php if ($esAdmin) {?>
															    		<button class="botonTabla" onClick="window.location.href='/acciones-nueva.php?idGrupo=<?php echo $grupo->getGruIdgrupo()?>&idGac=<?=$objAccion->getGacIdaccion()?>';">
																			<img src="/images/checksGreen.gif" class="imageButton">
																			<span><?=sprintf(litModificar);?></span>
																		</button>
																		<button class="botonTabla" onClick="borrar(<?=$objAccion->getGacIdaccion()?>)">
																			<img src="/images/delete.png" class="imageButton">
																			<span><?=sprintf(litBorrar)?></span>
																		</button>
																	<?php }?>
																</td>
														    </tr>
													<?php
															$i++;
														}
													?>
												</tbody>
											</table>
										</div>
										<input class="button" id="volver" name="volver" type="button" onclick="window.location.href='/otros-mis-grupos.php'" value="<?=sprintf(litVolver)?>">
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

		<?php include("in-footer.php");?>
		
	</body>
</html>	

<?php
	mysqli_close($conMsi);
	die();
?>