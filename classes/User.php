<?php
include_once 'classes/Funciones.php';

class User {
	//private $dbHost     = "localhost";
	//private $dbUsername = "root";
	//private $dbPassword = "";
	//private $dbName     = "codexworld";
	private $userTbl    = 'USER';
	private $grupoUserTbl    = 'GRUPO_USER';
	private $grupoTbl    = 'GRUPO';
	private $tiempoTbl    = 'TIEMPO';
	private $idiomaTbl    = 'IDIOMA';
	
	private $useIduser;
	private $useName;
	private $useLastname;
	private $useSex;
	private $useMail;
	private $useIdidioma;
	private $useIdunidad;
	private $useMostrarPeso;
	private $useAuthProvider;
	private $useAuthUid;
	private $usePassword;
	private $usePicture;
	private $useVerifyCode;
	private $useIdstatus;
	private $useReminderCode;
	private $useReminderExpir;

	
	function __construct(){}
	
	public function getUseIduser() {
		return $this->useIduser;
	}

	public function getUseName() {
		return $this->useName;
	}

	public function getUseLastname() {
		return $this->useLastname;
	}

	public function getUseSex() {
		return $this->useSex;
	}

	public function getUseMail() {
		return $this->useMail;
	}

	public function getUseIdidioma() {
		return $this->useIdidioma;
	}

	public function getUseIdunidad() {
		return $this->useIdunidad;
	}

	public function getUseMostrarPeso() {
		return $this->useMostrarPeso;
	}

	public function setUseMostrarPeso($useMostrarPeso) {
		$this->useMostrarPeso = $useMostrarPeso;
	}

	public function getUseAuthProvider() {
		return $this->useAuthProvider;
	}

	public function getUseAuthUid() {
		return $this->useAuthUid;
	}

	public function getUsePassword() {
		return $this->usePassword;
	}

	public function getUsePicture() {
		return $this->usePicture;
	}

	public function getUseVerifyCode() {
		return $this->useVerifyCode;
	}

	public function getUseIdstatus() {
		return $this->useIdstatus;
	}

	public function getUseReminderCode() {
		return $this->useReminderCode;
	}

	public function getUseReminderExpir() {
		return $this->useReminderExpir;
	}

	public function setUseIduser($useIduser) {
		$this->useIduser = $useIduser;
	}

	public function setUseName($useName) {
		$this->useName = trim($useName);
	}

	public function setUseLastname($useLastname) {
		$this->useLastname = trim($useLastname);
	}

	public function setUseSex($useSex) {
		$this->useSex = $useSex;
	}

	public function setUseMail($useMail) {
		$this->useMail = strtolower(trim($useMail));
	}

	public function setUseIdidioma($useIdidioma) {
		$this->useIdidioma = $useIdidioma;
	}

	public function setUseIdunidad($useIdunidad) {
		$this->useIdunidad = $useIdunidad;
	}

	public function setUseAuthProvider($useAuthProvider) {
		$this->useAuthProvider = $useAuthProvider;
	}

	public function setUseAuthUid($useAuthUid) {
		$this->useAuthUid = $useAuthUid;
	}

	public function setUsePassword($usePassword) {
		$this->usePassword = $usePassword;
	}

	public function setUsePicture($usePicture) {
		$this->usePicture = $usePicture;
	}

	public function setUseVerifyCode($useVerifyCode) {
		$this->useVerifyCode = $useVerifyCode;
	}

	public function setUseIdstatus($useIdstatus) {
		$this->useIdstatus = $useIdstatus;
	}

	public function setUseReminderCode($useReminderCode) {
		$this->useReminderCode = $useReminderCode;
	}

	public function setUseReminderExpir($useReminderExpir) {
		$this->useReminderExpir = $useReminderExpir;
	}

	function setUser($data = array()){
		$this->useIduser		= $data["USE_IDUSER"];
		$this->useName			= $data["USE_NAME"];
		$this->useLastname		= $data["USE_LASTNAME"];
		$this->useSex			= $data["USE_SEX"];
		$this->useMail			= $data["USE_MAIL"];
		$this->useAuthProvider	= $data["USE_AUTH_PROVIDER"];
		$this->useAuthUid		= $data["USE_AUTH_UID"];
		$this->usePassword		= $data["USE_PASSWORD"];
		$this->useIdidioma		= $data["USE_IDIDIOMA"];
		$this->useIdunidad		= $data["USE_IDUNIDAD"];
		$this->useMostrarPeso	= $data["USE_MOSTRAR_PESO"];
		$this->usePicture		= $data["USE_PICTURE"];
		$this->useVerifyCode	= $data["USE_VERIFY_CODE"];
		$this->useIdstatus		= $data["USE_IDSTATUS"];
		$this->useReminderCode	= $data["USE_REMINDER_CODE"];
		$this->useReminderExpir	= $data["USE_REMINDER_EXPIR"];
	}
		
	function validateUser($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM USER
				WHERE USE_IDSTATUS = 1
				  AND UPPER(USE_MAIL) = UPPER('".mysqli_real_escape_string($conMsi, $this->useMail)."')
				  AND USE_PASSWORD = '".SHA1(mysqli_real_escape_string($conMsi, $this->usePassword))."'";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-01", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)==1){
			$this->setUser($result->fetch_assoc());
			return true;
		}else return false;
	}
	
	function setUserWithMail($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM USER
				WHERE USE_IDSTATUS = 1
				  AND UPPER(USE_MAIL) = UPPER('".mysqli_real_escape_string($conMsi, $this->useMail)."')";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-02", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)==1){
			$this->setUser($result->fetch_assoc());
			return true;
		}else return false;
	}

	function setUserWithId($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM USER
				WHERE USE_IDSTATUS = 1
				  AND USE_IDUSER = ".mysqli_real_escape_string($conMsi, $this->useIduser);

		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-03", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)==1){
			$this->setUser($result->fetch_assoc());
			return true;
		}else return false;
	}
	
	function setUserWithIdNoStatus($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM USER
				WHERE USE_IDSTATUS != 99
				  AND USE_IDUSER = ".mysqli_real_escape_string($conMsi, $this->useIduser);
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-04", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)==1){
			$this->setUser($result->fetch_assoc());
			return true;
		}else return false;
	}
	
	function checkResetUserPassword($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM USER
				WHERE USE_IDUSER = '".mysqli_real_escape_string($conMsi, $this->useIduser)."'
				  AND USE_REMINDER_CODE = '".mysqli_real_escape_string($conMsi, $this->useReminderCode)."'
				  AND USE_IDSTATUS = 1
				  AND USE_REMINDER_EXPIR + INTERVAL 2 HOUR > NOW()";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-05", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)==1){
			$this->setUser($result->fetch_assoc());
			return true;
		}else return false;
	}
	
	function checkUserMailAlreadyExistsSet($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM USER
				WHERE USE_MAIL = '".mysqli_real_escape_string($conMsi, trim($this->useMail))."'";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-06", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)>0){
			$this->setUser($result->fetch_assoc());
			return true;
		}else return false;
	}
	
	function checkMailAlreadyExists($conMsi, $pageCode, $newMail){
		global $error;
		
		$sql = "SELECT *
				FROM USER
				WHERE USE_MAIL = '".mysqli_real_escape_string($conMsi, trim($newMail))."'
				  AND USE_IDUSER != ".mysqli_real_escape_string($conMsi, $this->useIduser);
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-07", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)>0){
			return true;
		}else return false;
	}
	
	function checkMailAlreadyExistsPendiente($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM USER
				WHERE USE_MAIL = '".mysqli_real_escape_string($conMsi, $this->useMail)."'
				  AND USE_IDSTATUS = 0
				  AND USE_PASSWORD IS NULL";

		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-07", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)>0){
			$userData = new User();
			$userData->setUser($result->fetch_assoc());
			$this->setUseIduser($userData->getUseIduser());
			return true;
		}else return false;
	}
	
	function checkMailAlreadyExistsSet($conMsi, $pageCode, $newMail){
		global $error;
		
		$sql = "SELECT *
				FROM USER
				WHERE UPPER(USE_MAIL) = UPPER('".mysqli_real_escape_string($conMsi, trim($newMail))."')
				  AND USE_IDUSER != ".mysqli_real_escape_string($conMsi, $this->useIduser);
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-08", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)>0){
			$this->setUser($result->fetch_assoc());
			return true;
		}else return false;
	}

	function checkUserPendiente($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT *
				FROM USER
				WHERE USE_IDUSER = ".mysqli_real_escape_string($conMsi, $this->useIduser)."
				  AND (USE_PASSWORD IS NULL OR USE_PASSWORD = '')";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-08", $sql." -> ".$conMsi->error, 3);}
		if (mysqli_num_rows($result)>0){
			$this->setUser($result->fetch_assoc());
			return true;
		}else return false;
	}
	
	function checkUserGoogle($conMsi, $pageCode){
		
		$this->useIduser= mysqli_real_escape_string($conMsi, $this->useIduser);
		$this->useMail= mysqli_real_escape_string($conMsi, $this->useMail);
		$this->useAuthProvider= mysqli_real_escape_string($conMsi, $this->useAuthProvider);
		$this->useAuthUid= mysqli_real_escape_string($conMsi, $this->useAuthUid);
		$this->useSex= mysqli_real_escape_string($conMsi, $this->useSex);
		$this->useIdidm= mysqli_real_escape_string($conMsi, $this->useIdidm);
		$this->usePicture= mysqli_real_escape_string($conMsi, $this->usePicture);
		$this->useLink= mysqli_real_escape_string($conMsi, $this->useLink);
		$this->useName= mysqli_real_escape_string($conMsi, $this->useName);
		$this->useLastname= mysqli_real_escape_string($conMsi, $this->useLastname);
		
		//Check whether user data already exists in database
		//$prevQuery = "SELECT * FROM ".$this->userTbl." WHERE USE_AUTH_PROVIDER = '".$userData['oauth_provider']."' AND USE_AUTH_UID = '".$userData['oauth_uid']."' AND USE_MAIL = '".$userData['email']."'";
		$prevQuery = "SELECT * FROM ".$this->userTbl." WHERE UPPER(USE_MAIL) = UPPER('".mysqli_real_escape_string($conMsi, $this->useMail)."')";
		if(!$prevResult = $conMsi->query($prevQuery)){ $error = true; rolLog("$pageCode> USE-SQL-09", $prevQuery." -> ".$conMsi->error, 3);}
		if($prevResult->num_rows > 0){
			$userDataCheck = array();
			$userDataCheck= $prevResult->fetch_assoc();
			// if exists, si aun no tiene los auth metidos, se meten
			if (($userDataCheck['USE_AUTH_PROVIDER']=="" && $userDataCheck['USE_AUTH_UID']=="") || ($userDataCheck['USE_AUTH_PROVIDER']==$this->useAuthProvider && $userDataCheck['USE_AUTH_UID']==$this->useAuthUid)){
				$query = "UPDATE ".$this->userTbl." 
							SET USE_AUTH_PROVIDER = '".mysqli_real_escape_string($conMsi, $this->useAuthProvider)."', 
								USE_AUTH_UID = '".mysqli_real_escape_string($conMsi, $this->useAuthUid)."', 
								USE_SEX= '".mysqli_real_escape_string($conMsi, $this->useSex)."', 
								USE_PICTURE = '".mysqli_real_escape_string($conMsi, $this->usePicture)."', 
								USE_LINK = '".mysqli_real_escape_string($conMsi, $this->useLink)."', 
								USE_IDSTATUS = 1 
							WHERE (USE_AUTH_PROVIDER = '".mysqli_real_escape_string($conMsi, $this->useAuthProvider)."' OR USE_AUTH_PROVIDER is null) 
							  AND (USE_AUTH_UID = '".mysqli_real_escape_string($conMsi, $this->useAuthUid)."' OR USE_AUTH_UID is null) AND UPPER(USE_MAIL) = UPPER('".mysqli_real_escape_string($conMsi, $this->useMail)."')";
				if(!$conMsi->query($query)){ $error = true; rolLog("$pageCode> USE-SQL-10", $query." -> ".$conMsi->error, 3);}
			}
		}else{
			
			$this->useName = $this->checkNameForUrl($conMsi, $pageCode, $this->useName);
			
			//Insert user data
			$query = "INSERT INTO ".$this->userTbl." 
						SET USE_AUTH_PROVIDER = '".mysqli_real_escape_string($conMsi, $this->useAuthProvider)."', 
							USE_AUTH_UID = '".mysqli_real_escape_string($conMsi, $this->useAuthUid)."', 
							USE_NAME = '".mysqli_real_escape_string($conMsi, $this->useName)."', 
							USE_LASTNAME = '".mysqli_real_escape_string($conMsi, $this->useLastname)."', 
							USE_MAIL = '".mysqli_real_escape_string($conMsi, $this->useMail)."', 
							USE_SEX= '".mysqli_real_escape_string($conMsi, $this->useSex)."', 
							USE_PICTURE = '".mysqli_real_escape_string($conMsi, $this->usePicture)."', 
							USE_LINK = '".mysqli_real_escape_string($conMsi, $this->useLink)."', 
							USE_IDSTATUS = 1";
			if(!$conMsi->query($query)){ $error = true; rolLog("$pageCode> USE-SQL-11", $query." -> ".$conMsi->error, 3);}
		}
		//Get user data from the database
		if(!$result = $conMsi->query($prevQuery)){ $error = true; rolLog("$pageCode> USE-SQL-12", $prevQuery." -> ".$conMsi->error, 3);}
		
		$this->setUser($result->fetch_assoc());
	}
		
	public function getUsers($conMsi, $pageCode){
		$list = array();
		$sql = "SELECT *
				FROM USER
				WHERE USE_IDSTATUS = 1
				ORDER BY USE_IDUSER";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-13", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new User();
			$obj->setUser($row);
			array_push($list, $obj);
		}
		return $list;
	}
	
		
	function getNewCode(){
		$str = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789";
		$codValida = "";for($iVal=0;$iVal<15;$iVal++) {$codValida .= substr($str,rand(0,57),1);}
		return $codValida;
	}
	
	function updatePassword($conMsi, $pageCode){
		global $error;
		
		$sql = "UPDATE USER SET USE_PASSWORD = '".SHA1(mysqli_real_escape_string($conMsi, $this->usePassword))."',
						USE_REMINDER_CODE = '',
						USE_REMINDER_EXPIR = null
				WHERE USE_IDUSER = ".mysqli_real_escape_string($conMsi, $this->useIduser);
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-1", $sql." -> ".$conMsi->error, 3);}
		
		if (mysqli_affected_rows($conMsi)==1)
			return true;
			else return false;
	}
	
	function updateProfile($conMsi, $pageCode){
		global $error;
		
		$sql = "UPDATE USER SET
					USE_NAME = '".mysqli_real_escape_string($conMsi, $this->useName)."',
					USE_LASTNAME = '".mysqli_real_escape_string($conMsi, $this->useLastname)."',
					USE_IDIDIOMA = ".mysqli_real_escape_string($conMsi, $this->useIdidioma).",
					USE_IDUNIDAD = ".mysqli_real_escape_string($conMsi, $this->useIdunidad).",
					USE_MOSTRAR_PESO = '".mysqli_real_escape_string($conMsi, $this->useMostrarPeso)."',
					".($this->usePassword!=""?"USE_PASSWORD = '".SHA1(mysqli_real_escape_string($conMsi, $this->usePassword))."',":"")."
					USE_MAIL = '".mysqli_real_escape_string($conMsi, $this->useMail)."',
					USE_IDSTATUS = 1
				WHERE USE_IDUSER = ".mysqli_real_escape_string($conMsi, $this->useIduser);

		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-15", $sql." -> ".$conMsi->error, 3);}
		else return true;
	}
	
	function updateVerifyUser($conMsi, $pageCode){
		global $error;
		
		$sql = "UPDATE USER SET USE_IDSTATUS = 1, USE_VERIFY_CODE = ''
				WHERE USE_IDUSER = ".mysqli_real_escape_string($conMsi, $this->useIduser)."
				  AND USE_VERIFY_CODE = '".mysqli_real_escape_string($conMsi, $this->useVerifyCode)."'";
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-16", $sql." -> ".$conMsi->error, 3);}
		
		if (mysqli_affected_rows($conMsi)==1)
			return true;
			else return false;
	}
	
	function updateReminderCode($conMsi, $pageCode){
		global $error;
		
		$sql = "UPDATE USER SET USE_REMINDER_CODE = '".mysqli_real_escape_string($conMsi, $this->useReminderCode)."',
					USE_REMINDER_EXPIR = NOW()
				WHERE USE_IDUSER = ".mysqli_real_escape_string($conMsi, $this->useIduser);
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-17", $sql." -> ".$conMsi->error, 3);}
		
		if (mysqli_affected_rows($conMsi)==1)
			return true;
			else return false;
	}
	
	function insertProfile($conMsi, $pageCode){
		global $error;
		
		$sql = "INSERT INTO ".$this->userTbl."
				SET USE_NAME = '".mysqli_real_escape_string($conMsi, $this->useName)."',
					USE_LASTNAME = '".mysqli_real_escape_string($conMsi, $this->useLastname)."',
					USE_MAIL = '".mysqli_real_escape_string($conMsi, $this->useMail)."',
					USE_IDIDIOMA = ".mysqli_real_escape_string($conMsi, $this->useIdidioma).",
					USE_IDUNIDAD = ".mysqli_real_escape_string($conMsi, $this->useIdunidad).",
					USE_MOSTRAR_PESO = '".mysqli_real_escape_string($conMsi, $this->useMostrarPeso)."',
					USE_PASSWORD = '".SHA1(mysqli_real_escape_string($conMsi, $this->usePassword))."',
					USE_VERIFY_CODE = '".mysqli_real_escape_string($conMsi, $this->useVerifyCode)."'";
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-18", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			$this->setUseIduser($conMsi->insert_id);
			return true;
		}else return false;
	}
	
	function insertProfilePendiente($conMsi, $pageCode){
		global $error;
		
		$sql = "INSERT INTO ".$this->userTbl."
				SET USE_NAME = '".mysqli_real_escape_string($conMsi, $this->useName)."',
					USE_MAIL = '".mysqli_real_escape_string($conMsi, $this->useMail)."'";
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-19", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			$this->setUseIduser($conMsi->insert_id);
			return true;
		}else return false;
	}
	
	function insertContact($conMsi, $pageCode){
		global $error;
		
		$sql = "INSERT IGNORE INTO ".$this->userTbl."
				SET USE_NAME = '".mysqli_real_escape_string($conMsi, $this->useName)."',
					USE_MAIL = '".mysqli_real_escape_string($conMsi, $this->useMail)."'";
		
		if(!$conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-20", $sql." -> ".$conMsi->error, 3);}
		
		if (!$error){
			$this->setUseIduser($conMsi->insert_id);
			return true;
		}else return false;
	}
	
	function checkNameForUrl($conMsi, $pageCode, $name){
		global $error;
		
		$urlredir = $name;
		$urlProbar = $urlredir;
		$urlredir_unica = false;
		$duplicado = 0;
		while (!$urlredir_unica){
			if ($duplicado>0) $urlProbar = $urlredir."-".$duplicado;
			
			$sql = "SELECT * FROM ".$this->userTbl."
					WHERE USE_NAME = '$urlProbar'";
			if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-21", $sql." -> ".$conMsi->error, 3);}
			if (mysqli_num_rows($result)>0){
				$duplicado++;
			}else $urlredir_unica = true;				
		}
		return $urlProbar;
	}
	
	function getUsersSharingGroupsWithMe($conMsi, $pageCode){
		global $error;
		$list = array();
		
		$sql = "SELECT * FROM ".$this->userTbl."
				WHERE USE_IDUSER IN (SELECT gu1.GUS_IDUSER 
									 FROM ".$this->grupoUserTbl." gu1
									 WHERE gu1.GUS_IDGRUPO IN (SELECT gu2.GUS_IDGRUPO 
														   FROM ".$this->grupoUserTbl." gu2
														   WHERE gu2.GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->useIduser)."))";
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-22", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			$obj = new User();
			$obj->setUser($row);
			array_push($list, $obj);
		}
		return $list;
	}
	
	function getFirstGroupDate($conMsi, $pageCode){
		global $error;
		
		$sql = "SELECT MIN(GRU_FECINI) min_fecini
				FROM ".$this->grupoTbl." 
				WHERE GRU_IDGRUPO IN (SELECT GUS_IDGRUPO 
									  FROM ".$this->grupoUserTbl."
									  WHERE GUS_IDUSER = ".mysqli_real_escape_string($conMsi, $this->useIduser).")";
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-23", $sql." -> ".$conMsi->error, 3);}
		if ($row = $result->fetch_assoc()){
			return $row["min_fecini"];
		}else {
			return null;
		}
	}
	
	function getUsersMailPendientes($conMsi, $pageCode, $idioma){
		global $error;
		$list = array();
		
		$sql = "SELECT *
				FROM ".$this->userTbl."
					LEFT JOIN ".$this->grupoUserTbl." ON GUS_IDUSER = USE_IDUSER
					LEFT JOIN ".$this->grupoTbl." ON GRU_IDGRUPO = GUS_IDGRUPO
    				LEFT JOIN ".$this->tiempoTbl." ON GRU_IDTIEMPO = TIE_IDTIEMPO
    				LEFT JOIN ".$this->idiomaTbl." ON USE_IDIDIOMA = IDM_IDIDIOMA
				WHERE IDM_LOCALE = '".mysqli_real_escape_string($conMsi, $idioma)."'
				  AND USE_IDSTATUS = 1
				  AND GRU_STATUS = 1
				  AND GRU_FECINI <= NOW()
				  AND (GRU_FECFIN is null OR GRU_FECFIN = '' OR GRU_FECFIN >= NOW())
				  AND GUS_AVISO_RETRASO != 'S'
				ORDER BY USE_IDUSER";
		
		if(!$result = $conMsi->query($sql)){ $error = true; rolLog("$pageCode> USE-SQL-24", $sql." -> ".$conMsi->error, 3);}
		while ($row = $result->fetch_assoc()){
			array_push($list, $row);
		}
		return $list;
	}
}
?>