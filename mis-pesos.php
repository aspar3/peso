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
	include_once 'classes/Peso.php';
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
	
	$accion = $_POST["accion"];
	if ($accion == "delete"){
		$peso = new Peso();
		$peso->setPesIduser($_SESSION["sesIduser"]);
		$peso->setPesIdpeso($_POST["id"]);
		if ($peso->delete($conMsi, $pageCode)){
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
		<title><?=$nombreGeneral." - ".sprintf(litMisPesos)?></title>
		<meta name="title" content="<?=$nombreGeneral." - ".sprintf(litMisPesos)?>">
		<meta name="verify-v1" content="iktchguQVSJTd8nwo6NGXdZ0nuE1URIv9bJN/OODK8E=" />
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?<?=rand(0, 999)?>" />
		<link rel="stylesheet" href="/css/extra.css?<?=rand(0, 999)?>" />
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
										<header>
											<h2><?=sprintf(litMisPesos)?></h2>
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
											<input type="hidden" name="id" value="save"/>
										</form>
										
										<div class="scroll">
											<table class="gen">
												<thead>
													<tr>
														<th <?=Funciones::getArrow("1", $order, $asc)?> onclick="ordenFiltro(1, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litFecha)?></th>
														<th <?=Funciones::getArrow("2", $order, $asc)?> onclick="ordenFiltro(2, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litPeso)?> (<?=$_SESSION["sesUniAbreviatura"]?>)</th>
														<th <?=Funciones::getArrow("3", $order, $asc)?> onclick="ordenFiltro(3, <?=($asc=="1"?"2":"1")?>)"><?=sprintf(litComentario)?></th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
														$labels = $g1Data1 = "";
														$i = 0;
														$peso = new Peso();
														$peso->setPesIduser($_SESSION["sesIduser"]);
														$peso->setOrder($order);
														$peso->setAsc($asc);
														foreach ($peso->getPesos($conMsi, $pageCode) as $objPeso){
															$labels.= "'".Funciones::fechaFormateadaIdioma($objPeso->getPesFecha(), $_SESSION["sesIdidioma"])."', ";
															$g1Data1.= "'".str_replace(",", ".", Funciones::pesoConvertido($objPeso->getPesPeso(), $_SESSION["sesIdunidad"], $_SESSION["sesUniMultipli"]))."', ";
													?>
														    <tr>
														      <td><?=Funciones::fechaFormateadaIdioma($objPeso->getPesFecha(), $_SESSION["sesIdidioma"])?></td>
														      <td class="number"><?=Funciones::pesoConvertido($objPeso->getPesPeso(), $_SESSION["sesIdunidad"], $_SESSION["sesUniMultipli"])?></td>
														      <td title="<?=$objPeso->getPesComent()?>" onclick="alert('<?=$objPeso->getPesComent()?>')"><?=(strlen($objPeso->getPesComent()) > 10?substr($objPeso->getPesComent(), 0, 10)."...":$objPeso->getPesComent())?></td>
														      <td class="centered">
																	<input type="image" class="tdIcon" src="/images/edit.gif" id="imageButton" title="<?=sprintf(litModificar)?>" alt="<?=sprintf(litModificar)?>" onClick="window.location.href='/nuevo-peso?idPeso=<?=$objPeso->getPesIdpeso()?>';return false;"/>
														      		<input type="image" class="tdIcon" src="/images/delete.png" id="imageButton" title="<?=sprintf(litBorrar)?>" alt="<?=sprintf(litBorrar)?>" onClick="borrar('<?=$objPeso->getPesIdpeso()?>');return false;"/>
														      </td>
														    </tr>
													<?php
															$i++;
														}
														$labels = trim($labels, ", ");
														$g1Data1 = trim($g1Data1, ", ");
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

			<?php 	
				$graph2Config = "cubicInterpolationMode: 'monotone', tension: 0.4, borderWidth: 2, spanGaps: true";
				$graph1Config = $graph2Config.", fill: true";
			?>
			<script>
			    var ctx1 = document.getElementById('myChart1').getContext('2d');
				var myChart = new Chart(ctx1, {
				    type: 'line',
				    data: {
				        labels: [<?=$labels?>],
				        datasets: [{
				            label: '<?=sprintf(litEvoluPeso)?>',
				            data: [<?=$g1Data1?>],
				            <?=$graph1Config?>,
				            borderColor: 'rgba(255, 0, 0, 1)',
				            backgroundColor: 'rgba(255, 225, 225, 1)'
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
			<?php include("in-footer.php");?>
		
	</body>
</html>	

<?php
	mysqli_close($conMsi);
	die();
?>