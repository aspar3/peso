<?php
// require_once "lib/PHPMailer-v5.2.23/PHPMailerAutoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'lib/PHPMailer6_9_1/src/Exception.php';
require 'lib/PHPMailer6_9_1/src/PHPMailer.php';
require 'lib/PHPMailer6_9_1/src/SMTP.php';

function enviarMailSMTP($from, $to, $cc, $replyTo, $subject, $body, $iduserMail){
	
	global $mailHostExt, $mailPortExt, $mailPortSslExt, $mailUserExt, $mailPassExt;
	global $mailHost, $mailPort, $mailPortSsl, $mailUser, $mailPass;
	global $mailAlertasAdmin;
	global $idMailReenviado;
	global $codigoAbrev;
	global $nombreGeneral;
	
	//Si hay servidor externo, se utiliza. Si no, se utiliza el normal
	$enviarSSL = false;
	if ($mailHostExt!=""){
		$host = $mailHostExt;
		if ($mailPortSslExt!=""){
			$port = $mailPortSslExt;
			$enviarSSL = true;
		}else $port = $mailPortExt;
		$username = $mailUserExt;
		$password = $mailPassExt;
	}else{
		$host = $mailHost;
		if ($mailPortSsl!=""){
			$port = $mailPortSsl;
			$enviarSSL = true;
		}else $port = $mailPort;
		$username = $mailUser;
		$password = $mailPass;
	}
	
	if ($replyTo=="") $replyTo = $from;
	
	// si se indica un id de psicologo, quiere decir que se quiere guardar ese email para ese psicologo
	if ($iduserMail!=""){
		global $conMsi;
		global $pageCode;
		
		include_once 'classes/UserMail.php';
		$userMail = new UserMail();
		$userMail->setUsmIduser($iduserMail);
		$userMail->setUsmFrom($from);
		$userMail->setUsmTo($to);
		$userMail->setUsmCc($cc);
		$userMail->setUsmReplyto($replyTo);
		$userMail->setUsmSubject($subject);
		$userMail->setUsmBody($body);
	}
	
	$mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
	
	$mail->IsSMTP();
	$mail->CharSet = 'UTF-8';
	$mail->Encoding = 'base64';
	$mail->Host = $host;
	$mail->Port = $port;
	$mail->SMTPAuth = true;
	if ($enviarSSL)
		$mail->SMTPSecure = 'ssl';
		$mail->Username = $username;
		$mail->Password = $password;
		
		try {
			$mail->AddReplyTo($replyTo);
			$mail->AddAddress($to);
			if ($cc!="") $mail->AddCC($cc);
			$mail->SetFrom($from, $nombreGeneral);
			$mail->Subject = $subject;
			$mail->Body = $body;
			$mail->Send();
			$error = false;
			// si ha ido bien, se guarda como correcto
			if ($iduserMail!=""){
				$userMail->setUsmSent("1");
				$userMail->insertUserMail($conMsi, $pageCode);
				$idMailReenviado = $userMail->getUsmIdusm();
			}
		} catch (phpmailerException $e) {
			$errorTxt = $e->errorMessage()."\n".$mail->ErrorInfo;
			$error = true;
		} catch (Exception $e) {
			$errorTxt = $e->errorMessage()."\n".$mail->ErrorInfo;
			$error = true;
		}
		
		if ($error){
			// si ha ido mal, se guarda como error
			if ($iduserMail!=""){
				$userMail->setUsmSent("0");
				$userMail->insertUserMail($conMsi, $pageCode);
				$idMailReenviado = $userMail->getUsmIdusm();
			}
			enviarMailAlert($from, $mailAlertasAdmin, "", $codigoAbrev.": Error enviando mail Externo por SMTP", $errorTxt."\nHost = $host\nPort = $port\nUsername = $username\nPassword = $password\nPara: $to\nResponder a:$replyTo\nDe: $from\nAsunto: $subject\nCuerpo: $body");
			return false;
		} else return true;
}

function enviarMailSMTPadjunto($from, $to, $cc, $replyTo, $subject, $body, $adjNombre, $adjContent, $adjTipo, $iduserMail){
	
	// $adjTipo indica el tipo de adjunto
	// 1: vendra el nombre del adjunto con su ruta
	// 2: vendra el contenido del adjunto, por lo que se adjuntara como un string
	
	global $mailHostExt, $mailPortExt, $mailPortSslExt, $mailUserExt, $mailPassExt;
	global $mailHost, $mailPort, $mailPortSsl, $mailUser, $mailPass;
	global $mailAlertasAdmin;
	global $codigoAbrev;
	global $nombreGeneral;
	
	//Si hay servidor externo, se utiliza. Si no, se utiliza el normal
	$enviarSSL = false;
	if ($mailHostExt!=""){
		$host = $mailHostExt;
		if ($mailPortSslExt!=""){
			$port = $mailPortSslExt;
			$enviarSSL = true;
		}else $port = $mailPortExt;
		$username = $mailUserExt;
		$password = $mailPassExt;
	}else{
		$host = $mailHost;
		if ($mailPortSsl!=""){
			$port = $mailPortSsl;
			$enviarSSL = true;
		}else $port = $mailPort;
		$username = $mailUser;
		$password = $mailPass;
	}
	
	if ($replyTo=="") $replyTo = $from;
	
	// si se indica un id de psicologo, quiere decir que se quiere guardar ese email para ese psicologo
	if ($iduserMail!=""){
		global $conMsi;
		global $pageCode;
		
		include_once 'classes/UserMail.php';
		$userMail = new UserMail();
		$userMail->setUsmIduser($iduserMail);
		$userMail->setUsmFrom($from);
		$userMail->setUsmTo($to);
		$userMail->setUsmCc($cc);
		$userMail->setUsmReplyto($replyTo);
		$userMail->setUsmSubject($subject);
		$userMail->setUsmBody($body);
	}
	
	$mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
	
	$mail->IsSMTP();
	$mail->Host = $host;
	$mail->Port = $port;
	$mail->SMTPAuth = true;
	if ($enviarSSL)
		$mail->SMTPSecure = 'ssl';
		$mail->Username = $username;
		$mail->Password = $password;
		
		try {
			//$mail->isHTML();
			$mail->AddReplyTo($replyTo);
			$mail->AddAddress($to);
			if ($cc!="") $mail->AddCC($cc);
			$mail->SetFrom($from, $nombreGeneral);
			$mail->Subject = $subject;
			$mail->Body = $body;
			if ($adjContent!=""){
				if ($adjTipo=="1") $mail->addaddAttachment($adjContent, $adjNombre);
				else $mail->addStringAttachment($adjContent, $adjNombre);
				//$mail->AddStringAttachment($adjContent, $adjNombre, "base64", "text/calendar; charset=utf-8; method=REQUEST");
			}
			
			//$mail->addCustomHeader($name);
			$mail->Send();
			if ($iduserMail!=""){
				$userMail->setUsmSent("1");
				$userMail->insertUserMail($conMsi, $pageCode);
				$idMailReenviado = $userMail->getUsmIdusm();
			}
			$error = false;
		} catch (phpmailerException $e) {
			$errorTxt = $e->errorMessage()."\n".$mail->ErrorInfo;
			$error = true;
		} catch (Exception $e) {
			$errorTxt = $e->errorMessage()."\n".$mail->ErrorInfo;
			$error = true;
		}
		
		if ($error){
			// si ha ido mal, se guarda como error
			if ($iduserMail!=""){
				$userMail->setUsmSent("0");
				$userMail->insertUserMail($conMsi, $pageCode);
				$idMailReenviado = $userMail->getUsmIdusm();
			}
			enviarMailAlert($from, $mailAlertasAdmin, "", $codigoAbrev.": Error enviado mail Externo con adjunto por SMTP", $errorTxt."\nHost = $host\nPort = $port\nUsername = $username\nPassword = $password\nPara: $to\nResponder a:$replyTo\nDe: $from\nAsunto: $subject\nCuerpo: $body");
			return false;
		} else return true;
}

function enviarMailInfo($from, $to, $replyTo, $subject, $body){
	
	global $mailHost, $mailPort, $mailPortSsl, $mailUser, $mailPass;
	global $mailAlertasAdmin;
	global $codigoAbrev;
	global $nombreGeneral;
	
	//Si hay servidor externo, se utiliza. Si no, se utiliza el normal
	$enviarSSL = false;
	$host = $mailHost;
	if ($mailPortSsl!=""){
		$port = $mailPortSsl;
		$enviarSSL = true;
	}else $port = $mailPort;
	$username = $mailUser;
	$password = $mailPass;
	
	if ($replyTo=="") $replyTo = $from;
	
	$mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
	
	$mail->IsSMTP();
	$mail->Host = $host;
	$mail->Port = $port;
	$mail->SMTPAuth = true;
	if ($enviarSSL)
		$mail->SMTPSecure = 'ssl';
		$mail->Username = $username;
		$mail->Password = $password;
		
		try {
			$mail->AddReplyTo($replyTo);
			$mail->AddAddress($to);
			if ($cc!="") $mail->AddCC($cc);
			$mail->SetFrom($from, $nombreGeneral);
			$mail->Subject = $subject;
			$mail->Body = $body;
			$mail->Send();
			$error = false;
		} catch (phpmailerException $e) {
			$errorTxt = $e->errorMessage()."\n".$mail->ErrorInfo;
			$error = true;
		} catch (Exception $e) {
			$errorTxt = $e->errorMessage()."\n".$mail->ErrorInfo;
			$error = true;
		}
		
		if ($error){
			enviarMailAlert($from, $mailAlertasAdmin, "", $codigoAbrev.": Error enviado mail INFO por SMTP", $errorTxt."\nHost = $host\nPort = $port\nUsername = $username\nPassword = $password\nPara: $to\nResponder a:$replyTo\nDe: $from\nAsunto: $subject\nCuerpo: $body");
			return false;
		} else return true;
}

function enviarMailAlert($from, $to, $replyTo, $subject, $body){
	
	global $nombreGeneral;
	
	$enviado = false;
	
	$pos = strpos($from,$nombreGeneral);
	if($pos === false)
		$from = "$nombreGeneral <".$from.">";
		
		$headers = "From: $from\r\n";
		$headers.= "X-Mailer: php";
		if (mail($to, $subject, $body, $headers))
			$enviado = true;
			else
				$enviado = false;
				
				return $enviado;
				
}

function enviarMailSMTPhtml($from, $to, $cc, $replyTo, $subject, $body, $idpsiMail){
	global $mailHostExt, $mailPortExt, $mailPortSslExt, $mailUserExt, $mailPassExt;
	global $mailHost, $mailPort, $mailPortSsl, $mailUser, $mailPass;
	global $mailAlertasAdmin;
	
	global $codigoAbrev;
	global $nombreGeneral;
	
	$host = $mailHost;
	$port = $mailPort;
	$username = $mailUser;
	$password = $mailPass;
	
	$enviado = false;
	
	if ($host!=""){
		// si se ha configurado un host para SMTP, se envian por SMTP, si no, directamente desde el servidor
		
		//Si hay servidor externo, se utiliza. Si no, se utiliza el normal
		$enviarSSL = false;
		if ($mailHostExt!=""){
			$host = $mailHostExt;
			if ($mailPortSslExt!=""){
				$port = $mailPortSslExt;
				$enviarSSL = true;
			}else $port = $mailPortExt;
			$username = $mailUserExt;
			$password = $mailPassExt;
		}else{
			$host = $mailHost;
			if ($mailPortSsl!=""){
				$port = $mailPortSsl;
				$enviarSSL = true;
			}else $port = $mailPort;
			$username = $mailUser;
			$password = $mailPass;
		}
		
		if ($replyTo=="") $replyTo = $from;
		
		$mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
		
		$mail->IsSMTP();
		$mail->Host = $host;
		$mail->Port = $port;
		$mail->SMTPAuth = true;
		if ($enviarSSL)
			$mail->SMTPSecure = 'ssl';
			$mail->Username = $username;
			$mail->Password = $password;
			
			try {
				$mail->AddReplyTo($replyTo);
				$mail->AddAddress($to);
				if ($cc!="") $mail->AddCC($cc);
				$mail->SetFrom($from, $nombreGeneral);
				$mail->Subject = $subject;
				$mail->msgHTML($body);
				$mail->Send();
				$error = false;
			} catch (phpmailerException $e) {
				$errorTxt = $e->errorMessage()."\n".$mail->ErrorInfo;
				$error = true;
			} catch (Exception $e) {
				$errorTxt = $e->errorMessage()."\n".$mail->ErrorInfo;
				$error = true;
			}
			
			if ($error){
				enviarMailAlert($from, $mailAlertasAdmin, "", $codigoAbrev.": Error enviado mail HTML por SMTP", $errorTxt."\nHost = $host\nPort = $port\nUsername = $username\nPassword = $password\nPara: $to\nResponder a:$replyTo\nDe: $from\nAsunto: $subject\nCuerpo: $body");
				return false;
			} else return true;
	}
	return $enviado;
}

?>