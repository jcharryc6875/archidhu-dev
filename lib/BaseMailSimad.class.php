<?php
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/OAuthTokenProvider.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class BaseMailSimad
{
    public $nulog;
    public $nudebug;

	public $Host = "";
	public $SMTPAuth = true;
	public $enableAlert = true;
	public $replyToAlert = false;
	public $Port = 587;
	public $Username = "";
	public $Password = "";
	public $From = "";
	public $NameFrom = ".::ArchiDhu SGDEA.::.Alertas";
    public $SMTPSecure  = "";
	public $log_dir = null;

	public $replyToMail = "radicacionbogota@unidadvictimas.gov.co";
	public $replyToName = "RADICACION BOGOTA";

    public $logfilename = "simad_alertas.log";
	public $mail_object;

	public $list_destinatarios = array();

	// El llamador configuro host, usuario o clave propios: se respeta y no se usan las cuentas de NOTIFICACION_CUENTA.
	private $cuentaPropia = false;
	private $fromPropio = false;
	private $nameFromPropio = false;
	private $smtpErrors = array();

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
		$this->mail_object->Port       = $this->Port; // set the SMTP port for the GMAIL server
		$this->mail_object->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		// Sin salida a pantalla: solo se retienen las respuestas de error del servidor para la auditoria de envios.
		$this->mail_object->SMTPDebug  = SMTP::DEBUG_SERVER;
		$this->mail_object->Debugoutput = function ($str, $level) {
			$this->collectSmtpError($str);
		};
		// Host, credenciales y remitente los define la cuenta activa al enviar (InitSend).
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
		$this->cuentaPropia = true;
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
		$this->cuentaPropia = true;
		$this->Username = $username;
		$this->mail_object->Username = $this->Username;
	}

	public function SetEmailPass($epassword)
	{
		$this->cuentaPropia = true;
		$this->Password = $epassword;
		$this->mail_object->Password   = $this->Password;
	}

	public function SetFromText($text)
	{
		$this->nameFromPropio = true;
		$this->NameFrom = $text;
	}

	public function SetFrom($mailfrom)
	{
		$this->fromPropio = true;
		$this->From = $mailfrom;
	}

	public function SetNameFrom($name_from)
	{
		$this->nameFromPropio = true;
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
            if($f){
				fprintf($f,"%s => %s\n",date("Y-m-d H:i:s"),$msg . PHP_EOL);
				fclose($f);
			}
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
			if(count74($this->list_destinatarios) <= 0){
				$message = "No hay ningun destinatario relacionado";
				$this->writetolog($message);
				return $message;
			}
			//*****************************************************************************
			if(!$this->enableAlert){
				$message = "El servicio esta deshabilitado";
				$this->writetolog($message);
				return $message;
			}
			//*****************************************************************************
			if($this->cuentaPropia){
				return $this->sendWithOwnAccount();
			}
			return $this->sendWithConfiguredAccounts();
		} catch (\PHPMailer\PHPMailer\Exception $e) {
            $error_msg = $e->errorMessage();
            $this->writetolog($error_msg);
			return $error_msg;

		} catch (Throwable $e) {
			$error_msg = $e->getMessage();
            $this->writetolog($error_msg);
			return $error_msg;
		}
	}

	/**
	 * Envio con el SMTP que configuro el llamador (p. ej. cuenta por dependencia).
	 */
	private function sendWithOwnAccount()
	{
		$from = trim($this->From) == "" ? $this->Username : trim($this->From);
		$nameFrom = trim($this->NameFrom) == "" ? $from : trim($this->NameFrom);
		$this->mail_object->Sender = '';
		$this->mail_object->SetFrom($from, $nameFrom);
		$this->smtpErrors = array();
		//*****************************************************************************
		$ok = $this->mail_object->send();
		$error = $ok ? null : $this->describeError();
		NotificacionEnvioLogger::registrar(null, $this->list_destinatarios, $this->mail_object->Subject, $ok, $error);
		return $ok ? true : $error;
	}

	/**
	 * Envio con las cuentas activas de NOTIFICACION_CUENTA: principal primero y, si falla, respaldo.
	 */
	private function sendWithConfiguredAccounts()
	{
		$cuentas = NotificacionCuentaResolver::ordenarPorDisponibilidad(NotificacionCuentaResolver::getCuentasActivas());
		if(count74($cuentas) <= 0){
			$message = "No hay una cuenta de notificacion activa configurada";
			$this->writetolog($message);
			NotificacionEnvioLogger::registrar(null, $this->list_destinatarios, $this->mail_object->Subject, false, $message);
			return $message;
		}
		//*****************************************************************************
		$lastError = "";
		foreach($cuentas as $cuenta){
			$error = $this->sendWithAccount($cuenta);
			if($error === null){
				return true;
			}
			$lastError = $error;
			$this->writetolog("Fallo el envio con la cuenta " . $cuenta['nombre'] . ": " . $error);
		}
		return $lastError;
	}

	/**
	 * @return string|null null si se envio, de lo contrario el motivo del fallo
	 */
	private function sendWithAccount(array $cuenta)
	{
		$ok = false;
		$this->smtpErrors = array();
		try {
			$this->mail_object->smtpClose();
			$provider = NotificacionCuentaResolver::configurarMailer($this->mail_object, $cuenta);
			if($provider !== null){
				// PHPMailer descarta el error del token; se obtiene antes para reportar el motivo real.
				$provider->getAccessToken();
			}
			$from = $this->fromPropio && trim($this->From) != "" ? trim($this->From) : $cuenta['remitente'];
			$nameFrom = $this->nameFromPropio && trim($this->NameFrom) != ""
				? trim($this->NameFrom)
				: (trim((string) $cuenta['nombre_remitente']) != "" ? trim($cuenta['nombre_remitente']) : $from);
			// PHPMailer conserva el Sender del primer SetFrom: sin limpiarlo, el respaldo enviaria con el remitente de la cuenta que fallo.
			$this->mail_object->Sender = '';
			$this->mail_object->SetFrom($from, $nameFrom);
			//*************************************************************************
			$ok = $this->mail_object->send();
			$error = $ok ? null : $this->describeError();
		} catch (Throwable $e) {
			$error = $e->getMessage();
		}
		//*****************************************************************************
		NotificacionEnvioLogger::registrar($cuenta['id'], $this->list_destinatarios, $this->mail_object->Subject, $ok, $error);
		NotificacionCuentaResolver::registrarResultado($cuenta, $ok, $error);
		return $ok ? null : $error;
	}

	/**
	 * Error de PHPMailer mas la respuesta del servidor SMTP (p. ej. 535 5.7.3 Authentication unsuccessful).
	 */
	private function describeError()
	{
		$error = trim(str_replace('https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting', '', $this->mail_object->ErrorInfo));
		if(count74($this->smtpErrors) > 0){
			$extra = implode(' | ', array_slice($this->smtpErrors, -2));
			$error = $error === '' ? $extra : $error . ' [' . $extra . ']';
		}
		return $error;
	}

	/**
	 * PHPMailer no conserva en ErrorInfo la respuesta del servidor cuando falla la conexion o la
	 * autenticacion, por eso se captura aqui: respuestas 4xx/5xx y lineas "SMTP ERROR".
	 */
	private function collectSmtpError($str)
	{
		$line = trim((string) $str);
		if(strncmp($line, 'SERVER -> CLIENT:', 17) === 0){
			$reply = trim(substr($line, 17));
			if(preg_match('/^[45]\d\d/', $reply)){
				$this->smtpErrors[] = $reply;
			}
		}elseif(stripos($line, 'SMTP ERROR') === 0){
			$this->smtpErrors[] = $line;
		}
	}
}
?>
