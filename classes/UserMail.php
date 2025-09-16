<?php
class UserMail {
	//private $dbHost     = "localhost";
	//private $dbUsername = "root";
	//private $dbPassword = "";
	//private $dbName     = "codexworld";
	private $userTbl    = 'USER_MAIL';

	
	private $usmIdusm;
	private $usmIduser;
	private $usmFrom;
	private $usmTo;
	private $usmCc;
	private $usmReplyto;
	private $usmSubject;
	private $usmBody;
	private $usmUsucre;
	private $usmIdusmReenvio;
	private $usmSent;
	
	public function setUsmIdusm($valor) { $this->usmIdusm = trim($valor); }
	public function getUsmIdusm() { return $this->usmIdusm; }
	
	public function setUsmIduser($valor) { $this->usmIduser = trim($valor); }
	public function getUsmIduser() { return $this->usmIduser; }
	
	public function setUsmFrom($valor) { $this->usmFrom = trim($valor); }
	public function getUsmFrom() { return $this->usmFrom; }
	
	public function setUsmTo($valor) { $this->usmTo = trim($valor); }
	public function getUsmTo() { return $this->usmTo; }
	
	public function setUsmCc($valor) { $this->usmCc = trim($valor); }
	public function getUsmCc() { return $this->usmCc; }
	
	public function setUsmReplyto($valor) { $this->usmReplyto = trim($valor); }
	public function getUsmReplyto() { return $this->usmReplyto; }
	
	public function setUsmSubject($valor) { $this->usmSubject = trim($valor); }
	public function getUsmSubject() { return $this->usmSubject; }
	
	public function setUsmBody($valor) { $this->usmBody = trim($valor); }
	public function getUsmBody() { return $this->usmBody; }
	
	public function setUsmUsucre($valor) { $this->usmUsucre = trim($valor); }
	public function getUsmUsucre() { return $this->usmUsucre; }
	
	public function setUsmIdusmReenvio($valor) { $this->usmIdusmReenvio = trim($valor); }
	public function getUsmIdusmReenvio() { return $this->usmIdusmReenvio; }
	
	public function setUsmSent($valor) { $this->usmSent= trim($valor); }
	public function getUsmSent() { return $this->usmSent; }
	
	function __construct(){
	}
		
	function setUserMail($data = array()){
		$this->usmIdusm			= $data["USM_IDUSM"];
		$this->usmIduser		= $data["USM_IDUSE"];
		$this->usmFrom			= $data["USM_FROM"];
		$this->usmTo			= $data["USM_TO"];
		$this->usmCC			= $data["USM_CC"];
		$this->usmReplyto		= $data["USM_REPLYTO"];
		$this->usmSubject		= $data["USM_SUBJECT"];
		$this->usmBody			= $data["USM_BODY"];
		$this->usmUsucre		= $data["USM_USUCRE"];
		$this->usmIdusmReenvio	= $data["USM_IDUSM_REENVIO"];
		$this->usmSent			= $data["USM_SENT"];
	}
	
	function insertUserMail($conMsi, $pageCode){
		global $error;

		$sql = "INSERT INTO ".$this->userTbl."
				SET USM_IDUSE = ".mysqli_real_escape_string($conMsi, $this->usmIduser).",
					USM_FROM = '".mysqli_real_escape_string($conMsi, $this->usmFrom)."',
					USM_TO = '".mysqli_real_escape_string($conMsi, $this->usmTo)."',
					USM_CC = '".mysqli_real_escape_string($conMsi, $this->usmCC)."',
					USM_REPLYTO = '".mysqli_real_escape_string($conMsi, $this->usmReplyto)."',
					USM_SUBJECT = '".mysqli_real_escape_string($conMsi, $this->usmSubject)."',
					USM_BODY = '".mysqli_real_escape_string($conMsi, $this->usmBody)."',
					USM_USUCRE = '".mysqli_real_escape_string($conMsi, $this->usmUsucre)."',
					USM_SENT = ".mysqli_real_escape_string($conMsi, $this->usmSent).",
					USM_IDUSM_REENVIO = '".mysqli_real_escape_string($conMsi, $this->usmIdusmReenvio)."'";
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> USM-SQL-01", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			$this->setUsmIdusm($conMsi->insert_id);
			return true;
		}else return false;
	}
	
}
?>