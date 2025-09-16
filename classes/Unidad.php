<?php
class Unidad {
	//private $dbHost     = "localhost";
	//private $dbUsername = "root";
	//private $dbPassword = "";
	//private $dbName     = "codexworld";
	private $tbl    = 'UNIDAD';

	private $uniIdunidad;
	private $uniNombre;
	private $uniAbrev;
	private $uniMultipli;
	private $uniOrden;
	

	public function getUniIdunidad() {
		return $this->uniIdunidad;
	}
	
	public function getUniNombre() {
		return $this->uniNombre;
	}
	
	public function getUniAbrev() {
		return $this->uniAbrev;
	}
	
	public function getUniMultipli() {
		return $this->uniMultipli;
	}
	
	public function setUniIdunidad($uniIdunidad) {
		$this->uniIdunidad = $uniIdunidad;
	}
	
	public function setUniNombre($uniNombre) {
		$this->uniNombre = $uniNombre;
	}
	
	public function setUniAbrev($uniAbrev) {
		$this->uniAbrev = $uniAbrev;
	}
	
	public function setUniMultipli($uniMultipli) {
		$this->uniMultipli = $uniMultipli;
	}
	
	public function getUniOrden() {
		return $this->uniOrden;
	}

	public function setUniOrden($uniOrden) {
		$this->uniOrden = $uniOrden;
	}

	function __construct(){
	}


	function setUnidad($data = array()){
		$this->uniIdunidad	= $data["UNI_IDUNIDAD"];
		$this->uniNombre	= $data["UNI_NOMBRE"];
		$this->uniAbrev		= $data["UNI_ABREV"];
		$this->uniMultipli	= $data["UNI_MULTIPLI"];
		$this->uniOrden		= $data["UNI_ORDEN"];
	}
	
	public function getUnidades($conMsi, $pageCode){
		$list = array();
		$sql = "SELECT *
				FROM ".$this->tbl."
				ORDER BY UNI_ORDEN";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> UNI-SQL-01", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new Unidad();
			$obj->setUnidad($row);
			array_push($list, $obj);
		}
		return $list;
	}

	
	public function getUnidad($conMsi, $pageCode){
		$sql = "SELECT *
				FROM ".$this->tbl."
				WHERE UNI_IDUNIDAD = ".mysqli_real_escape_string($conMsi, $this->uniIdunidad);
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> UNI-SQL-02", $sql." -> ".$conMsi->error, 3);}
		$row = $result->fetch_assoc();
		$this->uniNombre	= $row["UNI_NOMBRE"];
		$this->uniAbrev		= $row["UNI_ABREV"];
		$this->uniMultipli	= $row["UNI_MULTIPLI"];
		$this->uniOrden		= $row["UNI_ORDEN"];
	}
}
?>