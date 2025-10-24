<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
	session_start();
	$pageCode = "OMLI";
	
	include("admin/in_variables.php");
	include("in_www.php");
	require_once("in_func_mail.php");

	include("in_idiom.php");
	
	include_once 'classes/Unidad.php';
	include_once 'classes/Peso.php';
	include_once 'classes/Grupo.php';
	include_once 'classes/GrupoUser.php';
	include_once 'classes/GrupoUserDato.php';

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
	
	$order=$_GET["order"]==""?"1":$_GET["order"];
	$asc=$_GET["asc"]==""?"2":$_GET["asc"];
	
	$conMsi= crearConexionMysqli();

	// 1: Peso. 2: Otros. 3: Acciones.
	// $gruTipo = "2";
	
	$conMsi= crearConexionMysqli();
	
	$idGrupo = $_GET["idGrupo"];
	
	$grupoSelect = new Grupo();
	$grupoSelect->setGruTipo($gruTipo);
	$grupoSelect->setGruIduser($_SESSION["sesIduser"]);
	$listGruposAceptados = $grupoSelect->getGruposAceptados($conMsi, $pageCode);
	// Si solo esta en un grupo, que directamente salga seleccionado
	if ($idGrupo == "" && count($listGruposAceptados) == 1) {
		$idGrupo = $listGruposAceptados[0]->getGruIdgrupo();
	}
	
	$grupo = new Grupo();
	if ($idGrupo != "") {
		$grupo->setGruIdgrupo($idGrupo);
		$grupo->setGruIduser($_SESSION["sesIduser"]);
		if (!$grupo->getGrupo($conMsi, $pageCode)) {
			die;
		}
	}
	
	$grupoUserDato = new GrupoUserDato();
	
	$accion = $_POST["accion"];
	if ($accion == "delete"){
		$grupoUserDato = new GrupoUserDato();
		$grupoUserDato->setGudIduser($_SESSION["sesIduser"]);
		$grupoUserDato->setGudIdgrupo($grupo->getGruIdgrupo());
		$grupoUserDato->setGudIdgud($_POST["id"]);
		if ($grupoUserDato->delete($conMsi, $pageCode)){
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIduser($_SESSION["sesIduser"]);
			$grupoUser->setGusAvisoRetraso("N");
			if ($grupoUser->updateAvisoRetrasoUser($conMsi, $pageCode)) {
				$mensaje1=sprintf(litCambiosOk);
				$classMsgBox = "msgBox bgGreen txtBlack";
			}else{
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
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?=$nombreGeneral." - ".sprintf(litMenuMisDatos)?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litMenuMisDatos)?>">
		<meta name="verify-v1" content="iktchguQVSJTd8nwo6NGXdZ0nuE1URIv9bJN/OODK8E=" />
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 9999999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 9999999)?>" />
		<script src="/js/funciones.js?<?=rand(0, 9999999)?>"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.3.0/chart.min.js"></script>
		
		<script type="text/javascript">
			function borrar(id) {
				if (confirm("<?=sprintf(litConfirmElimPeso)?>")){
					formularioBorrar.id.value = id;
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
										<form name="formularioBorrar" method="post">
											<input type="hidden" name="accion" value="delete"/>
											<input type="hidden" name="id" value="save"/>
										</form>
										<form name="formulario" method="get">
											<input type="hidden" name="accion" value="save"/>
											<input type="hidden" name="id" value="save"/>
											<input type="hidden" name="order" value="<?=$order?>"/>
											<input type="hidden" name="asc" value="<?=$asc?>"/>
										<header>
											<h2><?=sprintf(litMenuMisDatos)?></h2>
											<div>
												<label class="desc" for="idGrupo"><?=($idGrupo==""?sprintf(litPrimeroGrupo):sprintf(litGrupo))?> <span class="txtRed">*</span></label>
												<div>
													<select id="idGrupo" name="idGrupo" onchange="window.location.href='/otros-mis-datos.php?idGrupo=' + this.value">
														<option value=""></option>
														<?php
															foreach ($listGruposAceptados as $objGrupo) {
																echo "<option ".($objGrupo->getGruIdgrupo() == $idGrupo?"selected":"")." value = '".$objGrupo->getGruIdgrupo()."'>".$objGrupo->getGruNombre()."</option>";
															}
														?>
													</select>
												</div>
											</div>
											<div></div>
										</header>
										</form>
										<?php if ($idGrupo != "") { ?>
												<br>
												<?php echo $grupo->getGruPregunta();?>						
												<div class="scroll">
													<table class="gen">
														<thead>
															<tr>
																<th <?=Funciones::getArrow("1", $order, $asc)?> onclick="ordenFiltro(1, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litFecha)?></th>
																<th <?=Funciones::getArrow("2", $order, $asc)?> onclick="ordenFiltro(2, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litDato)?></th>
																<th <?=Funciones::getArrow("3", $order, $asc)?> onclick="ordenFiltro(3, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litComentario)?></th>
																<th></th>
															</tr>
														</thead>
														<tbody>
															<?php
																$grupoUserDato = new GrupoUserDato();
																$grupoUserDato ->setGudIduser($_SESSION["sesIduser"]);
																$grupoUserDato ->setGudIdgrupo($idGrupo);
																$grupoUserDato->setOrder($order);
																$grupoUserDato->setAsc($asc);
																foreach ($grupoUserDato->getGrupoUserDatos($conMsi, $pageCode, "") as $objDato){																	
																	$datoMostrar = $objDato->getGudDato();
																	if ($grupo->getGruIdRespuesta() == "1") {
																		$datoMostrar = Funciones::formatNum2dec($datoMostrar);
																	}
															?>
																    <tr onclick="detalleLinea(<?=$objDato->getGudIdgud()?>)">
																      <td><?=Funciones::fechaFormateadaIdioma($objDato->getGudFecha(), $_SESSION["sesIdidioma"])?></td>
																      <td class="number"><?=$datoMostrar?></td>
																      <td <?php if ($objDato->getGudComent() !="") { echo 'title="'.$objDato->getGudComent().'" onclick="alert(\''.$objDato->getGudComent().'\')"';}?>><?=(strlen($objDato->getGudComent()) > 10?substr($objDato->getGudComent(), 0, 10)."...":$objDato->getGudComent())?></td>
																      <td class="centered">
																			<input type="image" class="tdIcon" src="/images/flechaDetalle.gif" id="imageButton" title="<?=sprintf(litVerOpciones)?>" alt="<?=sprintf(litVerOpciones)?>" onClick="detalleLinea(<?=$objDato->getGudIdgud()?>)';return false;"/><br>
																      </td>
																    </tr>
																    <tr class="oculto"></tr> <!-- para mantener los estilos de las filas de las tablas pares e impares -->
																    <tr class="ocultoFila" id="linea_<?=$objDato->getGudIdgud()?>">
																    	<td colspan="5">
																			<button class="botonTabla" onClick="window.location.href='/otros-nuevo-dato.php?idGrupo=<?=$idGrupo?>&idGud=<?=$objDato->getGudIdgud()?>';">
																				<img src="/images/edit.gif" class="imageButton">
																				<span><?=sprintf(litModificar)?></span>
																			</button>
																			<button class="botonTabla" onClick="borrar('<?=$objDato->getGudIdgud()?>')">
																				<img src="/images/delete.png" class="imageButton">
																				<span><?=sprintf(litBorrar)?></span>
																			</button>
																		</td>
																    </tr>
															<?php
																}
																$labels = $g1Data1 = "";
																foreach ($grupoUserDato->getGrupoUserDatos($conMsi, $pageCode, "asc") as $objDato){
																	$labels.= "'".Funciones::fechaFormateadaIdioma($objDato->getGudFecha(), $_SESSION["sesIdidioma"])."', ";
																	$g1Data1.= "'".str_replace(",", ".", $objDato->getGudDato())."', ";
																}
																
																if ($labels !== "") { $labels = substr($labels, 0, -1);}
																if ($g1Data !== "") { $g1Data = substr($g1Data, 0, -1);}
															?>
														</tbody>
													</table>
												</div>
										<?php }?>
									</section>
								</div>
							</div>
							<?php if ($idGrupo != "") { ?>										
									<div class="graphs">
									    <div class="graph100">
											<canvas id="myChart1" width="870" height="435" style="display: block; width: 870px; height: 435px;"></canvas>
										</div>
									</div>
							<?php }?>
								<div>
									<div>
								  		<br>
						  				<?php
							  				$urlNuevoDato = "/otros-nuevo-dato.php";
							  				if ($grupo->getGruTipo() == "3") {
							  					$urlNuevoDato = "/acciones-nuevo-dato.php";
						  					}
						  				?>
						  				<input class="button" id="saveForm" name="saveForm" type="submit" onclick="window.location.href='<?php echo $urlNuevoDato?>?idGrupo=<?=$grupo->getGruIdgrupo()?>'" value="<?=sprintf(litMenuNuevoDato)?>">&nbsp;
						  				<input class="button" id="volver" name="volver" type="button" onclick="window.location.href='/otros-mis-grupos.php'" value="<?=sprintf(litVolver)?>">
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

			<?php if ($idGrupo != "") {										
					$graph2Config = "cubicInterpolationMode: 'monotone', tension: 0.4, borderWidth: 2, spanGaps: true";
					$graph1Config = $graph2Config.", fill: true";
					$color1 = rand(0, 255);
					$color2 = rand(0, 255);
					$color3 = rand(0, 255);
			?>
			<script>
			    var ctx1 = document.getElementById('myChart1').getContext('2d');
				var myChart = new Chart(ctx1, {
				    type: 'line',
				    data: {
				        labels: [<?=$labels?>],
				        datasets: [{
				            label: '<?=sprintf(litEvoluDatos)?>',
				            data: [<?=$g1Data1?>],
				            <?=$graph2Config?>,
				            borderColor: 'rgba(<?=$color1?>, <?=$color2?>, <?=$color3?>, 1)',
				            backgroundColor: 'rgba(<?=$color1?>, <?=$color2?>, <?=$color3?>, 1)'
				        }]
				    },
				    options: {
					    scales: {
					      x: {
					        stacked: true,
					      },
					      y: {
					        stacked: true
					      }
					    }
				    }
				});
			</script>
			<?php }?>
			<?php include("in-footer.php");?>
		
	</body>
</html>	

<?php
	mysqli_close($conMsi);
	die();
?>