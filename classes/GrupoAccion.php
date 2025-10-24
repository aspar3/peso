<?php
include_once 'classes/Funciones.php';

class GrupoAccion {
	//private $dbHost     = "localhost";
	//private $dbUsername = "root";
	//private $dbPassword = "";
	//private $dbName     = "codexworld";
	private $tbl    = 'GRUPO_ACCION';

	private $gacIdaccion;
	private $gacIdgrupo;
	private $gacNombre;
	private $gacValor;
	private $gacUsucre;
	private $gacFeccre;
	
	private $order;
	private $asc;
	
	function __construct(){
	}
	

	public function getGacIdaccion() {
		return $this->gacIdaccion;
	}

	public function setGacIdaccion($gacIdaccion) {
		$this->gacIdaccion = $gacIdaccion;
	}

	public function getGacIdgrupo() {
		return $this->gacIdgrupo;
	}

	public function setGacIdgrupo($gacIdgrupo) {
		$this->gacIdgrupo = $gacIdgrupo;
	}

	public function getGacNombre() {
		return $this->gacNombre;
	}

	public function setGacNombre($gacNombre) {
		$this->gacNombre = $gacNombre;
	}

	public function getGacValor() {
		return $this->gacValor;
	}

	public function setGacValor($gacValor) {
		$this->gacValor = $gacValor;
	}

	public function getGacUsucre() {
		return $this->gacUsucre;
	}

	public function setGacUsucre($gacUsucre) {
		$this->gacUsucre = $gacUsucre;
	}

	public function getGacFeccre() {
		return $this->gacFeccre;
	}

	public function setGacFeccre($gacFeccre) {
		$this->gacFeccre = $gacFeccre;
	}

	public function setOrder($valor) { $this->order = trim($valor); }
	public function getOrder() { return $this->order; }
	
	public function setAsc($valor) { $this->asc = trim($valor); }
	public function getAsc() { return $this->asc; }
	
	
	function setGrupoAccion($data = array()){
		$this->gacIdaccion	= $data["GAC_IDACCION"];
		$this->gacIdgrupo	= $data["GAC_IDGRUPO"];
		$this->gacNombre 	= $data["GAC_NOMBRE"];
		$this->gacValor  	= $data["GAC_VALOR"];
		$this->gacUsucre 	= $data["GAC_USUCRE"];
		$this->gacFeccre 	= $data["GAC_FECCRE"];
	}

	public function getGrupoAccion($conMsi, $pageCode){
		$sql = "SELECT *
				FROM ".$this->tbl."
				WHERE GAC_IDACCION = ".mysqli_real_escape_string($conMsi, $this->gacIdaccion)."
				  AND GAC_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gacIdgrupo);
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GAC-SQL-01", $sql." -> ".$conMsi->error, 3);}
		$this->setGrupoAccion($result->fetch_assoc());
	}
	
	public function getGrupoAcciones($conMsi, $pageCode, $ascDesc){
		$list = array();
		$sql = "SELECT *
				FROM ".$this->tbl."
				WHERE GAC_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gacIdgrupo);
		
		if ($this->getOrder()=="") {
			$sql.= " ORDER BY GAC_NOMBRE ASC";
		} else {
			if ($this->getOrder()!=""){
				if ($this->getOrder()=="1") $orden = " GAC_NOMBRE ";
				else if ($this->getOrder()=="2") $orden = " GAC_VALOR ";
				
				if ($this->getAsc()=="1") $orden.= " ASC ";
				if ($this->getAsc()=="2") $orden.= " DESC ";
				$sql.= " ORDER BY ".$orden;
			}
		}
			
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GAC-SQL-02", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new GrupoAccion();
			$obj->setGrupoAccion($row);
			array_push($list, $obj);
		}
		return $list;
	}
	
	function insert($conMsi, $pageCode){
		global $error;
		
		$sql = "INSERT INTO ".$this->tbl." SET
					GAC_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gacIdgrupo).",
					GAC_NOMBRE = '".mysqli_real_escape_string($conMsi, $this->gacNombre)."',
					GAC_VALOR = ".mysqli_real_escape_string($conMsi, $this->gacValor).",
					GAC_USUCRE = ".mysqli_real_escape_string($conMsi, $this->gacUsucre);

		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GAC-SQL-03", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
	
	function update($conMsi, $pageCode){
		global $error;
		
		$sql = "UPDATE ".$this->tbl."
				SET 
					GAC_NOMBRE = '".mysqli_real_escape_string($conMsi, $this->gacNombre)."',
					GAC_VALOR = ".mysqli_real_escape_string($conMsi, $this->gacValor)."
				WHERE GAC_IDACCION = ".mysqli_real_escape_string($conMsi, $this->gacIdaccion)."
				  AND GAC_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gacIdgrupo);
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GAC-SQL-04", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
	
	function delete($conMsi, $pageCode){
		global $error;
		
		$sql = "DELETE FROM ".$this->tbl."
				WHERE GAC_IDACCION = ".mysqli_real_escape_string($conMsi, $this->gacIdaccion)."
				  AND GAC_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gacIdgrupo);
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GAC-SQL-05", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
	
	function deleteAccionesGrupo($conMsi, $pageCode){
		global $error;
		
		$sql = "DELETE FROM ".$this->tbl."
				WHERE GAC_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gacIdgrupo);
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GAC-SQL-06", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
	
	public function calcularValor($conMsi, $pageCode, $datos){
		$sql = "SELECT SUM(GAC_VALOR) AS TOTAL_VALOR
				FROM ".$this->tbl."
				WHERE GAC_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gacIdgrupo);
		echo $sql;
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GAC-SQL-07", $sql." -> ".$conMsi->error, 3);}
		$row = $result->fetch_assoc();
		$totalValor = $row["TOTAL_VALOR"];
	
		$listaRealizados = "";
		foreach ($datos as $dato) {
			$listaRealizados.= $dato.", ";
		}
		$listaRealizados = trim($listaRealizados, ", ");
		$sql = "SELECT SUM(GAC_VALOR) AS VALOR_REALIZADO
				FROM ".$this->tbl."
				WHERE GAC_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gacIdgrupo)."
				  AND GAC_IDACCION IN (".$listaRealizados.")";
		echo $sql;
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GAC-SQL-08", $sql." -> ".$conMsi->error, 3);}
		$row = $result->fetch_assoc();
		$valorRealizado = $row["VALOR_REALIZADO"];
		
		return ceil($valorRealizado * 100 / $totalValor);
	}

}
?>