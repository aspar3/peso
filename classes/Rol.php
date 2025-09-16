<?php
class Rol {

	private $tbl    = 'ROL';

	private $rolIdrol;
	private $rolNombre;
	private $rolOrden;

	function __construct(){}

	public function getRolIdrol() {
		return $this->rolIdrol;
	}

	public function getRolNombre() {
		return $this->rolNombre;
	}

	public function setRolIdrol($rolIdrol) {
		$this->rolIdrol = $rolIdrol;
	}

	public function setRolNombre($rolNombre) {
		$this->rolNombre = $rolNombre;
	}

	public function getRolOrden() {
		return $this->rolOrden;
	}

	public function setRolOrden($rolOrden) {
		$this->rolOrden = $rolOrden;
	}

	function setRol($data = array()){
		$this->rolIdrol		= $data["ROL_IDROL"];
		$this->rolNombre	= $data["ROL_NOMBRE"];
		$this->rolOrden		= $data["ROL_ORDEN"];
	}

	public function getRoles($conMsi, $pageCode){
		$list = array();
		$sql = "SELECT *
				FROM ".$this->tbl."
				ORDER BY ROL_ORDEN";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> ROL-SQL-01", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new Rol();
			$obj->setRol($row);
			array_push($list, $obj);
		}
		return $list;
	}
}
?>