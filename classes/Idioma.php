<?php
class Idioma {
	private $tbl = 'IDIOMA';
	
	private $idmIdidioma;
	private $idmName;
	private $idmLocale;
	private $idmHtmlCode;
	private $idmActivo;
	private $idmOrder;
	
	public function setIdmIdidioma($valor) { $this->idmIdidioma= trim($valor); }
	public function getIdmIdidioma() { return $this->idmIdidioma; }

	public function setIdmName($valor) { $this->idmName= trim($valor); }
	public function getIdmName() { return $this->idmName; }
	
	public function setIdmLocale($valor) { $this->idmLocale= trim($valor); }
	public function getIdmLocale() { return $this->idmLocale; }
		
	public function setIdmHtmlCode($valor) { $this->idmHtmlCode= trim($valor); }
	public function getIdmHtmlCode() { return $this->idmHtmlCode; }
	
	public function setIdmActivo($valor) { $this->idmActivo= trim($valor); }
	public function getIdmActivo() { return $this->idmActivo; }
	
	public function setIdmOrder($valor) { $this->idmOrder= trim($valor); }
	public function getIdmOrder() { return $this->idmOrder; }
	
	function __construct(){
	}

	function setIdioma($data = array()){
		$this->idmIdidioma	= $data["IDM_IDIDIOMA"];
		$this->idmName		= $data["IDM_NAME"];
		$this->idmLocale	= $data["IDM_LOCALE"];
		$this->idmHtmlCode	= $data["IDM_HTML_CODE"];
		$this->idmActivo	= $data["IDM_ACTIVO"];
		$this->idmOrder		= $data["IDM_ORDER"];
	}
	
	public function getIdioma($conMsi, $pageCode){
		$sql = "SELECT * FROM ".$this->tbl." WHERE IDM_ACTIVO = 1 AND IDM_IDIDIOMA = ".mysqli_real_escape_string($conMsi, $this->idmIdidioma);
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> IDM-SQL-04", $sql." -> ".$conMsi->error, 3);}
		if($row = $result->fetch_assoc()){
			$this->setIdioma($row);
			return true;
		}else return false;
		
	}
	
	public function getIdiomasActive($conMsi, $pageCode){
		$list = array();
		$sql = "SELECT * FROM ".$this->tbl." WHERE IDM_ACTIVO = 1 ORDER BY IDM_ORDER";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> IDM-SQL-01", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$objIdiom = new Idioma();
			$objIdiom->setIdioma($row);
			array_push($list, $objIdiom);
		}
		return $list;
	}
	
	public function getIdmByHtmlCodeOrDefault($conMsi, $pageCode){
		$sql = "SELECT * FROM ".$this->tbl." WHERE IDM_ACTIVO = 1 AND IDM_HTML_CODE = '".mysqli_real_escape_string($conMsi, $this->idmHtmlCode)."'";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> IDM-SQL-02", $sql." -> ".$conMsi->error, 3);}
		if($result->num_rows == 0){
			// default language
			$sql = "SELECT * FROM ".$this->tbl." WHERE IDM_ACTIVO = 1 AND IDM_IDIDIOMA = 1";
			if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> IDM-SQL-03", $sql." -> ".$conMsi->error, 3);}
		}
		$this->setIdioma($result->fetch_assoc());
	}	
	
}
?>