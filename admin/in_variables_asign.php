<?php
	/*
	$conPar = crearConexionMysqli();

	$sqlPar="SELECT PAR_NAME, PAR_VALUE FROM PARAMETER";
	$resultPar= $conPar->query($sqlPar);
	$iVar = 0;
	while($rowPar=$resultPar->fetch_assoc()){
  		$valor = $rowPar["PAR_VALUE"];
  		if (strtoupper($valor) == "TRUE")
  			$valor = true;
  		else if (strtoupper($valor) == "FALSE")
  		  	$valor = false;
  		else $valor = $valor;
  		
  		${$rowPar["PAR_NAME"]} = $valor;
  	}

  	mysqli_close($conPar);
  	*/
  	$accesoHttp = "https://";
  	$mailHost = "mail.challenges.group";
  	$mailPort = "290";
  	$mailPortSsl = "465";
  	$mailUser = "info@challenges.group";
  	$mailPass = "!ffV^mf#mJks!md?";
  	$mailAdmin = "info@challenges.group";
  	$mailAlertasAdmin = "alertas@psicologosensantodomingo.com"; 
  	$codigoAbrev = "PSG";
  	$nombreGeneral = "Peso (Reto grupal)";
  	$rootURL = "challenges.group";
  	
  	
	$urlFull = "$accesoHttp$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	
	function crearConexion(){
		global $bbdd_host;
		global $bbdd_name;
		global $bbdd_pass;
		global $bbdd_user;
		global $offset;
		
		$numAle = rand(1,100000);
		
		${"conexionCC".$numAle} = mysql_connect($bbdd_host,$bbdd_user,$bbdd_pass);
		mysql_select_db($bbdd_name, ${"conexionCC".$numAle});
		//mysql_query("SET time_zone='$offset'",${"conexionCC".$numAle});
		
		return ${"conexionCC".$numAle};
	}

	function crearConexionMysqli(){
		global $bbdd_host;
		global $bbdd_name;
		global $bbdd_pass;
		global $bbdd_user;
		global $offset;
		
		// Connect to the database
		$conn = new mysqli($bbdd_host, $bbdd_user, $bbdd_pass, $bbdd_name);
		if($conn->connect_error){
			die("Failed to connect with MySQL: " . $conn->connect_error);
		}else{
			return $conn;
		}
	}
	
	
	// Appends lines to file and makes sure the file doesn't grow too much
	function rolLog($code, $text, $level) {
		
		global $urlFull, $debug, $logFile;
		global $nombreGeneral, $mailAdmin, $mailAlertasAdmin, $codigoAbrev;
		
		$text = "[".date("Y-m-d h:i:s A")."] [".$_SERVER['REMOTE_ADDR']."] $urlFull: Error $code: $text";
		$headers = "From: $nombreGeneral <".$mailAdmin.">\r\n"."X-Mailer: php";
		
		if ($debug)
			echo $text;
		else{
			if ($level>=3){
				$text.= ". Mail sent to $mailAlertasAdmin:".mail($mailAlertasAdmin, "$codigoAbrev: Error", $text, $headers);
			}
			
			if (!file_exists($logFile)) { touch($logFile); chmod($logFile, 0666); }
			if (filesize($logFile) > 2*1024*1024) {
				$logFile2 = "$logFile.old";
				if (file_exists($logFile2)) unlink($logFile2);
				rename($logFile, $logFile2);
				touch($logFile); chmod($logFile,0666);
			}
			
			if (!is_writable($logFile)){
				if (mail($mailAlertasAdmin, "$codigoAbrev: Error", $text."\nCannot open log file ($logFile)", $headers));
				else echo "<p>\nCannot open log file ($logFile)</p>";
				die();
			}
			if (!$handle = fopen($logFile, 'a')){
				if (mail($mailAlertasAdmin, "$codigoAbrev: Error", $text."\nCannot open file ($logFile)", $headers));
				else echo "<p>\nCannot open file ($logFile)</p>";
				die();
			}
			if (fwrite($handle, $text."\n") === FALSE){
				if (mail($mailAlertasAdmin, "$codigoAbrev: Error", $text."\nCannot write to file ($logFile)", $headers));
				else echo "<p>\nCannot write to file ($logFile)</p>";
				die();
			}
			fclose($handle);
			// only interrupt execution if error level is bigger or equals to 3
			if ($level>=3) die("<p>\nERROR: $code</p>");
		}		
	}
	
	function dateToIcs($dateTime) {
		return str_replace(" ", "T", str_replace(":", "", str_replace("-", "", $dateTime)));
	}
?>