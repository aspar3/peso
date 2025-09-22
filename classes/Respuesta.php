<?php
class Respuesta {

	private $tbl    = 'RESPUESTA';

	private $resIdrespuesta;
	private $resNombre;
	private $resOrden;
	
	function __construct(){}
	
	public function getResIdrespuesta() {
		return $this->resIdrespuesta;
	}

	public function getResNombre() {
		return $this->resNombre;
	}

	public function getResOrden() {
		return $this->resOrden;
	}

	public function setResIdrespuesta($resIdrespuesta) {
		$this->resIdrespuesta = $resIdrespuesta;
	}

	public function setResNombre($resNombre) {
		$this->resNombre = $resNombre;
	}

	public function setResOrden($resOrden) {
		$this->resOrden = $resOrden;
	}


	function setRespuesta($data = array()){
		$this->resIdrespuesta	= $data["RES_IDRESPUESTA"];
		$this->resNombre		= $data["RES_NOMBRE"];
		$this->resOrden			= $data["RES_ORDEN"];
	}

	public function getRespuestas($conMsi, $pageCode){
		$list = array();
		$sql = "SELECT *
				FROM ".$this->tbl."
				ORDER BY RES_ORDEN";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> RES-SQL-01", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new Respuesta();
			$obj->setRespuesta($row);
			array_push($list, $obj);
		}
		return $list;
	}
}
?>