<?php
include_once 'classes/Funciones.php';

class Peso {
	//private $dbHost     = "localhost";
	//private $dbUsername = "root";
	//private $dbPassword = "";
	//private $dbName     = "codexworld";
	private $tbl    = 'PESO';

	private $pesIdpeso;
	private $pesIduser;
	private $pesPeso;
	private $pesPorcenGrasa;
	private $pesPorcenAgua;
	private $pesPorcenMusculo;
	private $pesFotoPeso;
	private $pesFotoPersona;
	private $pesFecha;
	private $pesComent;
	
	private $order;
	private $asc;
	
	function __construct(){
	}
	
	public function getPesIdpeso() {
		return $this->pesIdpeso;
	}

	public function getPesIduser() {
		return $this->pesIduser;
	}

	public function getPesPeso() {
		return $this->pesPeso;
	}

	public function getPesPorcenGrasa() {
		return $this->pesPorcenGrasa;
	}

	public function getPesPorcenAgua() {
		return $this->pesPorcenAgua;
	}

	public function getPesPorcenMusculo() {
		return $this->pesPorcenMusculo;
	}

	public function getPesFotoPeso() {
		return $this->pesFotoPeso;
	}

	public function setPesFotoPeso($pesFotoPeso) {
		$this->pesFotoPeso = $pesFotoPeso;
	}

	public function getPesFotoPersona() {
		return $this->pesFotoPersona;
	}
	
	public function setPesFotoPersona($pesFotoPersona) {
		$this->pesFotoPersona = $pesFotoPersona;
	}
	
	public function getPesFecha() {
		return $this->pesFecha;
	}

	public function setPesIdpeso($pesIdpeso) {
		$this->pesIdpeso = $pesIdpeso;
	}

	public function setPesIduser($pesIduser) {
		$this->pesIduser = $pesIduser;
	}

	public function setPesPeso($pesPeso) {
		$this->pesPeso = $pesPeso;
	}

	public function setPesPorcenGrasa($pesPorcenGrasa) {
		$this->pesPorcenGrasa = $pesPorcenGrasa;
	}

	public function setPesPorcenAgua($pesPorcenAgua) {
		$this->pesPorcenAgua = $pesPorcenAgua;
	}

	public function setPesPorcenMusculo($pesPorcenMusculo) {
		$this->pesPorcenMusculo = $pesPorcenMusculo;
	}

	public function setPesFecha($pesFecha) {
		$this->pesFecha = $pesFecha;
	}

	
	public function getPesComent() {
		return $this->pesComent;
	}

	public function setPesComent($pesComent) {
		$this->pesComent = trim($pesComent);
	}

	
	public function setOrder($valor) { $this->order = trim($valor); }
	public function getOrder() { return $this->order; }
	
	public function setAsc($valor) { $this->asc = trim($valor); }
	public function getAsc() { return $this->asc; }
	
	
	function setPeso($data = array()){
		$this->pesIdpeso		= $data["PES_IDPESO"];
		$this->pesIduser		= $data["PES_IDUSER"];
		$this->pesPeso			= $data["PES_PESO"];
		$this->pesPorcenGrasa	= $data["PES_PORCEN_GRASA"];
		$this->pesPorcenAgua	= $data["PES_PORCEN_AGUA"];
		$this->pesPorcenMusculo	= $data["PES_PORCEN_MUSCULO"];
		$this->pesFecha			= $data["PES_FECHA"];
		$this->pesComent		= $data["PES_COMENT"];
	}

	public function getPeso($conMsi, $pageCode){
		$sql = "SELECT *
				FROM ".$this->tbl."
				WHERE PES_IDUSER = ".mysqli_real_escape_string($conMsi, $this->pesIduser)."
				  AND PES_IDPESO = ".mysqli_real_escape_string($conMsi, $this->pesIdpeso);
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> PES-SQL-02", $sql." -> ".$conMsi->error, 3);}
		$this->setPeso($result->fetch_assoc());
	}
	
	public function getPesos($conMsi, $pageCode){
		$list = array();
		$sql = "SELECT *
				FROM ".$this->tbl."
				WHERE PES_IDUSER = ".mysqli_real_escape_string($conMsi, $this->pesIduser);
		
		if ($this->getOrder()=="") {
			$sql.= " ORDER BY PES_FECHA DESC";
		} else {
			if ($this->getOrder()!=""){
				if ($this->getOrder()=="1") $orden = " PES_FECHA ";
				else if ($this->getOrder()=="2") $orden = " PES_PESO ";
				else if ($this->getOrder()=="3") $orden = " PES_COMENT ";
				
				if ($this->getAsc()=="1") $orden.= " ASC ";
				if ($this->getAsc()=="2") $orden.= " DESC ";
				$sql.= " ORDER BY ".$orden;
			}
		}
			
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> PES-SQL-01", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new Peso();
			$obj->setPeso($row);
			array_push($list, $obj);
		}
		return $list;
	}
	
	function update($conMsi, $pageCode){
		global $error;
		
		$sql = "UPDATE ".$this->tbl."
				SET 
					PES_COMENT = '".mysqli_real_escape_string($conMsi, $this->pesComent)."',
					PES_PESO = ".mysqli_real_escape_string($conMsi, $this->pesPeso).",
					PES_PORCEN_GRASA = ".Funciones::numberVacio2null(mysqli_real_escape_string($conMsi, $this->pesPorcenGrasa)).",
					PES_PORCEN_AGUA = ".Funciones::numberVacio2null(mysqli_real_escape_string($conMsi, $this->pesPorcenAgua)).",
					PES_PORCEN_MUSCULO = ".Funciones::numberVacio2null(mysqli_real_escape_string($conMsi, $this->pesPorcenMusculo))."
				WHERE PES_IDPESO = ".mysqli_real_escape_string($conMsi, $this->pesIdpeso)."
				  AND PES_IDUSER = ".mysqli_real_escape_string($conMsi, $this->pesIduser);
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> PES-SQL-02", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}

	
	function insert($conMsi, $pageCode){
		global $error;
		
		$sql = "INSERT INTO ".$this->tbl." SET
					PES_IDUSER = ".mysqli_real_escape_string($conMsi, $this->pesIduser).",
					PES_PESO = ".mysqli_real_escape_string($conMsi, $this->pesPeso).",
					PES_PORCEN_GRASA = ".Funciones::numberVacio2null(mysqli_real_escape_string($conMsi, $this->pesPorcenGrasa)).",
					PES_PORCEN_AGUA = ".Funciones::numberVacio2null(mysqli_real_escape_string($conMsi, $this->pesPorcenAgua)).",
					PES_PORCEN_MUSCULO = ".Funciones::numberVacio2null(mysqli_real_escape_string($conMsi, $this->pesPorcenMusculo)).",
					PES_FECHA = '".mysqli_real_escape_string($conMsi, $this->pesFecha)."',
					PES_COMENT = '".mysqli_real_escape_string($conMsi, $this->pesComent)."'";
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> PES-SQL-11", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
	
	function delete($conMsi, $pageCode){
		global $error;
		
		$sql = "DELETE FROM ".$this->tbl."
				WHERE PES_IDPESO = ".mysqli_real_escape_string($conMsi, $this->pesIdpeso)."
				  AND PES_IDUSER = ".mysqli_real_escape_string($conMsi, $this->pesIduser);

		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> PES-SQL-13", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}

	public function checkRetrasoPeso($conMsi, $pageCode, $palabraTiempo){
		$sql = "SELECT CASE  WHEN MAX(PES_FECHA) IS NULL OR MAX(PES_FECHA) <= NOW() - INTERVAL 1 ".mysqli_real_escape_string($conMsi, $palabraTiempo)." THEN 'S'
							 ELSE 'N'
					   END AS retraso_peso
				FROM ".$this->tbl."
				WHERE PES_IDUSER = ".mysqli_real_escape_string($conMsi, $this->pesIduser);
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> PES-SQL-02", $sql." -> ".$conMsi->error, 3);}
		if ($row = $result->fetch_assoc()) {
			if ($row["retraso_peso"] == "S") {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
}
?>