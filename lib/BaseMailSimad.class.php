<?php
require 'lib/PHPMailer/src/Exception.php';
require 'lib/PHPMailer/src/PHPMailer.php';
require 'lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class BaseMailSimad
{
    public $nulog;
    public $nudebug;
    
	public $Host = "smtp.office365.comxx";
	public $SMTPAuth = true;
	public $enableAlert = true;
	public $replyToAlert = false;
	public $Port = 587;
	public $Username = "sgdea.alertas@unidadvictimas.gov.co";
	public $Password = "Colombia*2022";
	public $From = "sgdea.alertas@unidadvictimas.gov.co";
	public $NameFrom = ".::ArchiDhu SGDEA.::.Alertas";
    public $SMTPSecure  = "";
	public $log_dir = null;
	
	public $replyToMail = "radicacionbogota@unidadvictimas.gov.co";
	public $replyToName = "RADICACION BOGOTA";
	
    public $logfilename = "simad_alertas.log";
	public $mail_object;
	
	public $list_destinatarios = array();

	public function __construct()
	{
		$this->mail_object = new PHPMailer(false);
        $this->log_dir = sys_get_temp_dir();
        $this->logfilename = $this->log_dir."\\".$this->logfilename;
        $this->nulog = 1;
 	    $this->nudebug = 0;
		//*************************************************************************************
		$this->mail_object->CharSet = "UTF-8";//encoding
		$this->mail_object->IsSMTP();// telling the class to use SMTP
		$this->mail_object->SMTPAuth   = $this->SMTPAuth;// enable SMTP authentication
		$this->mail_object->SMTPDebug  = SMTP::DEBUG_OFF;//disable debug smtp
		$this->mail_object->Host       = $this->Host; // sets the SMTP server
		$this->mail_object->Port       = $this->Port; // set the SMTP port for the GMAIL server
		$this->mail_object->Username   = $this->Username; // SMTP account username
		$this->mail_object->Password   = $this->Password; // SMTP account password
		$this->mail_object->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$this->mail_object->Debugoutput = 'html';
		//*************************************************************************************
		$this->From = trim($this->From) == "" ? $this->Username : trim($this->From);
		$this->NameFrom = trim($this->NameFrom) == "" ? $this->Username : trim($this->NameFrom);
		$this->mail_object->SetFrom($this->From,$this->NameFrom);
	}
    
	public function SetLogDir($logDir = null)
	{
		$this->log_dir = $logDir;
		$this->logfilename = $this->log_dir."\\".$this->logfilename;
	}

	public function SetEnableService($isEnable = false)
	{
		$this->enableAlert = $isEnable;
	}
	
	public function SetEnableReplyTo($isEnable = false)
	{
		$this->replyToAlert = $isEnable;
		$this->SetAddReplyToEmail();
	}
	
	public function SetHost($hostname)
	{
		$this->Host = $hostname;
		$this->mail_object->Host = $this->Host;
	}

	public function SetSMTPAuth($SMTPAuth = true)
	{
		$this->SMTPAuth = $SMTPAuth;
		$this->mail_object->SMTPAuth   = $this->SMTPAuth;
	}

	public function SetSMTPSecure($SMTPSecure)
	{
		$this->SMTPSecure = $SMTPSecure;
		$this->mail_object->SMTPSecure = $this->SMTPSecure;
	}

	public function SetPuerto($port)
	{
		$this->Port = $port;
		$this->mail_object->Port = $this->Port;
	}

	public function SetEmailUser($username)
	{
		$this->Username = $username;
		$this->mail_object->Username = $this->Username; 
	}

	public function SetEmailPass($epassword)
	{
		$this->Password = $epassword;
		$this->mail_object->Password   = $this->Password;
	}

	public function SetFromText($text)
	{
		$this->NameFrom = $text;
	}

	public function SetFrom($mailfrom)
	{
		$this->From = $mailfrom;
	}
	
	public function SetNameFrom($name_from)
	{
		$this->NameFrom = $name_from;
	}
	
	public function SetReaplyToText($text)
	{
		$this->replyToName = $text;
	}

	public function SetReaplyToFrom($replyToAddress)
	{
		$this->replyToMail = $replyToAddress;
	}
	
	public function SetSubject($subjectmail)
	{
		$this->mail_object->Subject = $subjectmail;
	}
	
	public function SetMsgHTML($body)
	{
		$this->mail_object->MsgHTML($body);
	}
	
	public function SetAddAddress($address, $destinatario)
	{
		$this->mail_object->AddAddress($address, $destinatario);
		if(!empty($address)){
			$this->list_destinatarios[] = $address;
		}
	}
	
	public function SetAddCopyEmail($address_copy = array())
	{
		foreach($address_copy as $recipient){
			if(!empty($recipient)){
				$this->mail_object->AddCC($recipient,$recipient);
			}
		}
	}
	
	public function SetAddReplyToEmail()
	{
		if(!empty($this->replyToMail) && $this->replyToAlert == true){
			$this->mail_object->AddReplyTo($this->replyToMail,$this->replyToName);
		}
	}
	
	public function SetAddAttach($filepath = null)
	{
		if($filepath != null){
			$this->mail_object->AddAttachment($filepath,basename($filepath));
		}
	}
	
    public function SetNameFileLog($logname)
	{
		$this->logfilename = $logname;
	}
    
	public function setVerifySsl($isVerify = false)
	{
		if($isVerify){
			$this->mail_object->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
		}
	}

    public function writetolog($msg)
    {
        if($this->nulog)//habilitar variable nulog en 1 para guardar log
        {
            $f=fopen($this->logfilename,"a");
            if($f){fprintf($f,"%s => %s\n",date("Y-m-d H:i:s"),$msg . PHP_EOL);}
            fclose($f);
            if($this->nudebug){echo "<pre>".$msg."</pre>";}
        }
    }
        
	public function GetObjectMail()
	{
		return $this->mail_object;
	}
	
	public function InitSend()
	{
		try
		{
			if(count($this->list_destinatarios) <= 0){ 
				$message = "No hay ningun destinatario relacionado";
				$this->writetolog($message);
				return $message;
			}
			//*****************************************************************************
			if($this->enableAlert){
				$this->mail_object->SetFrom($this->From,$this->NameFrom);
				//*************************************************************************	
				if($this->mail_object->send()){
					return true;
				}else{
					return $this->mail_object->ErrorInfo;
				}
			}else{
				$message = "El servicio esta deshabilitado";
				$this->writetolog($message);
				return $message;
			}
		} catch (\PHPMailer\PHPMailer\Exception $e) {
            $error_msg = $e->errorMessage();
            $this->writetolog($error_msg);
			return $error_msg;
            
		} catch (Exception $e) {
			$error_msg = $e->errorMessage();
            $this->writetolog($error_msg);
			return $error_msg;
		}
	}
}
?>