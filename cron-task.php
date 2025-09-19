<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// las 12:45 en el cron de dentalguia son las 18:45 en Getxo
echo "The time is ".date("Y/m/d")." ". date("h:i:sa")."<br>";

include("admin/in_variables.php");
require_once("in_func_mail.php");
include_once 'classes/User.php';
include_once 'classes/Idioma.php';
include_once 'classes/Peso.php';
include_once 'classes/GrupoUser.php';

$pageCode = "CTK";

$conMsi= crearConexionMysqli();

$idioma = "";
if (isset($_GET["idioma"])) {
	$idioma = $_GET["idioma"];
}

$idiomaURL = "";
$idiomaTxt = "es";
if ($idioma != "" && $idioma != "es") {
	$idiomaTxt = $idioma;
	$idiomaURL = "/".$idioma;
}
include_once 'literales/idioma_'.$idiomaTxt.'.php';

$mailsJuntos = "";
$algunMailEnviado = false;
$usuarioAnterior = null;
$usuarioActual;
$idUsuarioUltimo = "";
$mailUsuarioUltimo = "";
$subjectUser = $nombreGeneral.": ".sprintf(litMailRecordatorioSubject);
$bodyUser = "";
$algunGrupoParaAvisar = false;
$user = new User();
foreach ($user->getUsersMailPendientes($conMsi, $pageCode, $idiomaTxt) as $objUser) {
	//echo $objUser["USE_NAME"]."<br>";
	
	$usuarioActual = $objUser['USE_IDUSER'];	
	if ($usuarioActual !== $usuarioAnterior) {
		if ($usuarioAnterior !== null && $algunGrupoParaAvisar) {
			// $usuarioAnterior !== null para que no se haga la primera vez
			// RECORDAR QUE ESTE TROZO SE PONE TAMBIEN AL FINAL DEL FOREACH
			$bodyUser.="\n".sprintf(litMailRecordatorio02)."\n".
					$accesoHttp.$rootURL."/login".$idiomaURL."\n\n".
					sprintf(litAtentamente)."\n".
					$nombreGeneral.": ".$accesoHttp.$rootURL.$idiomaURL;
					
			echo "mailUser: ".$mailUsuarioUltimo."<br>";
			echo "bodyUser: ".$bodyUser."<br>";
			if ($enviarMails) enviarMailSMTP($mailAdmin, $mailUsuarioUltimo, "", "", $subjectUser, $bodyUser, $usuarioAnterior);
			$algunMailEnviado = true;
			$mailsJuntos.=$bodyUser."\n\n\n";
		}
		
		// echo "Cambio de usuario detectado: de {$usuarioAnterior} a {$usuarioActual}\n";
		$mailUsuarioUltimo = $objUser["USE_MAIL"];
		$bodyUser = sprintf(litEstimado, $objUser["USE_NAME"])."\n\n".
				sprintf(litMailRecordatorio01)."\n";
		$algunGrupoParaAvisar = false;
	}
	
	$peso = new Peso();
	$peso->setPesIduser($usuarioActual);
	if ($peso->checkRetrasoPeso($conMsi, $pageCode, $objUser["TIE_PALABRA_MYSQL"])) {
		$grupoUser = new GrupoUser();
		$grupoUser->setGusIdgrupo($objUser["GUS_IDGRUPO"]);
		$grupoUser->setGusIduser($usuarioActual);
		$grupoUser->getGrupoUser($conMsi, $pageCode);
		if ($grupoUser->getGusAvisoRetraso() != "S") {
			// solo se manda si no se ha enviado antes
			$algunGrupoParaAvisar = true;
			$bodyUser.= " - ".$objUser["GRU_NOMBRE"]."\n";
			$grupoUser->setGusAvisoRetraso("S");
			$grupoUser->updateAvisoRetrasoUserGrupo($conMsi, $pageCode);
		}
	}
			
	$usuarioAnterior = $usuarioActual;
}

if ($algunGrupoParaAvisar) {
	// para el ultimo usuario
	$bodyUser.="\n".sprintf(litMailRecordatorio02)."\n".
			$accesoHttp.$rootURL."/login".$idiomaURL."\n\n".
			sprintf(litAtentamente)."\n".
			$nombreGeneral.": ".$accesoHttp.$rootURL.$idiomaURL;
			
	echo "mailUser: ".$mailUsuarioUltimo."<br>";
	echo "bodyUser: ".$bodyUser."<br>";
	if ($algunGrupoParaAvisar) {
		if ($enviarMails) enviarMailSMTP($mailAdmin, $mailUsuarioUltimo, "", "", $subjectUser, $bodyUser, $usuarioAnterior);
		$algunMailEnviado = true;
		$mailsJuntos.=$bodyUser."\n\n\n";
	}
}
		
//echo "The time is ".date("Y/m/d")." ". date("h:i:sa");
if ($algunMailEnviado && $enviarMails) {
	enviarMailSMTP($mailAdmin, $mailAlertasAdmin, "", "", $nombreGeneral.": Cron ejecutado con avisos enviados", $mailsJuntos, "");
}

// $peso = new Peso();
// $peso->setPesIduser("14");
// $peso->setPesPeso("115");
// $peso->setPesFecha(date("Y-m-d"));
// $peso->setPesComent(date("Y/m/d")." ". date("h:i:sa")."\n".$mailsJuntos);
// $peso->insert($conMsi, $pageCode);


mysqli_close($conMsi);
die(); 
?>