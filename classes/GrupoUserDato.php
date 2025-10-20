<?php
include_once 'classes/Funciones.php';

class GrupoUserDato {
	//private $dbHost     = "localhost";
	//private $dbUsername = "root";
	//private $dbPassword = "";
	//private $dbName     = "codexworld";
	private $tbl    = 'GRUPO_USER_DATO';

	private $gudIdgud;
	private $gudIdgrupo;
	private $gudIduser;
	private $gudDato;
	private $gudComent;
	private $gudFecha;
	private $gudFeccre;
	
	private $pesoMedio;
	
	private $order;
	private $asc;
	
	function __construct(){
	}
	
	public function getGudIdgud() {
		return $this->gudIdgud;
	}

	public function setGudIdgud($gudIdgud) {
		$this->gudIdgud = $gudIdgud;
	}

	public function getGudIdgrupo() {
		return $this->gudIdgrupo;
	}

	public function setGudIdgrupo($gudIdgrupo) {
		$this->gudIdgrupo = $gudIdgrupo;
	}

	public function getGudIduser() {
		return $this->gudIduser;
	}

	public function setGudIduser($gudIduser) {
		$this->gudIduser = $gudIduser;
	}

	public function getGudDato() {
		return $this->gudDato;
	}

	public function setGudDato($gudDato) {
		$this->gudDato = $gudDato;
	}

	public function getGudComent() {
		return $this->gudComent;
	}

	public function setGudComent($gudComent) {
		$this->gudComent = $gudComent;
	}

	public function getGudFecha() {
		return $this->gudFecha;
	}

	public function setGudFecha($gudFecha) {
		$this->gudFecha = $gudFecha;
	}

	public function getGudFeccre() {
		return $this->gudFeccre;
	}

	public function setGudFeccre($gudFeccre) {
		$this->gudFeccre = $gudFeccre;
	}
	
	
	public function getPesoMedio() {
		return $this->pesoMedio;
	}
	
	public function setPesoMedio($pesoMedio) {
		$this->pesoMedio = $pesoMedio;
	}
	

	public function setOrder($valor) { $this->order = trim($valor); }
	public function getOrder() { return $this->order; }
	
	public function setAsc($valor) { $this->asc = trim($valor); }
	public function getAsc() { return $this->asc; }
	
	
	function setGrupoUserDato($data = array()){
		$this->gudIdgud			= $data["GUD_IDGUD"];
		$this->gudIdgrupo		= $data["GUD_IDGRUPO"];
		$this->gudIduser		= $data["GUD_IDUSER"];
		$this->gudDato			= $data["GUD_DATO"];
		$this->gudComent		= $data["GUD_COMENT"];
		$this->gudFecha			= $data["GUD_FECHA"];
		$this->gudComent		= $data["GUD_COMENT"];
		$this->gudFeccre		= $data["GUD_FECCRE"];
		
		$this->pesoMedio		= $data["peso_medio"];
	}

	public function getGrupoUserDato($conMsi, $pageCode){
		$sql = "SELECT *
				FROM ".$this->tbl."
				WHERE GUD_IDGUD = ".mysqli_real_escape_string($conMsi, $this->gudIdgud)."
				  AND GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
				  AND GUD_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gudIduser);
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-01", $sql." -> ".$conMsi->error, 3);}
		$this->setGrupoUserDato($result->fetch_assoc());
	}
	
	public function getGrupoUserDatos($conMsi, $pageCode){
		$list = array();
		$sql = "SELECT *
				FROM ".$this->tbl."
				WHERE GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
				  AND GUD_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gudIduser);
		
		if ($this->getOrder()=="") {
			$sql.= " ORDER BY GUD_FECHA DESC";
		} else {
			if ($this->getOrder()!=""){
				if ($this->getOrder()=="1") $orden = " GUD_FECHA ";
				else if ($this->getOrder()=="2") $orden = " GUD_DATO ";
				else if ($this->getOrder()=="3") $orden = " GUD_COMENT ";
				
				if ($this->getAsc()=="1") $orden.= " ASC ";
				if ($this->getAsc()=="2") $orden.= " DESC ";
				$sql.= " ORDER BY ".$orden;
			}
		}
			
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-02", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new GrupoUserDato();
			$obj->setGrupoUserDato($row);
			array_push($list, $obj);
		}
		return $list;
	}
	
	function insert($conMsi, $pageCode){
		global $error;
		
		$sql = "INSERT INTO ".$this->tbl." SET
					GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo).",
					GUD_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gudIduser).",
					GUD_DATO = ".mysqli_real_escape_string($conMsi, $this->gudDato).",
					GUD_FECHA = '".mysqli_real_escape_string($conMsi, $this->gudFecha)."',
					GUD_COMENT = '".mysqli_real_escape_string($conMsi, $this->gudComent)."'";
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-03", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
	
	function update($conMsi, $pageCode){
		global $error;
		
		$sql = "UPDATE ".$this->tbl."
				SET 
					GUD_DATO = ".mysqli_real_escape_string($conMsi, $this->gudDato).",
					GUD_COMENT = '".mysqli_real_escape_string($conMsi, $this->gudComent)."'
				WHERE GUD_IDGUD = ".mysqli_real_escape_string($conMsi, $this->gudIdgud)."
				  AND GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
				  AND GUD_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gudIduser);
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-04", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
	
	function delete($conMsi, $pageCode){
		global $error;
		
		$sql = "DELETE FROM ".$this->tbl."
				WHERE GUD_IDGUD = ".mysqli_real_escape_string($conMsi, $this->gudIdgud)."
				  AND GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
				  AND GUD_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gudIduser);

		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-05", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}

	public function checkRetrasoGrupoUserDato($conMsi, $pageCode, $palabraTiempo){
		$sql = "SELECT CASE  WHEN MAX(GUD_FECHA) IS NULL OR MAX(GUD_FECHA) <= NOW() - INTERVAL 1 ".mysqli_real_escape_string($conMsi, $palabraTiempo)." THEN 'S'
							 ELSE 'N'
					   END AS retraso_dato
				FROM ".$this->tbl."
				WHERE GUD_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gudIduser);
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-05", $sql." -> ".$conMsi->error, 3);}
		if ($row = $result->fetch_assoc()) {
			if ($row["retraso_dato"] == "S") {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	
	function getGrupoUsersDatos($conMsi, $pageCode, $fecini){
		global $error;
		$list = array();
		$fecini = mysqli_real_escape_string($conMsi, $fecini);
		
		$sql = "SELECT GUD_IDUSER,
				GUD_DATO AS peso_medio,
				date(GUD_FECHA) AS GUD_FECHA
				FROM ".$this->tbl."
				WHERE GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
				  AND GUD_FECHA >= '".$fecini."'
				ORDER BY GUD_IDUSER";

		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-14", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new GrupoUserDato();
			$obj->setGrupoUserDato($row);
			array_push($list, $obj);
		}
		return $list;
	}
}
?>