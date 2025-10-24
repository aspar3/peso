<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// las 12:45 en el cron de dentalguia son las 18:45 en Getxo
echo "The time is ".date("Y/m/d")." ". date("h:i:sa")."<br>";

include("admin/in_variables.php");
require_once("in_func_mail.php");
include_once 'classes/User.php';
include_once 'classes/Idioma.php';
include_once 'classes/Peso.php';
include_once 'classes/GrupoUser.php';
include_once 'classes/Grupo.php';
include_once 'classes/GrupoUserDato.php';

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
$usuarioAnterior = null;
$mailUsuarioUltimo = "";
$subjectUser = $nombreGeneral.": ".sprintf(litMailRecordatorioSubject);
$bodyUser = "";
$algunMailEnviado = false;

$user = new User();
foreach ($user->getDistinctUsersMailPendientes($conMsi, $pageCode, $idiomaTxt) as $objUser) {
// 	echo $objUser["GUS_IDUSER"]."<br>";

	$algunGrupoParaAvisar = false;
	
	$bodyUser = sprintf(litEstimado, $objUser["USE_NAME"])."\n\n".
				sprintf(litMailRecordatorio01)."\n\n";
				sprintf(litMailRecordatorio02)."\n\n";
			
	// INICIO grupos PESO
	foreach ($user->getUsersMailPendientes($conMsi, $pageCode, $objUser["GUS_IDUSER"], "1") as $objGruposPeso) {
		$peso = new Peso();
		$peso->setPesIduser($objUser["GUS_IDUSER"]);
		if ($peso->checkRetrasoPeso($conMsi, $pageCode, $objGruposPeso["TIE_PALABRA_MYSQL"])) {
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIdgrupo($objGruposPeso["GUS_IDGRUPO"]);
			$grupoUser->setGusIduser($objUser["GUS_IDUSER"]);
			$grupoUser->getGrupoUser($conMsi, $pageCode);
			if ($grupoUser->getGusAvisoRetraso() != "S") {
				// solo se manda si no se ha enviado antes
				$algunGrupoParaAvisar = true;
				$bodyUser.= " - ".$objGruposPeso["GRU_NOMBRE"]."\n";
				$grupoUser->setGusAvisoRetraso("S");
				$grupoUser->updateAvisoRetrasoUserGrupo($conMsi, $pageCode);
			}
		}
	}
	if ($algunGrupoParaAvisar) {
		$bodyUser.= $accesoHttp.$rootURL."/nuevo-peso\n\n";
	}
	// FIN grupos PESO

	// INICIO grupos OTROS
	foreach ($user->getUsersMailPendientes($conMsi, $pageCode, $objUser["GUS_IDUSER"], "") as $objGruposPeso) {
		$grupoUserDato = new GrupoUserDato();
		$grupoUserDato->setGudIduser($objUser["GUS_IDUSER"]);
		$grupoUserDato->setGudIdgrupo($objGruposPeso["GUS_IDGRUPO"]);
		if ($grupoUserDato->checkRetrasoGrupoUserDato($conMsi, $pageCode, $objGruposPeso["TIE_PALABRA_MYSQL"])) {
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIdgrupo($objGruposPeso["GUS_IDGRUPO"]);
			$grupoUser->setGusIduser($objUser["GUS_IDUSER"]);
			$grupoUser->getGrupoUser($conMsi, $pageCode);
			if ($grupoUser->getGusAvisoRetraso() != "S") {
				// solo se manda si no se ha enviado antes
				$algunGrupoParaAvisar = true;
				$urlNuevoDato = "/otros-nuevo-dato";
				if ($objGruposPeso["GRU_TIPO"] == "3") {
					$urlNuevoDato = "/acciones-nuevo-dato";
				}
				$bodyUser.= " - ".$objGruposPeso["GRU_NOMBRE"]."\n".
							$accesoHttp.$rootURL.$urlNuevoDato."/".$objGruposPeso["GUS_IDGRUPO"]."\n\n";
				$grupoUser->setGusAvisoRetraso("S");
				$grupoUser->updateAvisoRetrasoUserGrupo($conMsi, $pageCode);
			}
		}
	}
	// FIN grupos OTROS
	
	$bodyUser.=sprintf(litAtentamente)."\n".
			$nombreGeneral.": ".$accesoHttp.$rootURL.$idiomaURL;
	
	if ($algunGrupoParaAvisar) {
		if ($enviarMails) enviarMailSMTP($mailAdmin, $objUser["USE_MAIL"], "", "", $subjectUser, $bodyUser, $objUser["GUS_IDUSER"]);
		$algunMailEnviado = true;
		$mailsJuntos.=$bodyUser."\n\n\n";
	}
	// echo $mailsJuntos;
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

// ESTO HABRIA QUE HACERLO, PERO DE MOMENTO NO PORQUE AL PONERLOS COMO INACTIVOS NO SALDRIAN EN LA PANTALLA DE GRUPOS
// EN REALIDAD, DEBEN SALIR EN LA PANTALLA PERO QUE YA NO SE PUEDA HACER NADA CON ELLOS EXCEPTO CONSULTAR
// $grupo = new Grupo();
// $grupo->desactivarGrupoPorFechafin($conMsi, $pageCode);

mysqli_close($conMsi);
die(); 
?>