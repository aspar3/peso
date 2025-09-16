<?php
include_once 'classes/Funciones.php';

class Grupo {

	private $tbl    = 'GRUPO';
	private $grupoUserTbl    = 'GRUPO_USER';
	
	private $gruIdgrupo;
	private $gruNombre;
	private $gruMostrarPeso;
	private $gruFecini;
	private $gruFecfin;
	private $gruIdtiempo;
	private $gruReto;
	private $gruFeccre;
	private $gruStatus;
	
	private $gruIduser;
	private $numeroMiembros;
	private $esAdmin;
	
	private $order;
	private $asc;
	
	function __construct(){}
	
	public function getGruIdgrupo() {
		return $this->gruIdgrupo;
	}

	public function getGruNombre() {
		return $this->gruNombre;
	}
	
	public function getGruFecini() {
		return $this->gruFecini;
	}

	public function getGruFecfin() {
		return $this->gruFecfin;
	}

	public function getGruIdtiempo() {
		return $this->gruIdtiempo;
	}

	public function getGruFeccre() {
		return $this->gruFeccre;
	}

	public function setGruIdgrupo($gruIdgrupo) {
		$this->gruIdgrupo = $gruIdgrupo;
	}

	public function setGruNombre($gruNombre) {
		$this->gruNombre = $gruNombre;
	}
	
	public function setGruFecini($gruFecini) {
		$this->gruFecini = $gruFecini;
	}

	public function setGruFecfin($gruFecfin) {
		$this->gruFecfin = $gruFecfin;
	}

	public function setGruIdtiempo($gruIdtiempo) {
		$this->gruIdtiempo = $gruIdtiempo;
	}

	public function getGruReto() {
		return $this->gruReto;
	}

	public function setGruReto($gruReto) {
		$this->gruReto = $gruReto;
	}

	public function setGruFeccre($gruFeccre) {
		$this->gruFeccre = $gruFeccre;
	}

	public function getGruStatus() {
		return $this->gruStatus;
	}

	public function setGruStatus($gruStatus) {
		$this->gruStatus = $gruStatus;
	}

	public function getGruMostrarPeso() {
		return $this->gruMostrarPeso;
	}

	public function setGruMostrarPeso($gruMostrarPeso) {
		$this->gruMostrarPeso = $gruMostrarPeso;
	}
	
	
	public function getGruIduser() {
		return $this->gruIduser;
	}

	public function setGruIduser($gruIduser) {
		$this->gruIduser = $gruIduser;
	}

	public function getNumeroMiembros() {
		return $this->numeroMiembros;
	}

	public function setNumeroMiembros($numeroMiembros) {
		$this->numeroMiembros = $numeroMiembros;
	}

	public function getEsAdmin() {
		return $this->esAdmin;
	}

	public function setEsAdmin($esAdmin) {
		$this->esAdmin = $esAdmin;
	}

	public function setOrder($valor) { $this->order = trim($valor); }
	public function getOrder() { return $this->order; }
	
	public function setAsc($valor) { $this->asc = trim($valor); }
	public function getAsc() { return $this->asc; }
	

	function setGrupo($data = array()){
		$this->gruIdgrupo		= $data["GRU_IDGRUPO"];
		$this->gruNombre		= $data["GRU_NOMBRE"];
		$this->gruMostrarPeso	= $data["GRU_MOSTRAR_PESO"];
		$this->gruFecini		= $data["GRU_FECINI"];
		$this->gruFecfin		= $data["GRU_FECFIN"];
		$this->gruIdtiempo		= $data["GRU_IDTIEMPO"];
		$this->gruReto		= $data["GRU_RETO"];
		$this->gruFeccre		= $data["GRU_USUCRE"];
		$this->gruStatus		= $data["GRU_STATUS"];
		
		$this->numeroMiembros	= $data["NUMERO_MIEMBROS"];
		$this->esAdmin			= $data["ES_ADMIN"];
	}
			
	function getGrupo($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *,
					   (SELECT COUNT(1) FROM ".$this->grupoUserTbl." WHERE GRU_IDGRUPO = GUS_IDGRUPO AND (GUS_VERIFY_CODE IS NULL OR GUS_VERIFY_CODE = '')) NUMERO_MIEMBROS,
					   EXISTS (SELECT 1 FROM ".$this->grupoUserTbl." WHERE GRU_IDGRUPO = GUS_IDGRUPO
																	   AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gruIduser)."
																	   AND GUS_IDROL = 1) ES_ADMIN
				FROM ".$this->tbl."
				WHERE GRU_STATUS = 1
				  AND GRU_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gruIdgrupo)."
				  AND EXISTS (SELECT 1 FROM GRUPO_USER 
							  WHERE GUS_IDGRUPO = GRU_IDGRUPO 
								AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gruIduser).")";

		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GRU-SQL-03", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)==1){
			$this->setGrupo($result->fetch_assoc());
			return true;
		}else return false;
	}
	
	public function getGruposAceptados($conMsi, $pageCode){
		$list = array();
		$sql = "SELECT *,
					   (SELECT COUNT(1) FROM ".$this->grupoUserTbl." WHERE GRU_IDGRUPO = GUS_IDGRUPO AND (GUS_VERIFY_CODE IS NULL OR GUS_VERIFY_CODE = '')) NUMERO_MIEMBROS,
					   EXISTS (SELECT 1 FROM ".$this->grupoUserTbl." WHERE GRU_IDGRUPO = GUS_IDGRUPO
																	   AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gruIduser)."
																	   AND GUS_IDROL = 1) ES_ADMIN
				FROM ".$this->tbl."
				WHERE GRU_STATUS = 1
				  AND EXISTS (SELECT 1 FROM GRUPO_USER 
							  WHERE GUS_IDGRUPO = GRU_IDGRUPO 
								AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gruIduser)."
								AND (GUS_VERIFY_CODE IS NULL OR GUS_VERIFY_CODE = ''))";
		
		if ($this->getOrder()=="") {
			$sql.= " ORDER BY GRU_FECINI DESC";
		} else {
			if ($this->getOrder()!=""){
				if ($this->getOrder()=="1") $orden = " GRU_NOMBRE ";
				else if ($this->getOrder()=="2") $orden = " GRU_FECINI ";
				else if ($this->getOrder()=="3") $orden = " GRU_FECFIN ";
				else if ($this->getOrder()=="4") $orden = " NUMERO_MIEMBROS ";
				
				if ($this->getAsc()=="1") $orden.= " ASC ";
				if ($this->getAsc()=="2") $orden.= " DESC ";
				$sql.= " ORDER BY ".$orden;
			}
		}

		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GRU-SQL-01", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new Grupo();
			$obj->setGrupo($row);
			array_push($list, $obj);
		}
		return $list;
	}
	
	public function getGruposPendientes($conMsi, $pageCode){
		$list = array();
		$sql = "SELECT *,
					   (SELECT COUNT(1) FROM ".$this->grupoUserTbl." WHERE GRU_IDGRUPO = GUS_IDGRUPO AND (GUS_VERIFY_CODE IS NULL OR GUS_VERIFY_CODE = '')) NUMERO_MIEMBROS,
					   EXISTS (SELECT 1 FROM ".$this->grupoUserTbl." WHERE GRU_IDGRUPO = GUS_IDGRUPO
																	   AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gruIduser)."
																	   AND GUS_IDROL = 1) ES_ADMIN
				FROM ".$this->tbl."
				WHERE GRU_STATUS = 1
				  AND EXISTS (SELECT 1 FROM GRUPO_USER
							  WHERE GUS_IDGRUPO = GRU_IDGRUPO
								AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gruIduser)."
								AND GUS_VERIFY_CODE != '' AND GUS_VERIFY_CODE IS NOT NULL)
				ORDER BY GRU_FECINI DESC";

		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GRU-SQL-01", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new Grupo();
			$obj->setGrupo($row);
			array_push($list, $obj);
		}
		return $list;
	}
	
	function insertGrupo($conMsi, $pageCode){
		global $error;
		
		$sql = "INSERT INTO ".$this->tbl."
				SET GRU_NOMBRE = '".mysqli_real_escape_string($conMsi, $this->gruNombre)."',
					GRU_MOSTRAR_PESO = '".mysqli_real_escape_string($conMsi, $this->gruMostrarPeso)."',
					GRU_FECINI = '".mysqli_real_escape_string($conMsi, $this->gruFecini)."',
					".($this->gruFecfin != ""?"GRU_FECFIN = '".mysqli_real_escape_string($conMsi, $this->gruFecfin)."',":"")."
					GRU_IDTIEMPO = '".mysqli_real_escape_string($conMsi, $this->gruIdtiempo)."',
					GRU_RETO = '".mysqli_real_escape_string($conMsi, $this->gruReto)."',
					GRU_FECCRE = NOW(),
					GRU_USUCRE = '".mysqli_real_escape_string($conMsi, $this->gruIduser)."',
					GRU_STATUS = 1";

		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GRU-SQL-18", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			$this->setGruIdgrupo($conMsi->insert_id);
			return true;
		}else return false;
	}
	
	function updateGrupo($conMsi, $pageCode){
		global $error;
		
		$sql = "UPDATE ".$this->tbl."
				SET GRU_NOMBRE = '".mysqli_real_escape_string($conMsi, $this->gruNombre)."',
					GRU_MOSTRAR_PESO = '".mysqli_real_escape_string($conMsi, $this->gruMostrarPeso)."',
					GRU_FECINI = '".mysqli_real_escape_string($conMsi, $this->gruFecini)."',
					".($this->gruFecfin != ""?"GRU_FECFIN = '".mysqli_real_escape_string($conMsi, $this->gruFecfin)."',":"")."
					GRU_IDTIEMPO = '".mysqli_real_escape_string($conMsi, $this->gruIdtiempo)."',
					GRU_RETO = '".mysqli_real_escape_string($conMsi, $this->gruReto)."',
					GRU_STATUS = ".mysqli_real_escape_string($conMsi, $this->gruStatus)."
				WHERE GRU_STATUS = 1
				  AND GRU_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gruIdgrupo)."
				  AND EXISTS (SELECT 1 FROM GRUPO_USER 
							  WHERE GUS_IDGRUPO = GRU_IDGRUPO 
								AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gruIduser).")";
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GRU-SQL-18", $sql." -> ".$conMsi->error, 3);}
		else {return true;}

		return false;
	}
	
	function deleteGrupo($conMsi, $pageCode){
		global $error;
		// El primer exists es para que solo pueda borrar un grupo el administrador
		// El segundo es para que se pueda borrar si esta vacio, porque si un admin se va y se queda vacio el grupo, que se pueda borrar
		$sql = "DELETE FROM ".$this->tbl."
				WHERE GRU_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gruIdgrupo)."
				  AND (EXISTS (SELECT 1 FROM GRUPO_USER 
							  WHERE GUS_IDGRUPO = GRU_IDGRUPO 
								AND GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gruIduser)."
								AND GUS_IDROL = 1)
					   OR NOT EXISTS (SELECT 1 FROM GRUPO_USER 
							  WHERE GUS_IDGRUPO = GRU_IDGRUPO)
					  )";
		echo $sql;
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GRU-SQL-13", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}
}
?>