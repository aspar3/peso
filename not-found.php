<?php
$pageCode = "NOF";
include("admin/in_variables.php");
include("in_www.php");

include("in_idiom.php");

header("HTTP/1.0 404 Not Found");
header("Status: 404 Not Found");
?>
<!DOCTYPE HTML>
<html lang="es" translate="no">
	<head>
		<title><?=$nombreGeneral?> - No encontrado</title>
		<meta name="title" content="<?=$nombreGeneral?> - No encontrado">
		<?php include("in-metas.php");?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="/assets/css/main.css?878" />
		<link rel="stylesheet" href="/css/extra.css" />
		<script src="/js/index.js"></script>
	</head>
	<body class="homepage is-preload">
		<div id="page-wrapper">

			<!-- Header -->
				<section id="header">
					<div class="container">

						<!-- Logo -->
							<h1 id="logo"><a href="index.php"><img src="/images/logo.png" alt="<?=$nombreGeneral?>"></a></h1>
							<p>Una forma de vida. Una forma de comer.</p>
							<h2>:-(</h2>
							<span>Lo sentimos, no hemos encontrado la página que busca</span>
							
						<?php include("in-menu.php");?>

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
