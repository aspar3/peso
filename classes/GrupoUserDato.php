<?php
include_once 'classes/Funciones.php';

class GrupoUserDato {
	//private $dbHost     = "localhost";
	//private $dbUsername = "root";
	//private $dbPassword = "";
	//private $dbName     = "codexworld";
	private $tbl    		= 'GRUPO_USER_DATO';
	private $tblGrupo    	= 'GRUPO';
	private $tblGrupoUser	= 'GRUPO_USER';
	
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
	
	public function getGrupoUserDatos($conMsi, $pageCode, $ascDesc){
		$list = array();
		$sql = "SELECT *
				FROM ".$this->tbl."
				WHERE GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
				  AND GUD_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gudIduser);
		
		if ($ascDesc != "") {
			$sql.= " ORDER BY GUD_FECHA $ascDesc";
		} else if ($this->getOrder()=="") {
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
	
	
	function deleteTodosDatosGrupo($conMsi, $pageCode){
		global $error;
		
		$sql = "DELETE FROM ".$this->tbl."
				WHERE GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo);
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-06", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			return true;
		}else return false;
	}

	public function checkRetrasoGrupoUserDato($conMsi, $pageCode, $palabraTiempo){
		$sql = "SELECT CASE  WHEN MAX(GUD_FECHA) IS NULL OR MAX(GUD_FECHA) <= NOW() - INTERVAL 1 ".mysqli_real_escape_string($conMsi, $palabraTiempo)." THEN 'S'
							 ELSE 'N'
					   END AS retraso_dato
				FROM ".$this->tbl."
				WHERE GUD_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gudIduser)."
				  AND GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo);
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-07", $sql." -> ".$conMsi->error, 3);}
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
	
	function getGrupoUsersDatos($conMsi, $pageCode){
		global $error;
		$list = array();
		
		$sql = "SELECT GUD_IDUSER,
					   GUD_DATO AS peso_medio,
					   date(GUD_FECHA) AS GUD_FECHA,
					   GUD_COMENT
				FROM ".$this->tbl."
					JOIN ".$this->tblGrupo." ON GRU_IDGRUPO = GUD_IDGRUPO
					JOIN ".$this->tblGrupoUser." ON GUS_IDGRUPO = GUD_IDGRUPO AND GUS_IDUSER = GUD_IDUSER
				WHERE GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
				  AND GUD_FECHA >= GRU_FECINI
				  AND (GUD_FECHA <= GRU_FECFIN OR GRU_FECFIN IS NULL)
				  AND (GUS_VERIFY_CODE IS NULL OR GUS_VERIFY_CODE = '')
				ORDER BY GUD_IDUSER";

		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-08", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new GrupoUserDato();
			$obj->setGrupoUserDato($row);
			array_push($list, $obj);
		}
		return $list;
	}

	function getDatosPorUserDiaDeLaSemana($conMsi, $pageCode){
		global $error;
		$resultado = [];
		/*
		$sql = "SELECT d.dia_semana,
					   COALESCE(ROUND(AVG(p.GUD_DATO), 2), 0) AS media
				FROM (
						SELECT 1 AS dia_semana UNION
						SELECT 2 UNION
						SELECT 3 UNION
						SELECT 4 UNION
						SELECT 5 UNION
						SELECT 6 UNION
						SELECT 7
						) AS d
					LEFT JOIN ".$this->tbl." p ON DAYOFWEEK(p.GUD_FECHA) = d.dia_semana
							AND GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
							AND GUD_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gudIduser)."
				GROUP BY d.dia_semana
				ORDER BY d.dia_semana";
		*/
		
		$sql = "SELECT DAYOFWEEK(GUD_FECHA) AS dia_semana,
					   ROUND(AVG(GUD_DATO), 2) AS media,
					   ROUND(SUM(GUD_DATO), 2) AS suma
				FROM ".$this->tbl."
					JOIN ".$this->tblGrupo." ON GRU_IDGRUPO = GUD_IDGRUPO
				WHERE GUD_FECHA >= GRU_FECINI
					AND (GUD_FECHA <= GRU_FECFIN OR GRU_FECFIN IS NULL)
					AND GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
					AND GUD_IDUSER = ".mysqli_real_escape_string($conMsi, $this->gudIduser)."
				GROUP BY DAYOFWEEK(GUD_FECHA)
				ORDER BY dia_semana";

		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-09", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()) {
			$resultado[$row['dia_semana']] = ["media" => $row['media'], "suma" => $row['suma']];
		}
		return $resultado;
	}
	
	function getGrupoUsersDatosSemanal($conMsi, $pageCode){
		global $error;
		$list = array();
		
		$sql = "SELECT GUD_IDUSER iduser,
					   AVG(GUD_DATO) AS peso_medio,
					   YEARWEEK(GUD_FECHA, 1) AS semana,
					   STR_TO_DATE(CONCAT(YEARWEEK(GUD_FECHA, 1), ' Monday'), '%X%V %W') AS lunes_semana,
					   DATE_SUB(STR_TO_DATE(CONCAT(YEARWEEK(GUD_FECHA, 1), ' Monday'), '%X%V %W'), INTERVAL 1 WEEK) AS lunes_semana_anterior
				FROM ".$this->tbl."
					JOIN ".$this->tblGrupo." ON GRU_IDGRUPO = GUD_IDGRUPO
					JOIN ".$this->tblGrupoUser." ON GUS_IDGRUPO = GUD_IDGRUPO AND GUS_IDUSER = GUD_IDUSER
				WHERE GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
				  AND GUD_FECHA >= GRU_FECINI
				  AND (GUD_FECHA <= GRU_FECFIN OR GRU_FECFIN IS NULL)
				  AND (GUS_VERIFY_CODE IS NULL OR GUS_VERIFY_CODE = '')
				GROUP BY GUD_IDUSER, YEARWEEK(GUD_FECHA, 1)
				ORDER BY GUD_IDUSER";
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-10", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			array_push($list, $row);
		}
		return $list;
	}

	function getGrupoUsersDatosMensual($conMsi, $pageCode){
		global $error;
		$list = array();
		
		$sql = "SELECT GUD_IDUSER iduser,
					   AVG(GUD_DATO) AS peso_medio,
					   DATE_FORMAT(GUD_FECHA, '%m-%Y') AS mes
				FROM ".$this->tbl."
					JOIN ".$this->tblGrupo." ON GRU_IDGRUPO = GUD_IDGRUPO
					JOIN ".$this->tblGrupoUser." ON GUS_IDGRUPO = GUD_IDGRUPO AND GUS_IDUSER = GUD_IDUSER
				WHERE GUD_IDGRUPO = ".mysqli_real_escape_string($conMsi, $this->gudIdgrupo)."
				  AND GUD_FECHA >= GRU_FECINI
				  AND (GUD_FECHA <= GRU_FECFIN OR GRU_FECFIN IS NULL)
				  AND (GUS_VERIFY_CODE IS NULL OR GUS_VERIFY_CODE = '')
				GROUP BY GUD_IDUSER, DATE_FORMAT(GUD_FECHA, '%m-%Y')
				ORDER BY GUD_IDUSER";
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> GUD-SQL-10", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			array_push($list, $row);
		}
		return $list;
	}
}
?>