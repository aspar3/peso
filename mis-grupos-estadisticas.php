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
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.3.0/chart.min.js"></script>
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
										<header>
											<h2><?=sprintf(litEstadGrupo, $grupo->getGruNombre())?></h2>
										</header>
										<div>
											<div>
										  		<?php
											  		$grupoUser = new GrupoUser();
											  		$grupoUser->setGusIdgrupo($idGrupo);
											  		$grupoUser->setOrder($order);
											  		$grupoUser->setAsc($asc);
											  		
											  		// crear array, que tendra lo siguiente en las posiciones:
											  		// 0) id usuario
											  		// 1) nombre usuario
											  		// 2) peso inicial
											  		// 3) array con los pesos
											  		$users = [];
											  		$fila = 0;
											  		foreach ($grupoUser->getGrupoUsers($conMsi, $pageCode) as $objGrupoUser){
											  			$users[$fila][0] = $objGrupoUser->getGusIduser();
											  			$users[$fila][1] = $objGrupoUser->getUseName();
											  			$users[$fila][3] = array();
											  			$fila++;
											  		}
											  			
										  			$labels = "";
										  			$start = $grupo->getGruFecini();
										  			$end   = $grupo->getGruFecfin();
													$hoy = date('Y-m-d');
													if ($end > $hoy) { $end = $hoy;}											  		
											  		$weeks = Funciones::getIsoWeeksWithStartDates($start, $end);
											  		foreach ($weeks as $week) {
											  			//echo $week['year'] . '-W' . sprintf('%02d', $week['week']) . " starts on " . $week['start_of_week'];
											  			$labels.= "'".Funciones::fechaFormateadaIdioma($week['start_of_week'], $_SESSION["sesIdidioma"])."', ";
											  			
											  			$grupoUserSemana = new GrupoUser();
											  			$grupoUserSemana->setGusIdgrupo($grupo->getGruIdgrupo());
											  			foreach ($grupoUserSemana->getGrupoUsersPeso($conMsi, $pageCode, $week['year'], $week['week']) as $objUserSemana) {
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
											  		$labels = trim($labels, ", ");
										  		?>
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
									</section>
								</div>
							</div>
							<div class="graphs">
							    <div class="graph100">
									<canvas id="myChart1" width="870" height="435" style="display: block; width: 870px; height: 435px;"></canvas>
								</div>
							    <div class="graph100">
									<canvas id="myChart2" width="870" height="435" style="display: block; width: 870px; height: 435px;"></canvas>
								</div>
								<div>
							  		<br><input class="button" id="volver" name="volver" type="button" onclick="history.back();" value="<?=sprintf(litVolver)?>">
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
				<?php if ($grupo->getGruMostrarPeso() == "S") {?>
						    var ctx2 = document.getElementById('myChart2').getContext('2d');
							var myChart = new Chart(ctx2, {
							    type: 'line',
							    data: {
							        labels: [<?=$labels?>],
							        datasets: [
							        <?php 
								        foreach ($users as $objUser){
								        	$color = rand(0, 255);
								        	$g1Data = "";
							        		foreach ($objUser[3] as $objPeso){
						        				$g1Data.= str_replace(",", ".", $objPeso).", ";
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
				<?php }?>
			</script>
			
		<?php include("in-footer.php");?>
		
	</body>
</html>	

<?php
	mysqli_close($conMsi);
	die();
?>