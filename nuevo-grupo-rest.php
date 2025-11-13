<?php
session_start();
$pageCode = "NGR-REST";

include("admin/in_variables.php");
include("in_www.php");
require_once("in_func_mail.php");

include("in_idiom.php");

include_once 'classes/Funciones.php';
include_once 'classes/User.php';
include_once 'classes/GrupoUser.php';

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

$conMsi= crearConexionMysqli();

$method = $_SERVER['REQUEST_METHOD'];

// Detectar recurso solicitado (ejemplo: api.php?resource=usuarios)
$resource = isset($_GET['resource']) ? $_GET['resource'] : null;

switch ($method) {
	case 'PUT':
		$input = json_decode(file_get_contents("php://input"), true);
		if (isset($input["idGrupo"])) {
			$grupoUser = new GrupoUser();
			$grupoUser->setGusIduser($_SESSION["sesIduser"]);
			$grupoUser->setGusIdgrupo($input["idGrupo"]);
			$grupoUser->setGusAvisosMail($input["avisosMail"]);
			if ($input["avisosMail"] == "S") {
				$grupoUser->setGusAvisosCode(User::getNewCode());				
			}
			if ($grupoUser->updateAvisosMail($conMsi, $pageCode)) {
				echo json_encode(["status" => "OK", "msg" => sprintf(litCambiosOk)]);
			} else {
				echo json_encode(["status" => "KO", "msg" => sprintf(litError1)]);
			}
		}
		break;
	default:
		echo json_encode(["status" => "KO", "error" => sprintf(litMetodoNoSoportado)]);
		break;
}

mysqli_close($conMsi);
die();
?>