<?php
include_once 'classes/Funciones.php';

class GrupoUser {

	private $tbl		= 'GRUPO_USER';
	private $tblGrupo	= 'GRUPO';
	private $tblUser	= 'USER';
	private $tblRol		= 'ROL';
	private $tblPeso	= 'PESO';
	
	private $gusIdgrupo;
	private $gusIduser;
	private $gusIdrol;
	private $gusFeccre;
	private $gusUsucre;
	private $gusVerifyCode;
	
	private $useName;
	private $useLastname;
	private $rolNombre;
	
	private $pesoMedio;
	
	private $order;
	private $asc;
	
	function __construct(){}
	
	public function getGusIdgrupo() {
		return $this->gusIdgrupo;
	}

	public function getGusIduser() {
		return $this->gusIduser;
	}

	public function getGusIdrol() {
		return $this->gusIdrol;
	}

	public function getGusFeccre() {
		return $this->gusFeccre;
	}

	public function setGusIdgrupo($gusIdgrupo) {
		$this->gusIdgrupo = $gusIdgrupo;
	}

	public function setGusIduser($gusIduser) {
		$this->gusIduser = $gusIduser;
	}

	public function setGusIdrol($gusIdrol) {
		$this->gusIdrol = $gusIdrol;
	}

	public function setGusFeccre($gusFeccre) {
		$this->gusFeccre = $gusFeccre;
	}

	public function getGusUsucre() {
		return $this->gusUsucre;
	}

	public function setGusUsucre($gusUsucre) {
		$this->gusUsucre = $gusUsucre;
	}

	public function getGusVerifyCode() {
		return $this->gusVerifyCode;
	}

	public function setGusVerifyCode($gusVerifyCode) {
		$this->gusVerifyCode = $gusVerifyCode;
	}

	
	public function getUseName() {
		return $this->useName;
	}

	public function setUseName($useName) {
		$this->useName = $useName;
	}

	public function getUseLastname() {
		return $this->useLastname;
	}

	public function setUseLastname($useLastname) {
		$this->useLastname = $useLastname;
	}

	public function getRolNombre() {
		return $this->rolNombre;
	}

	public function setRolNombre($rolNombre) {
		$this->rolNombre = $rolNombre;
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
	
	
	function setGrupoUser($data = array()){
		$this->gusIdgrupo		= $data["GUS_IDGRUPO"];
		$this->gusIduser		= $data["GUS_IDUSER"];
		$this->gusIdrol			= $data["GUS_IDROL"];
		$this->gusFeccre		= $data["GUS_FECCRE"];
		$this->gusUsucre		= $data["GUS_USUCRE"];
		$this->gusVerifyCode	= $data["GUS_VERIFY_CODE"];
		
		$this->pesoMedio		= $data["peso_medio"];
		
		$this->useName			= $data["USE_NAME"];
		$this->useLastname		= $data["USE_LASTNAME"];
		$this->rolNombre		= $data["ROL_NOMBRE"];
	}
	
	function getGruposByIduser($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM ".$this->tbl."
					LEFT JOIN ".$this->tblGrupo." ON GUS_IDGRUPO = GRU_IDGRUPO
				WHERE GRU_STATUS = 1
				  AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gusIduser);
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-03", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)==1){
			$this->setGrupoUser($result->fetch_assoc());
			return true;
		}else return false;
	}
	
	function getGrupoUser($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM ".$this->tbl."
				WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo)."
				  AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gusIduser);
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-03", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)==1){
			$this->setGrupoUser($result->fetch_assoc());
			return true;
		}else return false;
	}
	
	function getGrupoUsers($conMsi, $pageCode){
		global $error;
		$list = array();
		
		$sql = "SELECT *
				FROM ".$this->tbl."
					LEFT JOIN ".$this->tblGrupo." ON GUS_IDGRUPO = GRU_IDGRUPO
					LEFT JOIN ".$this->tblUser." ON GUS_IDUSER = USE_IDUSER
					LEFT JOIN ".$this->tblRol." ON GUS_IDROL = ROL_IDROL
				WHERE GRU_STATUS = 1
				  AND GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo);
		
		if ($this->getOrder()=="") {
			$sql.= " ORDER BY USE_NAME";
		} else {
			if ($this->getOrder()!=""){
				if ($this->getOrder()=="1") $orden = " USE_NAME ";
				else if ($this->getOrder()=="2") $orden = " USE_LASTNAME ";
				else if ($this->getOrder()=="3") $orden = " ROL_NOMBRE ";
				
				if ($this->getAsc()=="1") $orden.= " ASC ";
				if ($this->getAsc()=="2") $orden.= " DESC ";
				$sql.= " ORDER BY ".$orden;
			}
		}

		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-03", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new GrupoUser();
			$obj->setGrupoUser($row);
			array_push($list, $obj);
		}
		return $list;
	}
	
	function insertGrupoUser($conMsi, $pageCode){
		global $error;
		
		$sql = "INSERT INTO ".$this->tbl."
				SET GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo).",
					GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gusIduser).",
					GUS_IDROL = ".mysqli_real_escape_string($conMsi, $this->gusIdrol).",
					GUS_FECCRE = NOW(),
					GUS_USUCRE = ".mysqli_real_escape_string($conMsi, $this->gusUsucre).",
					GUS_VERIFY_CODE = '".mysqli_real_escape_string($conMsi, $this->gusVerifyCode)."'";
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-18", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
	
	function aceptarInvitacion($conMsi, $pageCode){
		global $error;
		
		$sql = "UPDATE ".$this->tbl." SET
					GUS_VERIFY_CODE = NULL
				WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo)."
				  AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gusIduser);

		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-13", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
	
	function deleteGrupoUser($conMsi, $pageCode){
		global $error;
		
		$sql = "DELETE FROM ".$this->tbl."
				WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo)."
				  AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gusIduser);
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-13", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
	
	function rechazarInvitacion($conMsi, $pageCode){
		global $error;
		
		$sql = "DELETE FROM ".$this->tbl."
				WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo)."
				  AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gusIduser)."
				  AND GUS_VERIFY_CODE = '".mysqli_real_escape_string($conMsi, $this->gusVerifyCode)."'";

		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-13", $sql." -> ".$conMsi->error, 3);}
		
		if (mysqli_affected_rows($conMsi)>0){
			return true;
		}else return false;
	}
	
	function checkEsAdministrador($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM GRUPO_USER
				WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo)."
				  AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gusIduser)."
				  AND GUS_IDROL = 1";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-08", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)>0){
			return true;
		}else return false;
	}

	function quedanUsuarios($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM GRUPO_USER
				WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo);
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-08", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)>0){
			return true;
		}else return false;
	}
	
	function quedaAlgunAdministrador($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM GRUPO_USER
				WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo)."
				  AND GUS_IDROL = 1";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-08", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)>0){
			return true;
		}else return false;
	}
	
	function ponerNuevoAdministrador($conMsi, $pageCode){
		global $error;
		
		$sql = "UPDATE ".$this->tbl."
				SET GUS_IDROL = 1
				WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo)."
				  AND GUS_IDUSER = (SELECT GUS_IDUSER FROM ".$this->tbl." 
									WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo)."
									  AND GUS_FECCRE = (SELECT MIN(GUS_FECCRE) FROM ".$this->tbl." 
														WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo)."))";

		echo $sql;
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-13", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}

	function getGrupoUsersPeso($conMsi, $pageCode, $year, $week){
		global $error;
		$list = array();
		
		$sql = "SELECT GUS_IDUSER,
				AVG(PES_PESO) AS peso_medio
				FROM ".$this->tbl."
					LEFT JOIN ".$this->tblPeso." ON GUS_IDUSER = PES_IDUSER
				WHERE GUS_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gusIdgrupo)."
				  AND YEAR(PES_FECHA) = ".mysqli_real_escape_string($conMsi, $year)."
				  AND WEEK(PES_FECHA, 3) = ".mysqli_real_escape_string($conMsi, $week)."
				GROUP BY GUS_IDUSER
				ORDER BY GUS_IDUSER";

		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-14", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new GrupoUser();
			$obj->setGrupoUser($row);
			array_push($list, $obj);
		}
		return $list;
	}
	
	function getTodosLosGruposUsersPeso($conMsi, $pageCode, $year, $week){
		global $error;
		$list = array();
		
		$sql = "SELECT GUS_IDUSER,
				AVG(PES_PESO) AS peso_medio
				FROM ".$this->tbl."
					LEFT JOIN ".$this->tblPeso." ON GUS_IDUSER = PES_IDUSER
				WHERE GUS_IDGRUPO IN (SELECT gu2.GUS_IDGRUPO 
									   FROM ".$this->tbl." gu2
									   WHERE gu2.GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gusIduser).")
				  AND YEAR(PES_FECHA) = ".mysqli_real_escape_string($conMsi, $year)."
				  AND WEEK(PES_FECHA, 3) = ".mysqli_real_escape_string($conMsi, $week)."
				GROUP BY GUS_IDUSER
				ORDER BY GUS_IDUSER";
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUS-SQL-15", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new GrupoUser();
			$obj->setGrupoUser($row);
			array_push($list, $obj);
		}
		return $list;
	}
	
}
?>