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
	include_once 'classes/User.php';
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
	
	$order=$_GET["order"]==""?"1":$_GET["order"];
	$asc=$_GET["asc"]==""?"1":$_GET["asc"];
	
	$conMsi= crearConexionMysqli();
	
	$idGrupo = $_POST["idGrupo"];
	$accion = $_POST["accion"];
	$accionGet = $_GET["accion"];
	
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
		$grupo = new Grupo();
		$grupo->setGruIdgrupo($idGrupo);
		$grupo->setGruIduser($_SESSION["sesIduser"]);
		$grupo->getGrupo($conMsi, $pageCode);
		
		if ($grupo->getEsAdmin()) {
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIdgrupo($idGrupo);
			$grupoUser->setGusIduser($_SESSION["sesIduser"]);
			
			if ($grupoUser->deleteGrupoUser($conMsi, $pageCode)) {
				if ($grupoUser->quedanUsuarios($conMsi, $pageCode)) {
					if (!$grupoUser->quedaAlgunAdministrador($conMsi, $pageCode)) {
						if ($grupoUser->ponerNuevoAdministrador($conMsi, $pageCode)) {
							$mensaje1=sprintf(litCambiosOk);
							$classMsgBox = "msgBox bgGreen txtBlack";
						} else {
							$mensaje1=sprintf(litError1);
							$mensaje2=sprintf(litError2, $mailAdmin);
							$classMsgBox = "msgBox bgRed txtWhite";
						}
					}
				} else {
					// no quedan usuarios, se borra el grupo y se vuelve atras
					$grupo = new Grupo();
					$grupo->setGruIduser($_SESSION["sesIduser"]);
					$grupo->setGruIdgrupo($idGrupo);
					if (!$grupo->deleteGrupo($conMsi, $pageCode)){
						$mensaje1=sprintf(litError1);
						$mensaje2=sprintf(litError2, $mailAdmin);
						$classMsgBox = "msgBox bgRed txtWhite";
					}
				}
			}
		} else {
			$mensaje1=sprintf(litBorrarGrupoNoAdmin01);
			$mensaje2=sprintf(litBorrarGrupoNoAdmin02);
			$classMsgBox = "msgBox bgRed txtWhite";
		}
	}
	
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$nombreGeneral." - ".sprintf(litMisGrupos)?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litMisGrupos)?>">
		<meta name="verify-v1" content="iktchguQVSJTd8nwo6NGXdZ0nuE1URIv9bJN/OODK8E=" />
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.3.0/chart.min.js"></script>
		<script type="text/javascript">
			function borrar(id) {
				if (confirm("<?=sprintf(litSeguroElimGrupo)?>")){
					formularioBorrar.idGrupo.value = id;
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
										<?php
											$grupo = new Grupo();
											$grupo->setGruIduser($_SESSION["sesIduser"]);
											$grupo->setOrder($order);
											$grupo->setAsc($asc);
											$listGruposPendientes = $grupo->getGruposPendientes($conMsi, $pageCode);
											if (is_array($listGruposPendientes) && count($listGruposPendientes) > 0){
										?>
												<header>
													<h2><?=sprintf(litInvitacionesPendientes)?></h2>
													<div><?=sprintf(litIconoVerdeRojo)?></div>
												</header>
												<div class="scroll">
													<table class="gen">
														<thead>
															<tr>
																<th <?=Funciones::getArrow("1", $order, $asc)?> onclick="ordenFiltro(1, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litNombre)?></th>
																<th <?=Funciones::getArrow("2", $order, $asc)?> onclick="ordenFiltro(2, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litFechaInicio)?></th>
																<th <?=Funciones::getArrow("3", $order, $asc)?> onclick="ordenFiltro(3, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litFechaFin)?></th>
																<th <?=Funciones::getArrow("4", $order, $asc)?> onclick="ordenFiltro(4, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litMiembros)?></th>
																<th></th>
															</tr>
														</thead>
														<tbody>
															<?php
																$i = 0;
																foreach ($listGruposPendientes as $objGrupo){
															?>
																    <tr>
																      <td><?=$objGrupo->getGruNombre()?></td>
																      <td><?=Funciones::fechaFormateadaIdioma($objGrupo->getGruFecini(), $_SESSION["sesIdidioma"])?></td>
																      <td><?=Funciones::fechaFormateadaIdioma($objGrupo->getGruFecfin(), $_SESSION["sesIdidioma"])?></td>
																      <td class="number"><?=$objGrupo->getNumeroMiembros()?></td>
																      <td class="centered">
																			<input type="image" class="tdIcon" src="/images/like.gif" id="imageButton" title="<?=sprintf(litAceptarInvitacion)?>" alt="<?=sprintf(litAceptarInvitacion)?>" onClick="if (confirm('<?=sprintf(litAceptarInvitacionConfirm)?>')) {window.location.href='/mis-grupos?accion=aceptar&idGrupo=<?=$objGrupo->getGruIdgrupo()?>';}return false;"/>
																      		<input type="image" class="tdIcon" src="/images/likeNot.gif" id="imageButton" title="<?=sprintf(litRechazarInvitacion)?>" alt="<?=sprintf(litRechazarInvitacion)?>" onClick="if (confirm('<?=sprintf(litRechazarInvitacionConfirm)?>')) {window.location.href='/mis-grupos?accion=rechazar&idGrupo=<?=$objGrupo->getGruIdgrupo()?>';}return false;"/>
																      </td>
																    </tr>
															<?php
																	$i++;
																}
															?>
														</tbody>
													</table>
												</div>
										<?php }?>
										<header>
											<h2><?=sprintf(litMisGrupos)?> <input class="button" id="saveForm" name="saveForm" type="button" onclick="window.location.href='/nuevo-grupo'" value="<?=sprintf(litCrearNuevoGrupo)?>"></h2>
											<div><?=sprintf(litReordenarColumnas)?></div>
										</header>
										<form name="formulario" method="get">
											<input type="hidden" name="accion" value="save"/>
											<input type="hidden" name="id" value="save"/>
											<input type="hidden" name="order" value="<?=$order?>"/>
											<input type="hidden" name="asc" value="<?=$asc?>"/>
										</form>
										<form name="formularioBorrar" method="post">
											<input type="hidden" name="accion" value="delete"/>
											<input type="hidden" name="idGrupo" value="save"/>
										</form>
										
										<div class="scroll">
											<table class="gen">
												<thead>
													<tr>
														<th <?=Funciones::getArrow("1", $order, $asc)?> onclick="ordenFiltro(1, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litNombre)?></th>
														<th <?=Funciones::getArrow("2", $order, $asc)?> onclick="ordenFiltro(2, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litFechaInicio)?></th>
														<th <?=Funciones::getArrow("3", $order, $asc)?> onclick="ordenFiltro(3, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litFechaFin)?></th>
														<th <?=Funciones::getArrow("4", $order, $asc)?> onclick="ordenFiltro(4, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litMiembros)?></th>
														<th <?=Funciones::getArrow("4", $order, $asc)?> onclick="ordenFiltro(4, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litReto)?></th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
														$i = 0;
														$grupo = new Grupo();
														$grupo->setGruIduser($_SESSION["sesIduser"]);
														$grupo->setOrder($order);
														$grupo->setAsc($asc);
														foreach ($grupo->getGruposAceptados($conMsi, $pageCode) as $objGrupo){
													?>
														    <tr>
														      <td><?=$objGrupo->getGruNombre()?></td>
														      <td><?=Funciones::fechaFormateadaIdioma($objGrupo->getGruFecini(), $_SESSION["sesIdidioma"])?></td>
														      <td><?=Funciones::fechaFormateadaIdioma($objGrupo->getGruFecfin(), $_SESSION["sesIdidioma"])?></td>
														      <td class="number"><?=$objGrupo->getNumeroMiembros()?></td>
														      <td title="<?=$objGrupo->getGruReto()?>" onclick="alert('<?=$objGrupo->getGruReto()?>')"><?=(strlen($objGrupo->getGruReto()) > 10?substr($objGrupo->getGruReto(), 0, 10)."...":$objGrupo->getGruReto())?></td>
														      <td class="centered">
														      		<?php if ($objGrupo->getEsAdmin() == "1") {?>
																			<input type="image" class="tdIcon" src="/images/edit.gif" id="imageButton" title="<?=sprintf(litModificar)?>" alt="<?=sprintf(litModificar)?>" onClick="window.location.href='/nuevo-grupo?idGrupo=<?=$objGrupo->getGruIdgrupo()?>';return false;"/><br>
																	<?php } else { ?>
																			<img src="/images/blank.gif"><br>
																	<?php } ?>
																	<input type="image" class="tdIcon" src="/images/stats.gif" id="imageButton" title="<?=sprintf(litEstadisticas)?>" alt="<?=sprintf(litEstadisticas)?>" onClick="window.location.href='/mis-grupos-estadisticas?idGrupo=<?=$objGrupo->getGruIdgrupo()?>';return false;"/><br>
																	<input type="image" class="tdIcon" src="/images/users.gif" id="imageButton" title="<?=sprintf(litMiembros)?>" alt="<?=sprintf(litMiembros)?>" onClick="window.location.href='/mis-grupos-miembros?idGrupo=<?=$objGrupo->getGruIdgrupo()?>';return false;"/><br>
														      		<input type="image" class="tdIcon" src="/images/salir.gif" id="imageButton" title="<?=sprintf(litSalirGrupo)?>" alt="<?=sprintf(litSalirGrupo)?>" onClick="borrar('<?=$objGrupo->getGruIdgrupo()?>');return false;"/>
														      </td>
														    </tr>
													<?php
															$i++;
														}
													?>
												</tbody>
											</table>
										</div>
										
									</section>
								</div>
							</div>
							<div class="graphs">
							    <div class="graph100">
									<canvas id="myChart1" width="870" height="435" style="display: block; width: 870px; height: 435px;"></canvas>
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

			<?php
				$user = new User();
				$user->setUseIduser($_SESSION["sesIduser"]);
				
				// crear array, que tendra lo siguiente en las posiciones:
				// 0) id usuario
				// 1) nombre usuario
				// 2) peso inicial
				// 3) array con los pesos
				$users = [];
				$fila = 0;
				foreach ($user->getUsersSharingGroupsWithMe($conMsi, $pageCode) as $objUser) {
					$users[$fila][0] = $objUser->getUseIduser();
					$users[$fila][1] = $objUser->getUseName();
					$users[$fila][3] = array();
					$fila++;
				}
				
				$labels = "";
				$start = $user->getFirstGroupDate($conMsi, $pageCode);
				$end   = $grupo->getGruFecfin();
				$hoy = date('Y-m-d');
				if ($start == "") { $start = $hoy;}
				if ($end > $hoy) { $end = $hoy;}
				
				$weeks = Funciones::getIsoWeeksWithStartDates($start, $end);
				foreach ($weeks as $week) {
					//echo $week['year'] . '-W' . sprintf('%02d', $week['week']) . " starts on " . $week['start_of_week'];
					$labels.= "'".Funciones::fechaFormateadaIdioma($week['start_of_week'], $_SESSION["sesIdidioma"])."', ";
					
					$grupoUserSemana = new GrupoUser();
					$grupoUserSemana->setGusIduser($_SESSION["sesIduser"]);
					foreach ($grupoUserSemana->getTodosLosGruposUsersPeso($conMsi, $pageCode, $week['year'], $week['week']) as $objUserSemana) {
						for ($fila = 0; $fila < count($users); $fila++) {
							if ($objUserSemana->getGusIduser() == $users[$fila][0]){
								if (count($users[$fila][3]) == 0) {
									$users[$fila][2] = Funciones::pesoConvertido($objUserSemana->getPesoMedio(), $_SESSION["sesIdunidad"], $_SESSION["sesUniMultipli"]);
								}
								array_push($users[$fila][3], Funciones::pesoConvertido($objUserSemana->getPesoMedio(), $_SESSION["sesIdunidad"], $_SESSION["sesUniMultipli"]));
							}
						}
					}
				}
				
				$graph2Config = "cubicInterpolationMode: 'monotone', tension: 0.4, borderWidth: 2, spanGaps: true";
				$graph1Config = $graph2Config.", fill: true";
			?>
			<script>
			    var ctx1 = document.getElementById('myChart1').getContext('2d');
				var myChart = new Chart(ctx1, {
				    type: 'line',
				    data: {
				        labels: [<?=$labels?>],
				        datasets: [
				        <?php 
					        foreach ($users as $objUser){
					        	$color = rand(0, 255);
					        	$g1Data = "";
				        		foreach ($objUser[3] as $objPeso){
			        				$pesoInicial = str_replace(",", ".", $objUser[2]);
			        				$pesoComparar = str_replace(",", ".", $objPeso);
			        				$g1Data.= ($pesoComparar * 100 / $pesoInicial).", ";
				        		}
				        		$g1Data = trim($g1Data, ", ");
				        ?>
				        		{
					            label: '<?=$objUser[1]?>',
					            data: [<?=$g1Data?>],
					            <?=$graph2Config?>,
					            borderColor: 'rgba(<?=$color?>, 0, 0, 1)',
					            backgroundColor: 'rgba(<?=$color?>, 225, 225, 1)'
					            }
				        <?php 
				        		if ($objUser !== end($users)) {
						        	echo ", ";
						        }
					        }
					    ?>
				        ]
				    },
				    options: {
					    scales: {
					      x: {
					        stacked: true
					      },
					      y: {
					        stacked: false
					      }
					    }
				    }
				});
			</script>
			
		<?php include("in-footer.php");?>
		
	</body>
</html>	

<?php
	mysqli_close($conMsi);
	die();
?>