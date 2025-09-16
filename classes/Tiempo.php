<?php
class Tiempo {

	private $tbl    = 'TIEMPO';

	private $tieIdtiempo;
	private $tieNombre;
	private $tieOrden;

	function __construct(){}
	
	public function getTieIdtiempo() {
		return $this->tieIdtiempo;
	}

	public function getTieNombre() {
		return $this->tieNombre;
	}

	public function getTieOrden() {
		return $this->tieOrden;
	}

	public function setTieIdtiempo($tieIdtiempo) {
		$this->tieIdtiempo = $tieIdtiempo;
	}

	public function setTieNombre($tieNombre) {
		$this->tieNombre = $tieNombre;
	}

	public function setTieOrden($tieOrden) {
		$this->tieOrden = $tieOrden;
	}

	function setTiempo($data = array()){
		$this->tieIdtiempo	= $data["TIE_IDTIEMPO"];
		$this->tieNombre	= $data["TIE_NOMBRE"];
		$this->tieOrden		= $data["TIE_ORDEN"];
	}

	public function getTiempos($conMsi, $pageCode){
		$list = array();
		$sql = "SELECT *
				FROM ".$this->tbl."
				ORDER BY TIE_ORDEN";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> TIE-SQL-01", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new Tiempo();
			$obj->setTiempo($row);
			array_push($list, $obj);
		}
		return $list;
	}
}
?>