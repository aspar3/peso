<?php
class Funciones {

	function __construct(){
	}

	public static function just_clean($string){
		$string = strtolower($string);
		
		// Replace other special chars
		$specialCharacters = array(
				'#' => '-',
				'$' => '-',
				'%' => '-',
				'&' => '-',
				'@' => '-',
				'.' => '-',
				',' => '-',
				'€' => '-',
				'+' => '-',
				'=' => '-',
				'§' => '-',
				'\\' => '-',
				'/' => '-',
				'_' => '-',
				' ' => '-',
				'-----' => '-',
				'----' => '-',
				'---' => '-',
				'--' => '-',
		);
		
		foreach ($specialCharacters as $character => $replacement) {
			$string = str_replace($character, '' . $replacement . '', $string);
		}
		
		$string = strtr($string,
				"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ",
				"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn"
				);
		
		// Remove all remaining other unknown characters
		$string = preg_replace('/[^a-zA-Z\-]/', '', $string);
		$string = preg_replace('/^[\-]+/', '', $string);
		$string = preg_replace('/[\-]+$/', '', $string);
		$string = preg_replace('/[\-]{2,}/', '', $string);
		
		return $string;
	}
	
	
	public static function getArrow($optMenu, $order, $asc) {
		if ($optMenu==$order){
			if ($asc=="1")
				return 'class="headerSortUp"';
			else return 'class="headerSortDown"';
		}
	}
	
	public static function formatNumberToSQL($value) {
		if ($value == "") {
			$value = "null";
		} else if ($value == "&#8734;" || $value == "∞") { // infinito
			$value = "-1";
		} else {
			$value = str_replace(".", "", $value);
			$value = str_replace(",", ".", $value);
		}
		return $value;
	}
	
	public static function getSemester() {
		$mes = date('m');
		if ($mes > 6){
			return 1;
		} else {
			return 0;
		}
	}

	public static function getYearFromSqlDate($date) {
		return date('Y', strtotime($date));
	}
	
	public static function getMonthFromSqlDate($date) {
		return date('m', strtotime($date));
	}
	
	public static function textVacio2null($valor) {
		if ($valor == "" || $valor == null) {
			return "null";
		} else {
			return "'".$valor."'";
		}
	}
	
	public static function numberVacio2null($valor) {
		if ($valor == "" || $valor == null) {
			return "null";
		} else {
			return $valor;
		}
	}
	
	public static function dateVacio2null($valor) {
		if ($valor == "" || $valor == null) {
			return "null";
		} else {
			return "'".$valor."'";
		}
	}

	public static function anadirAniosFaltantes($anoInicio, $anoFin, $arrayAnadir) {
		for ($ano = $anoInicio; $ano <= $anoFin; $ano++) {
			if ($arrayAnadir[$ano - $anoInicio]["anio"] != $ano) {
				for ($j = $ano; $j < intval($arrayAnadir[$ano - $anoInicio]["anio"]) ; $j++) {
					array_splice($arrayAnadir, $ano - $anoInicio, 0, array(''));
				}
			}
		}
		return $arrayAnadir;
	}
	
	public static function formatNum0dec($valor) {
		if ($valor === 0) {
			return "0";
		}
		return number_format($valor, 0, ',', '.');
	}
	
	public static function formatNum2dec($valor) {
		if ($valor === 0) {
			return "0";
		}
// 		if ($valor == "" || $valor == null) {
// 			return "";
// 		}
		return number_format($valor, 2, ',', '.');
	}
	
	public static function pesoConvertido($valor, $unidad, $multiplicador) {
		if ($valor === 0) {
			return "0";
		}
		if ($valor == "" || $valor == null) {
			return "";
		}
		$valor = $valor / 1000;
		if ($unidad == "2") {
			$valor = $valor * $multiplicador;
			return number_format($valor, 2, '.', ',');
		} else {
			return number_format($valor, 2, ',', '.');
		}
	}
	
	public static function pesoConvertidoParaInput($valor, $unidad, $multiplicador) {
		// la diferencia con el anterior es que aqui siempre tiene que devolver con separador "." para decimales
		if ($valor === 0) {
			return "0";
		}
		if ($valor == "" || $valor == null) {
			return "";
		}
		$valor = $valor / 1000;
		if ($unidad == "2") {
			$valor = $valor * $multiplicador;
		}
		return number_format($valor, 2, '.', ',');
	}
	
	public static function formatDate($valor) {
		if ($valor == null || $valor == "") {
			return "";
		}
		$phpdate = strtotime($valor);
		return date('d/m/Y', $phpdate);
	}
	
	public static function formatDateToTxt($valor) {
		return date_format($valor, 'Y-m-d');
	}
	
	public static function fechaTxtUsToSpain($valor, $separador) {
		$fecha = explode($separador, $valor);
		return $fecha[2]."/".$fecha[1]."/".$fecha[0];
	}
	
	public static function fechaFormateadaIdioma($valor, $idioma) {
		if ($valor == "") {
			return "";
		}
		$date = new DateTime($valor);
		if ($idioma == "2") {
			return $date->format('m-d-Y');
		} else {
			return $date->format('d/m/Y');
		}
	}
	
	public static function fechaFormateadaInput($valor) {
		if ($valor == "") {
			return "";
		}
		$date = new DateTime($valor);
		return $date->format('Y-m-d');
	}
	
	public static function dateToIcs($dateTime) {
		return str_replace(" ", "T", str_replace(":", "", str_replace("-", "", $dateTime)));
	}
	
	public static function isweekend($date){
		if(date('w', strtotime($date)) == 6 || date('w', strtotime($date)) == 0) {
			return true;
		}
		return false;
	}
	
	public static function isweekendTxt($date){
		$date = strtotime($date);
		$date = date("l", $date);
		$date = strtolower($date);
		if($date == "saturday" || $date == "sunday") {
			return true;
		} else {
			return false;
		}
	}
	
	public static function vacacionesType($type){
		if($type == "sick") {
			return "baja";
		} else if($type == "vacation") {
			return "vacaciones";
		}
	}
	
	public static function getNewCode(){
		$str = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789";
		$codValida = "";for($iVal=0;$iVal<15;$iVal++) {$codValida .= substr($str,rand(0,57),1);}
		return $codValida;
	}
	
	public static function getIsoWeeksWithStartDates($startDate, $endDate): array {
		$start = $startDate instanceof \DateTime ? clone $startDate : new \DateTime($startDate);
		$end   = $endDate instanceof \DateTime ? clone $endDate : new \DateTime($endDate);
		
		// Normalize time
		$start->setTime(0, 0, 0);
		$end->setTime(0, 0, 0);
		
		// Move start to the Monday of its ISO week
		$start->setISODate((int)$start->format('o'), (int)$start->format('W'));
		
		$result = [];
		
		$current = clone $start;
		while ($current <= $end) {
			$yearIso  = (int)$current->format('o'); // ISO year
			$weekIso  = (int)$current->format('W'); // ISO week (01-53)
			
			$startOfWeek = clone $current; // Monday of this ISO week
			
			$result[] = [
					'year' => $yearIso,
					'week' => $weekIso,
					'start_of_week' => $startOfWeek->format('Y-m-d')
			];
			
			// Next week (Monday)
			$current->modify('+1 week');
		}
		
		return $result;
	}
}
?>