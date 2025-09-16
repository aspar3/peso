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
		<title><?=$nombreGeneral?> - Condiciones de uso</title>
		<meta name="title" content="<?=$nombreGeneral?> - Condiciones de uso">
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
							
						<?php include("in-menu.php");?>

					</div>
					<div>
							<br><b>DERECHOS RESERVADOS</b> 
							<br> 
							<br> 
							El objetivo del presente documento es informar a visitantes y usuarios de esta web de las normas generales que rigen para su utilización y consulta.							<br> 
							<br> 
							Los visitantes y usuarios aceptan plenamente y sin reservas las condiciones expuestas en este documento y se  comprometen a utilizar esta web y los servicios que se ofrecen de conformidad con la ley, así como con la moral y buenas costumbres generalmente aceptadas y el orden público.							<br> 
							<br> 
							<?=$nombreGeneral?> se reserva el derecho de modificar las condiciones legales que se exponen en este documento.							<br> 
							<br> 
							<?=$nombreGeneral?> se reserva la facultad de efectuar, en cualquier momento y sin previo aviso, modificaciones en la configuración y presentación de su Web.							<br> 
							<?=$nombreGeneral?> no es responsable de ningún tipo de material expuesto en la web, es responsabilidad de quien lo aporta, los propios anunciantes. 
							<br> 
							<br> 
							El acceso a <?=$nombreGeneral?> es totalmente voluntario y bajo la responsabilidad de quién lo realiza. Por ello, <?=$nombreGeneral?> no se hace cargo de ninguna consecuencia, daño o perjuicio derivado de dicho acceso o del uso de la información que aparezca en las páginas web.							<br> 
							<br> 
							Queda prohibida, salvo autorización previa, expresa y por escrito, la transmisión, cesión, venta, alquiler y/o exposición pública de esta web o cualquier parte de la misma.							<br> 
							<br> 
							<?=$nombreGeneral?> no se  hace  responsable de la veracidad de los contenidos ofrecidos por los anunciantes ni de los contenidos de sus sitios web, así como de cualquier cambio de la información ofrecida en nuestro directorio de productos y servicios y en nuestras secciones informativas.							<br> 
							<br> 
							<?=$nombreGeneral?> no se responsabiliza de los eventuales daños y perjuicios que puedan ocasionarse por la falta de disponibilidad y/o continuidad de esta web y de los servicios que se ofrecen en ella. En casos de haber algun acuerdo para mostrar la informacion destacada, si esta web tiene una discontinuidad definitiva, se devolveran las partes proporcionales de las cuotas correspondientes.							<br> 
							<br> 
							<?=$nombreGeneral?> no garantiza la ausencia de virus u otros elementos que puedan producir alteraciones en su sistema informático y declina cualquier responsabilidad contractual o extracontractual con la empresa que tuviera perjuicios en este sentido.							<br> 
							<br> 
							<?=$nombreGeneral?> declina cualquier responsabilidad por los servicios y/o información que eventualmente pudieran prestarse en la web por parte de terceros ya que no ejerce ningún tipo de supervisión sobre los mismos, aconsejando a los visitantes y usuarios de las mismas a consultar las condiciones legales expuestas en dichas webs.							<br> 
							<br> 
							Las empresas o personas que remitan cualquier tipo de información a <?=$nombreGeneral?> se comprometen a que sea veraz y no vulnere la legalidad vigente o cualquier derecho de terceros.							<br> 
							<br>
							<b>POLÍTICA DE PRIVACIDAD</b>
							<br>
							<br>
							 Sus datos serán incluidos en la base de datos de <?=$nombreGeneral?>, cuyo código de inscripción en la AGPD es el 2083090492							 Según LEY ORGÁNICA 15/1999, del 13 de diciembre de protección de datos de carácter personal, usted tiene derecho a acceder a la información que le concierne, recopilada en nuestro fichero de clientes y cancelarla o ratificarla de ser errónea, a través de esta misma web o por email.<br>
							<br>
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

	</body>
</html>
