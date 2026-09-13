<?php

/**
 * email_sync actions.
 *
 * @package    simad
 * @subpackage email_sync
 * @author     Javier Fernando Charry - javier.charry@aureasas.com
 * @version    SVN: $Id: actions.class.php 3335 2025-07-01 16:19:56Z fabien $
 */

class email_syncActions extends sfActions
{
    /**
     * email_syncActions::preExecute.
     * @return void si tiene el permiso retorna void y continua con la ejecucion
     * si no tiene el permiso se redirecciona a una pagina por defecto
    */
    public function preExecute(): void
    {
        $isAuthenticated = $this->getUser()->isAuthenticated();
        $base_path = sfConfig::get('base_simad');
        if (!$isAuthenticated) {
            $this->redirect($base_path . "/backend.php/security/login");
        }
    }

    /**
     * email_syncActions::verificaPrilegio.     
     * @param string $currentForm nombre del permiso que se deve validar     
     * @return void si tiene el permiso retorna void y continua con la ejecucion
     * si no tiene el permiso se redirecciona a una pagina por defecto
    */
    public function verificaPrilegio($currentForm): void
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        if (!$this->getUser()->checkPerm($currentForm, $usuariologuiado)) {
            $this->redirect(sfConfig::get('base_simad') . '/no_autorizado.html');
        }
    }

    /**
     * email_syncActions::tienePrivilegio.     
     * @param string $currentForm nombre del permiso que se deve validar     
     * @return bool true|false si tiene el permiso para ejecutar la accion actual
    */
    public function tienePrivilegio($currentForm): bool
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $isValid = true;
        if (!$this->getUser()->checkPerm($currentForm, $usuariologuiado)) {
            $isValid = false;
        }
        return $isValid;
    }

    /**
     * email_syncActions::verificaPrilegioCerrar.     
     * @param string $currentForm nombre del permiso que se deve validar     
     * @return void si tiene el permiso retorna void y continua con la ejecucion
     * si no tiene el permiso se redirecciona a una pagina por defecto
    */
    public function verificaPrilegioCerrar($currentForm): void
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        if (!$this->getUser()->checkPerm($currentForm, $usuariologuiado)) {
            $this->redirect(sfConfig::get('base_simad') . '/no_autorizado_cerrar.html');
        }
    }

    /**
     * email_syncActions::executeIndex.     
     * @return void retorna la vista asociada a este actions
    */
    public function executeIndex()
    {
        /*$email_message = EmailPeer::retrieveByPk(17);
        $com_recibida = ComRecibidaPeer::retrieveByPK(358382);
        $emailcom_pdf = $email_message->getCreateComPdfByEmailInfo($com_recibida);*/
        //**************************************************************************************************
        $this->forward('email_sync','list');
    }

    /**
     * email_syncActions::executeIndex.     
     * @return void retorna la vista asociada a este actions
    */
    public function executeTaskViewImage()
    {
        $token = trim($this->getRequestParameter('vtoken'));
        $base_web = sfConfig::get('base_simad');
        $response_process = array('status' => 400, 'message' => 'Error interno del servidor');
        //**************************************************************************************************
        try {
        if(!empty($token)){
            $object_info = EmailAttachmentPeer::retrieveByPk($this->getRequestParameter('key_id'));
            //**********************************************************************************************
            $total_char = strlen($token);
            $offset =  $total_char - 128;
            $parte01 = substr($token,0,64);
            $parte02 = substr($token,(64+$offset),$total_char);
            $token_url = $parte01.$parte02;
            $keytime = substr($token,64,$offset);
            $time = urldecode($keytime);
            $token_base = hash("sha512",$object_info->getPrimaryKey().$object_info->getFechaCreacion().$time);
            //**********************************************************************************************
            $time_actual = strtotime(date("Y-m-d G:i:s",time()));  
            $date_expiracion = strtotime ( "+30 minutes" , strtotime(date("Y-m-d G:i:s",$time)));		
            //**********************************************************************************************
            if($time_actual <= $date_expiracion){
            if($token_url == $token_base){
                $file_source = simad_util::NormalizePath($object_info->getFullPathAttach());
                $extension = pathinfo($file_source, PATHINFO_EXTENSION);
                //******************************************************************************************
                $filename = md5(time().uniqid()) .'.'. $extension;
                $tmpfile_path = sfConfig::get('sf_web_dir'). '/tmp/' . $filename;
                //******************************************************************************************
                if(file_exists($file_source)){ 
                if(!copy($file_source,$tmpfile_path)){
                    $response_process['message'] = 'Ocurrio un error con el archivo o este no existe en el servidor';
                }else{
                    $url_viewer = $base_web.'/tviews/'.$filename;
                    if(strtolower($extension) == 'pdf'){
                        $url_viewer = $base_web.'/viewerEx.php?fileview='.$filename;
                    }
                    //**************************************************************************************
                    $response_process = array('status' => 200, 'message' => 'Archivo generado y enviado para visualizacion', 'url_file' => $url_viewer);
                }
                }else{
                $response_process['message'] = 'Ocurrio un error con el archivo o este no existe en el servidor';
                }
            }
            }else{
            $response_process['message'] = 'El archivo no puede ser visualizado, el token del vinculo ha expirado, actualice la pagina e intente de nuevo';
            }
        }else{
            $response_process['message'] = 'No tine acceso a este recurso, actualice la pagina e intente de nuevo';
        }
        } catch (PropelException $th) {
        $response_process['message'] = $th->getMessage();
        } catch (\Exception $th) {
        $response_process['message'] = $th->getMessage();
        } catch (\Throwable $th) {
        $response_process['message'] = $th->getMessage();
        }
        //***************************************************************************************************
        $this->getResponse()->setContentType('application/json');      
        return $this->renderText(json_encode($response_process));
    }

    /**
     * email_syncActions::executeShow.     
     * @return void retorna la vista asociada a este actions
    */
    public function executeShow()
    {
        $this->verificaPrilegioCerrar('email_sync/show');
        //***********************************************************************************************
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $email_id = trim($this->getRequestParameter('email_id')) ? trim($this->getRequestParameter('email_id')) : null;
        //***********************************************************************************************
        $this->email_message = EmailPeer::retrieveByPk($email_id);
    }

    /**
     * email_syncActions::executeAsignarEmail.     
     * @return void retorna la vista asociada a este actions
    */
    public function executeAsignarEmail()
    {
        if(!$this->tienePrivilegio('EMAIL_ASIGNAR')){
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, esta funcionalidad esta restringida';
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $email_id = trim($this->getRequestParameter('email_id')) ? SED::decryption(trim($this->getRequestParameter('email_id'))) : null;
        $email_message = EmailPeer::retrieveByPk($email_id);
        //***********************************************************************************************
        if($email_message == null){
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, los parametros enviados no son validos';
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        if($email_message->getEstaAsignado()){
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, no se puede asignar el registro, otro usuario lo tiene asignado';
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        try {
            $email_message->setEstaAsignado(1);
            $email_message->setUsuarioId($usuariologuiado);
            $email_message->setFechaModificacion(date("Y-m-d G:i:s"));
            $email_message->save();
            //*******************************************************************************************
            $message_text = "Registro de email asignado";
            EmailBitacoraPeer::addBitacoraByEmailId($email_message->getPrimaryKey(), $usuariologuiado, null, null, $message_text, "EMAIL_ASIGNADO");
            //*******************************************************************************************
            $html = '<td>'.htmlspecialchars($email_message->getEmailOrigen()).'</td>';
            $html .= '<td>'.$email_message->getAsunto().'</td>';
            $html .= '<td class="text-center">'.$email_message->getFechaRecibido().'</td>';
            $html .= '<td class="text-center">'.$email_message->getCountAttachment().'</td>';
            $html .= '<td class="text-center">'.$email_message->getEmailEstado().'</td>';
            //$html .= '<td class="text-center">'.$email_message->getFechaCreacion().'</td>';
            $html .= '<td class="text-center">';
            $html .= $this->getPartial('optionsEmail',array('email_message' => $email_message,'asignar_email' => false));
            $html .= '</td>';
            //*******************************************************************************************
            $response_info['error'] = false;
            $response_info['code'] = 200;
            $response_info['message'] = 'El registro se asignó al usuario exitosamente';
            $response_info['resp_html'] = $html;
            $response_info['element_update'] = md5($email_message->getPrimaryKey().$email_message->getMessageId());
        } catch (PropelException $th) {
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, error de acceso a los datos';
        } catch (\Exception $th) {
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, error interno en la aplicación(Exception)';
        } catch (\Throwable $th) {
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, error interno del servidor(Throwable)';
        }
        //***********************************************************************************************
        $this->getResponse()->setContentType('application/json');
        return $this->renderText(json_encode($response_info));
    }

    /**
     * email_syncActions::executeReleaseEmail.     
     * @return void retorna la vista asociada a este actions
    */
    public function executeReleaseEmail()
    {
        if(!$this->tienePrivilegio('EMAIL_ASIGNAR')){
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, esta funcionalidad esta restringida';
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $email_id = trim($this->getRequestParameter('email_id')) ? SED::decryption(trim($this->getRequestParameter('email_id'))) : null;
        $email_message = EmailPeer::retrieveByPk($email_id);
        //***********************************************************************************************
        if($email_message == null){
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, los parametros enviados no son validos';
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        try {
            if($email_message->getEstaAsignado()){
                $email_message->setEstaAsignado(0);
                $email_message->setUsuarioId($usuariologuiado);
                $email_message->setFechaModificacion(date("Y-m-d G:i:s"));
                $email_message->save();
                //*******************************************************************************************
                $message_text = "Registro de email liberado";
                EmailBitacoraPeer::addBitacoraByEmailId($email_message->getPrimaryKey(), $usuariologuiado, null, null, $message_text, "EMAIL_RELEASED");
            }
            //*******************************************************************************************
            $html = '<td>'.htmlspecialchars($email_message->getEmailOrigen()).'</td>';
            $html .= '<td>'.$email_message->getAsunto().'</td>';
            $html .= '<td class="text-center">'.$email_message->getFechaRecibido().'</td>';
            $html .= '<td class="text-center">'.$email_message->getCountAttachment().'</td>';
            $html .= '<td class="text-center">'.$email_message->getEmailEstado().'</td>';
            $html .= '<td class="text-center">';
            $html .= $this->getPartial('optionsEmail',array('email_message' => $email_message,'asignar_email' => true));
            $html .= '</td>';
            //*******************************************************************************************
            $response_info['error'] = false;
            $response_info['code'] = 200;
            $response_info['message'] = 'Registro de email liberado satisfactoriamente';
            $response_info['resp_html'] = $html;
            $response_info['element_update'] = md5($email_message->getPrimaryKey().$email_message->getMessageId());
        } catch (PropelException $th) {
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, error de acceso a los datos';
        } catch (\Exception $th) {
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, error interno en la aplicación(Exception)';
        } catch (\Throwable $th) {
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, error interno del servidor(Throwable)';
        }
        //***********************************************************************************************
        $this->getResponse()->setContentType('application/json');
        return $this->renderText(json_encode($response_info));
    }

    /**
     * email_syncActions::executeAttachLinkShared.     
     * @return void retorna la vista asociada a este actions
    */
    public function executeAttachLinkShared()
    {
        $this->verificaPrilegioCerrar('EMAIL_ADJUNTO_LINK_COMPARTIDO');
        //***********************************************************************************************
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $email_id = trim($this->getRequestParameter('email_id')) ? SED::decryption(trim($this->getRequestParameter('email_id'))) : null;
        //***********************************************************************************************
        $this->email_message = EmailPeer::retrieveByPk($email_id);
        //***********************************************************************************************
        $this->setTemplate('addAttachShared');
    }

    /**
    * email_syncActions::executeSharedUpload.     
    * @return void retorna la vista asociada a este actions
    */
    public function executeSharedUpload()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $email_id = trim($this->getRequestParameter('email_id')) ? SED::decryption(trim($this->getRequestParameter('email_id'))) : null;
        $shared_link = trim($this->getRequestParameter('shared_link')) ? trim($this->getRequestParameter('shared_link')) : null;
        //***********************************************************************************************
        $response_info['error'] = true;
        $response_info['code'] = 400;
        $isError = false;
        //***********************************************************************************************
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response_info['message'] = 'Ocurrio un error, esta funcionalidad esta restringida';
            $isError = true;
        }
        //***********************************************************************************************
        $fdownloader = new DescargadorArchivos();
        $urlCodificada = $fdownloader->codificarUrlConEspacios($shared_link);
        //***********************************************************************************************
        // Validar URL
        if (!filter_var($urlCodificada, FILTER_VALIDATE_URL)) {
            $response_info['message'] = 'Ocurrio un error, la url enviada no es valida';
            $isError = true;
        }
        //***********************************************************************************************
        if(!$this->tienePrivilegio('EMAIL_ADJUNTO_LINK_COMPARTIDO')){
            $response_info['message'] = 'Ocurrio un error, esta funcionalidad esta restringida';
            $isError = true;
        }
        //***********************************************************************************************
        // Descargar el archivo
        $fileData = $fdownloader->getFileBasicData($shared_link);
        if ($fileData['success'] === true) {
            $resp_fdownloader = $fileData;
        } else if($fileData['success'] == false){
            $resp_fdownloader = $fdownloader->descargarArchivo($shared_link,$urlCodificada);
            //*******************************************************************************************
            if($resp_fdownloader['success'] == false){
                $response_info['error'] = true;
                $response_info['code'] = 400;
                $response_info['message'] = $resp_fdownloader['error'];
                $this->getResponse()->setContentType('application/json');
                return $this->renderText(json_encode($response_info));
            }
        }else{
            $response_info['message'] = 'Ocurrio un error, el archivo no se pudo descargar, el archivo no existe o se necesita autenticación para la descarga';
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        $email_message = EmailPeer::retrieveByPk($email_id);
        $resp_fdownloader['email_id'] = $email_message->getPrimaryKey();
        EmailAttachmentPeer::addNewAttachment($resp_fdownloader,$email_message->getEmailaccountId());
        //***********************************************************************************************
        $html_resp = $this->getPartial('viewAttachment',array('attach_email' => $email_message->getEmailAttachments(),'email_message'=>$email_message));
        //***********************************************************************************************
        $response_info['error'] = false;
        $response_info['code'] = 200;
        $response_info['message'] = 'El archivo se descargo y se adjunto al registro actual';
        $response_info['resp_html'] = $html_resp;
        $response_info['element_update'] = 'stcurrentattach';
        //***********************************************************************************************
        $this->getResponse()->setContentType('application/json');
        return $this->renderText(json_encode($response_info));
    }

    /**
     * email_syncActions::executeAnular.     
     * @return void retorna la vista asociada a este actions
    */
    public function executeAnular()
    {
        $this->verificaPrilegioCerrar('email_sync/anular');
        //***********************************************************************************************
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $email_id = trim($this->getRequestParameter('email_id')) ? SED::decryption(trim($this->getRequestParameter('email_id'))) : null;
        //***********************************************************************************************
        $this->email_message = EmailPeer::retrieveByPk($email_id);
    }

    /**
     * email_syncActions::executeUpdateAnular.     
     * @return void retorna la vista asociada a este actions
    */
    public function executeUpdateAnular()
    {
        if(!$this->tienePrivilegio('email_sync/anular')){
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, esta funcionalidad esta restringida';
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $email_id = trim($this->getRequestParameter('email_id')) ? trim($this->getRequestParameter('email_id')) : null;
        $fecha_anulacion = trim($this->getRequestParameter('fecha_anulacion')) ? trim($this->getRequestParameter('fecha_anulacion')) : null;
        $nota_anulacion = trim($this->getRequestParameter('nota_anulacion')) ? trim($this->getRequestParameter('nota_anulacion')) : null;
        $email_message = EmailPeer::retrieveByPk($email_id);
        //***********************************************************************************************
        if($email_message->getEmailestadoId() != EmailStatus::Descargado){
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, este registro no se puede anular';
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        if($email_message == null || empty($fecha_anulacion) || empty($nota_anulacion)){
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, los parametros enviados no son validos';
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        try {
            $email_message->setEmailestadoId(EmailStatus::Anulado);
            $email_message->setNotaAnulacion($nota_anulacion);
            $email_message->setFechaModificacion(date("Y-m-d G:i:s"));
            $email_message->save();
            //*******************************************************************************************
            EmailBitacoraPeer::addBitacoraByEmailId($email_message->getPrimaryKey(), $usuariologuiado, null, null, $nota_anulacion, "EMAIL_ANULADO");
            //*******************************************************************************************
            $html = '<td>'.htmlspecialchars($email_message->getEmailOrigen()).'</td>';
            $html .= '<td>'.$email_message->getAsunto().'</td>';
            $html .= '<td class="text-center">'.$email_message->getFechaRecibido().'</td>';
            $html .= '<td class="text-center">'.$email_message->getCountAttachment().'</td>';
            $html .= '<td class="text-center">'.$email_message->getEmailEstado().'</td>';
            $html .= '<td class="text-center">'.$email_message->getFechaCreacion().'</td>';
            $html .= '<td class="text-center">';
            $html .= $this->getPartial('optionsEmail',array('email_message' => $email_message,'asignar_email' => false));
            $html .= '</td>';
            //*******************************************************************************************
            $response_info['error'] = false;
            $response_info['code'] = 200;
            $response_info['message'] = 'Ocurrio un error, los parametros enviados no son validos';
            $response_info['resp_html'] = $html;
            $response_info['element_update'] = md5($email_message->getPrimaryKey().$email_message->getMessageId());
        } catch (PropelException $th) {
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, error de acceso a los datos';
        } catch (\Exception $th) {
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, error interno en la aplicación(Exception)';
        } catch (\Throwable $th) {
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = 'Ocurrio un error, error interno del servidor(Throwable)';
        }
        //***********************************************************************************************
        $this->getResponse()->setContentType('application/json');
        return $this->renderText(json_encode($response_info));
    }

    /**
    * email_syncActions::executeTaskAsync
    * @return void retorna la vista asociada a este actions
    */
    public function executeTaskAsync()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $emailaccount_id = trim($this->getRequestParameter('emailaccount_id')) ? trim($this->getRequestParameter('emailaccount_id')) : null;
        $account_login = trim($this->getRequestParameter('account_login')) ? trim($this->getRequestParameter('account_login')) : null;
        //***********************************************************************************************
        if(empty($emailaccount_id) || empty($account_login)){
            $response_info['error'] = true;
            $response_info['code'] = 500;
            $response_info['message'] = "Ocurrio un error, los parametros enviados no son validos";
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        $isAccountAsig = EmailAccountUsuarioPeer::getUserAccessEmailAccount($account_login,$usuariologuiado);
        if(!$isAccountAsig){
            $response_info['error'] = true;
            $response_info['code'] = 404;
            $response_info['message'] = "Ocurrio un error, no se puede acceser a la cuenta seleccionada";
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        //$isAsync = EmailPeer::getDataEmailAcount($emailaccount_id,$usuariologuiado);
        $isAsync = EmailPeer::getDataEmailAcountWebklex($emailaccount_id,$usuariologuiado);
        //***********************************************************************************************
        if(empty($isAsync)){
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = "Ocurrio un error, los correos no se pudieron descargar";
        }
        //***********************************************************************************************
        $response_info['error'] = false;
        $response_info['code'] = 200;
        //***********************************************************************************************
        $this->getResponse()->setContentType('application/json');
        return $this->renderText(json_encode($response_info)); 
    }

    /**
    * email_syncActions::executeConsulta
    * @return void retorna la vista asociada a este actions
    */
    public function executeConsulta()
    {
        
    }

    /**
    * email_syncActions::executeAuthAccount
    * @return void retorna la vista asociada a este actions
    */
    public function executeAuthAccount()
    {
        $this->verificaPrilegioCerrar('EMAIL_ACCOUNT_ACTIVATE');
        //***********************************************************************************************
        $this->csrf_token = md5(session_id() . time());
        $this->getUser()->setAttribute('csrf_token', $this->csrf_token);
        //***********************************************************************************************
        $emailaccount_id = trim($this->getRequestParameter('emailaccount_id')) ? trim($this->getRequestParameter('emailaccount_id')) : null;
        $this->email_account = EmailAccountPeer::retrieveByPK($emailaccount_id);
    }

    /**
    * email_syncActions::executeshowComRec
    * @return void retorna la vista asociada a este actions
    */
    public function executeShowComRec()
    {
        $comrecibida_id = trim($this->getRequestParameter('comrecibida_id')) ? trim($this->getRequestParameter('comrecibida_id')) : null;
        $url_redirect = $this->getRequest()->getScriptName() . '/com_recibida/show?comrecibida_id=' . $comrecibida_id;
        $this->redirect($url_redirect);
    }

    /**
     * email_syncActions::executeCreateComRec
     * @return void retorna la vista asociada a este actions
    */
    public function executeCreateComRec()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $email_id = trim($this->getRequestParameter('email_id')) ? trim($this->getRequestParameter('email_id')) : null;
        //***********************************************************************************************
        $this->verificaPrilegioCerrar('COM_RECIBIDA_RADICAR_EMAIL');
        //***********************************************************************************************
        if(empty($email_id)){
            $this->redirect(sfConfig::get('base_simad') . '/no_autorizado_cerrar.html');
        }
        //***********************************************************************************************
        $this->csrf_token = md5(session_id() . time());
        $this->getUser()->setAttribute('csrf_token', $this->csrf_token);
        //***********************************************************************************************
        $email_message = EmailPeer::retrieveByPk($email_id);
        $com_recibida = new ComRecibida();
        //***********************************************************************************************
        $com_recibida->setAsunto($email_message->getAsunto());
        $com_recibida->setFechaRecibido($email_message->getFechaRecibido('Y-m-d'));
        $com_recibida->setFolios($email_message->getCountAttachment());
        //***********************************************************************************************
        $observaciones = sprintf("Radicacion Correo Electronico MessageID %s",$email_message->getMessageId());
        $com_recibida->setObservaciones($observaciones);
        //***********************************************************************************************
        $email_origen = null;
        if (preg_match('/<([^>]+)>/', $email_message->getEmailOrigen(), $coincidencias)) {
            $email_origen = $coincidencias[1];
        }
        //***********************************************************************************************
        $interesado_text = "";
        $idUserInteresados = array();
        $interesado_email = InteresadosPeer::getInteresadoByEmail($email_origen);
        if($interesado_email != null){
            $interesado_text = $interesado_email->getNombreCustomEmail();
            $idUserInteresados[] = $interesado_email->getPrimaryKey();
        }
        //***********************************************************************************************
        $remitente_text = "";
        $idRemitenteUser = array();
        $remitente_email = DirectorioExternoPeer::getDirectorioExternoByEmail($email_origen);
        if($remitente_email != null){
            $remitente_text = $remitente_email->getNombreAndNuid();
            $idRemitenteUser[] = $remitente_email->getPrimaryKey();
        }
        //***********************************************************************************************
        if(!empty($interesado_email) && !empty($remitente_email)){
            $this->getUser()->setFlash('messages_warning', 'Por favor verifique tanto el interesado como el remitente, se encontraron ambos registros');
        }
        //***********************************************************************************************
        //trigger regionales destino
        $app_cfg = simad_util::readConfigFileApp(array('distribuidor_regional_area_pk'));
        $this->valorActivador = $app_cfg['distribuidor_regional_area_pk'];
        //***********************************************************************************************
        $this->email_message = $email_message;
        $this->com_recibida = $com_recibida;
        $this->textUserInteresados = $interesado_text;
        $this->idUserInteresados = $idUserInteresados;
        $this->idRemitenteUser = $idRemitenteUser;
        $this->textRemitenteUser = $remitente_text;
    }

     /**
     * email_syncActions::executeUpdateActivateAccount
     * @return void retorna la vista asociada a este actions
    */
    public function executeUpdateActivateAccount()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $emailaccount_id = trim($this->getRequestParameter('emailaccount_id')) ? SED::decryption(trim($this->getRequestParameter('emailaccount_id'))) : null;
        $account_password = trim($this->getRequestParameter('account_password')) ? trim($this->getRequestParameter('account_password')) : null;
        $observaciones = trim($this->getRequestParameter('observaciones')) ? trim($this->getRequestParameter('observaciones')) : null;
        //***********************************************************************************************
        if(!$this->tienePrivilegio('EMAIL_ACCOUNT_ACTIVATE')){
            $this->getUser()->setFlash('messages_error', 'Ocurrio un error, esta funcionalidad esta restringida por privilegios de acceso');
            $this->redirect($this->getRequest()->getScriptName() . '/email_sync/authAccount?emailaccount_id=' . $emailaccount_id);
        }
        //***********************************************************************************************
        if ($this->getRequest()->getMethod() != sfRequest::POST){
            $this->getUser()->setFlash('messages_error', 'Ocurrio un error, el metodo de envio de los parametros no esta permitido');
            $this->redirect($this->getRequest()->getScriptName() . '/email_sync/authAccount?emailaccount_id=' . $emailaccount_id);
        }
        //***********************************************************************************************
        $csrf_token = trim($this->getRequestParameter('_csrf_token')) ? trim($this->getRequestParameter('_csrf_token')) : null;
        if (!$this->getUser()->checkCsrfToken($csrf_token)){
            $this->getUser()->setFlash('messages_error', 'Ocurrio un error, el token del formualario no es valido');
            $this->redirect($this->getRequest()->getScriptName() . '/email_sync/authAccount?emailaccount_id=' . $emailaccount_id);
        }
        //***********************************************************************************************
        if (empty($emailaccount_id) && empty($account_password) && empty($observaciones)){
            $this->getUser()->setFlash('messages_error', 'Ocurrio un error, los datos enviados no son validos');
            $this->redirect($this->getRequest()->getScriptName() . '/email_sync/authAccount?emailaccount_id=' . $emailaccount_id);
        }
        //***********************************************************************************************
        try {
            $email_account = EmailAccountPeer::retrieveByPK($emailaccount_id);
            $email_provider = $email_account->getEmailProviderConfig()->getEmailProvider();
            //*******************************************************************************************
            if(EmailPeer::checkAutheticateActive($emailaccount_id)){
                EmailAccountUsuarioPeer::addAutorizationAccountSync($emailaccount_id,$usuariologuiado);
                //*******************************************************************************************
                if($email_provider->getEmailtipoauthId() == EmailTypeAuth::Password){
                    $email_account->setAccountPass(SED::encryption($account_password));
                }
                //$email_account->setObservaciones($observaciones);
                $email_account->setFechaModificacion(date("Y-m-d G:i:s"));
                $email_account->setEsActual(1);
                $email_account->save();
                //*******************************************************************************************
                $this->getUser()->setFlash('messages_success', 'La sincronización de correos se activo exitosamente');
            }else{
                $this->getUser()->setFlash('messages_error', 'Error de autenticación, no se puede conectar con el servidor de correos');
                $email_account->setAccountPass(null);
                $email_account->setFechaModificacion(date("Y-m-d G:i:s"));
                $email_account->setEsActual(0);
                $email_account->save();
            }
        } catch (PropelException $th) {
            $message_status = 'Ocurrio un error almacenando los datos y no se radico el correo electrónico, comuniquese con el administrador';
            $this->getUser()->setFlash('messages_error', $message_status);
        } catch (\Exception $th) {
            $message_status = 'Ocurrio un error interno en la aplicación y no se radico el correo electrónico, comuniquese con el administrador';
            $this->getUser()->setFlash('messages_error', $message_status);
        } catch (\Throwable $th) {
            $message_status = 'Ocurrio un error interno en el servidor y no se radico el correo electrónico, comuniquese con el administrador';
            $this->getUser()->setFlash('messages_error', $message_status);
        }
        //****************************************************************************************************
        return $this->redirect($this->getRequest()->getScriptName() . '/email_sync/authAccount?emailaccount_id=' . $emailaccount_id);
    }

    /**
     * email_syncActions::executeUpdateComRec
     * @return void retorna la vista asociada a este actions
    */
    public function executeUpdateComRec()
    {
        if ($this->getRequest()->getMethod() != sfRequest::POST)
		{
            $message_error = "Ocurrio un error, el metodo de envio de los parametros no esta permitido";
            $isError = true;{}
        }
        //***********************************************************************************************
        $csrf_token = trim($this->getRequestParameter('_csrf_token')) ? trim($this->getRequestParameter('_csrf_token')) : null;
        if (!$this->getUser()->checkCsrfToken($csrf_token)){
            $message_error = "Ocurrio un error, el token del formualario no es valido";
            $isError = true;
        }
        //***********************************************************************************************
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $email_id = trim($this->getRequestParameter('email_id')) ? trim($this->getRequestParameter('email_id')) : null;
        $email_message = EmailPeer::retrieveByPk($email_id);
        //***********************************************************************************************
        if(!$this->tienePrivilegio('COM_RECIBIDA_RADICAR_EMAIL') || !($email_message->getUsuarioId() == $usuariologuiado && $email_message->getEstaAsignado())){
            $message_error = "Ocurrio un error, esta funcionalidad esta restringida por privilegios de acceso";
            $isError = true;
        }
        //***********************************************************************************************
        if($email_message->getEmailestadoId() != EmailStatus::Descargado){
            $message_error = "Ocurrio un error, este correo ya fue procesado";
            $isError = true;
        }
        //***********************************************************************************************
        if(empty($email_id)){
            $message_error = "Ocurrio un error, los parametros no son validos";
            $isError = true;
        }
        //***********************************************************************************************
        if($isError){
            $response_info['error'] = true;
            $response_info['code'] = 400;
            $response_info['message'] = $message_error;
            $this->getResponse()->setContentType('application/json');
            return $this->renderText(json_encode($response_info));
        }
        //***********************************************************************************************
        try {
            $udestino_id = trim($this->getRequestParameter('idUser')) ? trim($this->getRequestParameter('idUser')) : null;
            //***********************************************************************************************
            $current_usuario = UsuarioPeer::retrieveByPk($usuariologuiado);
            $ucargo_radica = CargoUsuarioPeer::getCargoUsuarioByIdUser($current_usuario->getPrimaryKey());
            $formarecepcion_id = FormaRecepcionPeer::getFormaRecepcionByText("Correo Electronico");
            //***********************************************************************************************
            if(empty($formarecepcion_id)){
                $response_info['error'] = true;
                $response_info['code'] = 400;
                $response_info['message'] = "La forma de recepcion (Correo Electronico) no esta configurada";
                $this->getResponse()->setContentType('application/json');
                return $this->renderText(json_encode($response_info));
            }
            //***********************************************************************************************
            $observaciones = trim($this->getRequestParameter('observaciones')) ? trim($this->getRequestParameter('observaciones')) : null;
            $regional_destino_id = trim($this->getRequestParameter('regional_destino_id')) ? trim($this->getRequestParameter('regional_destino_id')) : null;
            if(!empty($regional_destino_id))
            {
                $regional_descripcion = RegionalPeer::retrieveByPK($regional_destino_id)->getDescripcion();
                $observaciones = $observaciones . " | Redireccionado para distribuidor: " . $regional_descripcion;
                $com_values['regional_destino_id'] = $regional_destino_id;
            }
            //***********************************************************************************************
            $com_values['regional_id'] = $current_usuario->getRegionalId();
            $com_values['tipocomrecibida_id'] = trim($this->getRequestParameter('tipo_com_recibida_id')) ? trim($this->getRequestParameter('tipo_com_recibida_id')) : null;
            $com_values['dependencia_id'] = trim($this->getRequestParameter('dependencia_id')) ? trim($this->getRequestParameter('dependencia_id')) : null;
            $com_values['estadocomrecibida_id'] = 11;
            $com_values['formarecepcion_id'] = $formarecepcion_id;
            $com_values['ciudad_id'] = $current_usuario->getRegional()->getCiudadId();
            $com_values['directorioexterno_id'] = trim($this->getRequestParameter('directorioexterno')) ? trim($this->getRequestParameter('directorioexterno')) : null;
            $com_values['radicado_origen'] = trim($this->getRequestParameter('radicado_origen')) ? trim($this->getRequestParameter('radicado_origen')) : null;
            $com_values['asunto'] = trim($this->getRequestParameter('asunto')) ? trim($this->getRequestParameter('asunto')) : null;
            $com_values['prioridadcom_id'] = trim($this->getRequestParameter('prioridadcom_id')) ? trim($this->getRequestParameter('prioridadcom_id')) : null;
            $com_values['numero_fud'] = trim($this->getRequestParameter('numero_fud')) ? trim($this->getRequestParameter('numero_fud')) : null;
            $com_values['numero_proceso'] = trim($this->getRequestParameter('numero_proceso')) ? trim($this->getRequestParameter('numero_proceso')) : null;
            $com_values['empresamensajeria_id'] = trim($this->getRequestParameter('empresamensajeria_id')) ? trim($this->getRequestParameter('empresamensajeria_id')) : null;
            $com_values['numero_guia'] = trim($this->getRequestParameter('guia')) ? trim($this->getRequestParameter('guia')) : null;
            $com_values['fecha_recibido'] = trim($this->getRequestParameter('fecha_recibido')) ? trim($this->getRequestParameter('fecha_recibido')) : null;
            $com_values['folios'] = trim($this->getRequestParameter('folios')) ? trim($this->getRequestParameter('folios')) : null;
            $com_values['observaciones'] = $observaciones;
            $com_values['anexos'] = trim($this->getRequestParameter('anexos')) ? trim($this->getRequestParameter('anexos')) : null;
            $com_values['fecha_vencimiento'] = trim($this->getRequestParameter('fecha_maxima_respuesta')) ? trim($this->getRequestParameter('fecha_maxima_respuesta')) : null;
            //***********************************************************************************************
            $com_values['usuario_radicador'] =  $current_usuario->getPrimaryKey();
            $com_values['cusuario_radicador'] =  $ucargo_radica;
            //***********************************************************************************************
            if(!empty($udestino_id)){
                $com_values['usuario_destino'] = $udestino_id;
                $com_values['cusuario_destino'] = CargoUsuarioPeer::getCargoUsuarioByIdUser($udestino_id);
            }
            //***********************************************************************************************
            $com_recibida = ComRecibidaPeer::addComRecibida($com_values,true);
            //***********************************************************************************************
            if(empty($com_recibida)){
                $response_info['error'] = true;
                $response_info['code'] = 400;
                $response_info['message'] = "Ocurrio un error radicando la comunicación, intente de nuevo, si persiste el error comuniquese con el administrador del sistema";
                $this->getResponse()->setContentType('application/json');
                return $this->renderText(json_encode($response_info));
            }
            //***********************************************************************************************
            $radicarByInt = trim($this->getRequestParameter(md5('radByIntzx1'))) ? $this->getRequestParameter(md5('radByIntzx1')) : 0;
            $isAddInteresado = false;
            $list_interesado = preg_split("/[,]+/", trim($this->getRequestParameter('idUserInteresados')), -1, PREG_SPLIT_NO_EMPTY);
            $radicado_com = "";
            $mailErrorMsg = array();
            for ($index = 0; $index < count($list_interesado); $index++) {
                $interesado_obj = InteresadosPeer::retrieveByPK($list_interesado[$index]);
                if (!$radicarByInt) {
                    $isAddInteresado = ComrecibidaInteresadosPeer::addNewInteresadoByComId($com_recibida->getPrimaryKey(), $list_interesado[$index]);
                    $radicado_com = $com_recibida->getRadicado();
                } elseif ($index == 0) {
                    $isAddInteresado = ComrecibidaInteresadosPeer::addNewInteresadoByComId($com_recibida->getPrimaryKey(), $list_interesado[$index]);
                    $radicado_com = $com_recibida->getRadicado();
                } else {
                    $com_recibida_new = $com_recibida->copy();
                    $com_recibida_new->save();
                    //***************************************************************************************
                    $radicado_compose = $com_recibida_new->getRadicadoFormat($com_recibida_new->getRegionalId(), $com_recibida_new->getDependenciaId(), 0, false);
                    $com_recibida_new->setRadicado($radicado_compose);
                    $com_recibida_new->setCodigoReenResp($com_recibida_new->getPrimaryKey());
                    $com_recibida_new->save();
                    //***************************************************************************************
                    $radicado_com = $radicado_compose;
                    //***************************************************************************************
                    foreach ($com_recibida->getComrecibidaUsuarios() as $current_ucom) {
                        $newucomrecibida = $current_ucom->copy();
                        $newucomrecibida->setComrecibidaId($com_recibida_new->getPrimaryKey());
                        $newucomrecibida->setFechaAsigna(date("Y-m-d G:i:s"));
                        $newucomrecibida->save();
                    }
                    //***************************************************************************************
                    $isAddInteresado = ComrecibidaInteresadosPeer::addNewInteresadoByComId($com_recibida_new->getPrimaryKey(), $list_interesado[$index]);
                }
                //*******************************************************************************************
                if ($interesado_obj != null) {
                    $response_mail = $interesado_obj->envioEmailNotificacion($radicado_com);
                    if ($response_mail['IsSend'] == false) {
                        $mailErrorMsg[] = $response_mail['message'];
                    }
                }
            }
            //***********************************************************************************************
            if($com_recibida != null){
                $emailcom_pdf = $email_message->getCreateComPdfByEmailInfo($com_recibida);
                if(file_exists($emailcom_pdf)){
                    $digit_config = ComRecibidaPeer::initFolderDigit($com_recibida->getRegionalId(),$com_recibida->getPeriodoId());
                    $filename = sprintf("%s.pdf",$com_recibida->getRadicado());
                    $path_digit = $digit_config['full_path'].DIRECTORY_SEPARATOR.$filename;
                    if(@rename($emailcom_pdf,$path_digit)){
                        $com_recibida->setNewStateByDigitCom();
                    }
                }
            }
            //***********************************************************************************************
            $email_message->sendEmailAttachToCom($com_recibida,$usuariologuiado);
            //***********************************************************************************************        
            AuditLogPeer::guardarAuditoriaLite(ComRecibidaPeer::getOMClass(),new ComRecibida(),$com_recibida,ModulesEnable::ComRecibida,$com_recibida->getRadicado());
            //***********************************************************************************************
            if($com_recibida != null)
            {
                $estadoemail_id = EmailStatus::Radicado;
                $email_message->setRadicadoSgdea($com_recibida->getRadicado());
                $email_message->setEmailestadoId($estadoemail_id);
                $email_message->setFechaModificacion(date("Y-m-d G:i:s"));
                $email_message->setConsecutivoId($com_recibida->getPrimaryKey());
                $email_message->setModuloId(ModulesEnable::ComRecibida);
                $email_message->save();
                //*******************************************************************************************
                $message_success = 'El correo fue radicado exitosamente en las rxternas recibidas radicado '.$com_recibida->getRadicado();
                EmailBitacoraPeer::addBitacoraByEmailId($email_message->getPrimaryKey(), $usuariologuiado, $com_recibida->getPrimaryKey(), 
                    $com_recibida->getRadicado(), $message_success, "EMAIL_RADICADO");
                //*******************************************************************************************
                $message_status = 'La comunicación se radico satisfactoriamente';
                $url_redirect = $this->getRequest()->getScriptName() . '/email_sync/showComRec?comrecibida_id=' . $com_recibida->getPrimaryKey();
            }else{
                if($com_recibida != null){ ComRecibidaPeer::deleteCascada($com_recibida->getPrimaryKey()); }
                $message_status = 'Ocurrio un error en el servidor y no se radico el correo electrónico';
                EmailBitacoraPeer::addBitacoraByEmailId($email_message->getPrimaryKey(), $usuariologuiado, null, null, $message_status, "EMAIL_ERROR_RADICANDO");
            }
        } catch (PropelException $th) {
            $message_status = 'Ocurrio un error almacenando los datos y no se radico el correo electrónico, comuniquese con el administrador';
            if($com_recibida != null){ ComRecibidaPeer::deleteCascada($com_recibida->getPrimaryKey()); }
        } catch (\Exception $th) {
            $message_status = 'Ocurrio un error interno en la aplicación y no se radico el correo electrónico, comuniquese con el administrador';
            if($com_recibida != null){ ComRecibidaPeer::deleteCascada($com_recibida->getPrimaryKey()); }
        } catch (\Throwable $th) {
            $message_status = 'Ocurrio un error interno en el servidor y no se radico el correo electrónico, comuniquese con el administrador';
            if($com_recibida != null){ ComRecibidaPeer::deleteCascada($com_recibida->getPrimaryKey()); }
        }
        //***************************************************************************************************
        $this->getResponse()->setContentType('application/json');
        $response_info = array('status' => 200, 'message' => $message_status, 'url_redirect' => $url_redirect);
        return $this->renderText(json_encode($response_info));
    }

    /**
     * email_syncActions::executeListHist
     * @return void retorna la vista asociada a este actions
    */
    public function executeListHist()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $email_id = trim($this->getRequestParameter('email_id')) ? SED::decryption(trim($this->getRequestParameter('email_id'))) : null;
        $this->parametros = "&a=1";
        //***********************************************************************************************
        $c = new Criteria();
        $c->setDistinct();
        //***********************************************************************************************
        $c->addJoin(EmailBitacoraPeer::EMAIL_ID,EmailPeer::EMAIL_ID,Criteria::INNER_JOIN);
        $c->addJoin(EmailBitacoraPeer::USUARIO_ID,UsuarioPeer::USUARIO_ID,Criteria::INNER_JOIN);
        //***********************************************************************************************
        $c->add(EmailBitacoraPeer::EMAIL_ID,$email_id);
        //***********************************************************************************************
        $pager = new sfPropelPager('EmailBitacora', 15);
        $pager->setCriteria($c);
        $pager->setPage($this->getRequestParameter('page', 1));
        $pager->init();
        $this->pager = $pager;
        //***********************************************************************************************
        $this->account_list = EmailAccountUsuarioPeer::getEmailAccountByCurrentUser($usuariologuiado);
    }

    /**
     * email_syncActions::executeEsync.     
     * @return void retorna la vista asociada a este actions
    */
    public function executeEsync()
    {
        $this->verificaPrilegioCerrar('email_sync/esync');
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $this->parametros = "&a=1";
        //***********************************************************************************************
        $c = new Criteria();
        $c->setDistinct();
        //***********************************************************************************************
        $c->add(EmailAccountPeer::USUARIO_ID,$usuariologuiado);
        //***********************************************************************************************
        $pager = new sfPropelPager('EmailAccount', 15);
        $pager->setCriteria($c);
        $pager->setPage($this->getRequestParameter('page', 1));
        $pager->init();
        $this->pager = $pager;
    }

    /**
     * email_syncActions::executeList
     * @return void retorna la vista asociada a este actions
    */
    public function executeList()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $this->parametros = "&a=1";
        //***********************************************************************************************
        $this->verificaPrilegio('email_sync/list');
        //***********************************************************************************************
        $c = new Criteria();
        //***********************************************************************************************
        $c = $this->getCriteriaBasic($c);
        //$c->setDistinct();
        //***********************************************************************************************
        $pager = new sfPropelPager('Email', 15);
        $pager->setCriteria($c);
        $pager->setPeerMethod('doSelectCustom');
        $pager->setPeerCountMethod('doCountCustom');
        $pager->setPage($this->getRequestParameter('page', 1));
        $pager->init();
        $this->pager = $pager;
        //***********************************************************************************************
        $this->account_list = EmailAccountUsuarioPeer::getEmailAccountByCurrentUser($usuariologuiado);
    }

    /**
     * email_syncActions::executeListAsync
     * @return void retorna la vista asociada a este actions
    */
    public function executeListAsync()
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $this->parametros = "&a=1";
        //***********************************************************************************************
        $c = new Criteria();
        $c = $this->getCriteriaBasic($c);
        //$c->setDistinct();
        //***********************************************************************************************
        $pager = new sfPropelPager('Email', 15);
        $pager->setCriteria($c);
        $pager->setPeerMethod('doSelectCustom');
        $pager->setPeerCountMethod('doCountCustom');
        $pager->setPage($this->getRequestParameter('page', 1));
        $pager->init();
        $this->pager = $pager;
    }

    /**
     * email_syncActions::getCriteriaBasic
     * @param Criteria objeto criteria principal el cual se adicionaran los filtros 
     * basado en los parametros del formulario de consulta
     * @return Criteria retorna objeto criteria con los filtros aplicados
    */
    private function getCriteriaBasic(Criteria $c)
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $accountemail_id = trim($this->getRequestParameter('accountemail_id')) ? trim($this->getRequestParameter('accountemail_id')) : null;
        $isValidAccess = false;
        //***********************************************************************************************
        if(empty($accountemail_id)){
            $c->add(EmailAccountPeer::EMAILACCOUNT_ID,-1);
            return $c;
        }
        //***********************************************************************************************
        if(!$this->tienePrivilegio('EMAIL_LIST_ALL_ACCOUNT')){
            $email_account = EmailAccountPeer::retrieveByPk($accountemail_id);
            if($email_account == null){
                $account_list = EmailAccountUsuarioPeer::getEmailAccountByCurrentUser($usuariologuiado);
                //***************************************************************************************
                if(empty($account_list)){
                    $c->add(EmailAccountPeer::EMAILACCOUNT_ID,-1);
                }else{
                    $email_account = $account_list[0];
                    $accountemail_id = $email_account->getPrimaryKey();
                    $c->add(EmailAccountPeer::EMAILACCOUNT_ID,$account_list[0]->getPrimaryKey());
                }
            }
            //*******************************************************************************************
            $isValidAccess = false;
            if($email_account != null){
                $isValidAccess = EmailAccountUsuarioPeer::getUserAccessEmailAccount($email_account->getAccountLogin(),$usuariologuiado);
            }
        }elseif(!empty($accountemail_id)){
            $isValidAccess = true;
            $email_account = EmailAccountPeer::retrieveByPk($accountemail_id);
        }
        //***********************************************************************************************
        $this->parametros .= "&accountemail_id=" . $accountemail_id;
        //***********************************************************************************************
        $c->addJoin(EmailPeer::EMAILACCOUNT_ID,EmailAccountPeer::EMAILACCOUNT_ID,Criteria::INNER_JOIN);
        $c->addJoin(EmailAccountPeer::EMAILACCOUNT_ID,EmailAccountUsuarioPeer::EMAILACCOUNT_ID,Criteria::INNER_JOIN);
        //$c->add(EmailPeer::EMAILACCOUNT_ID,$accountemail_id);
        //***********************************************************************************************
        if($email_account != null){
            $c->add(EmailAccountPeer::ACCOUNT_LOGIN,$email_account->getAccountLogin());
        }else{
            $c->add(EmailAccountPeer::ACCOUNT_LOGIN,null,Criteria::ISNOTNULL);
        }
        //***********************************************************************************************
        if($isValidAccess == false){
            $c->add(EmailAccountUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $c->add(EmailAccountPeer::EMAILACCOUNT_ID,$accountemail_id);
        }
        //***********************************************************************************************
        if ($this->getRequestParameter('erecfecha_inicio')) {
            if ($this->getRequestParameter('erecfecha_fin')) {
                $c->add(EmailPeer::FECHA_RECIBIDO, $this->getRequestParameter('erecfecha_inicio') . ' 00:00:00', Criteria::GREATER_THAN);
                $c->addAnd(EmailPeer::FECHA_RECIBIDO, $this->getRequestParameter('erecfecha_fin') . ' 23:59:59', Criteria::LESS_THAN);
                $this->parametros .= "&erecfecha_inicio=" . str_replace("/", "-", $this->getRequestParameter('erecfecha_inicio'));
                $this->parametros .= "&erecfecha_fin=" . str_replace("/", "-", $this->getRequestParameter('erecfecha_fin'));
            } else {
                $c->add(EmailPeer::FECHA_RECIBIDO, $this->getRequestParameter('erecfecha_inicio') . ' 00:00:00', Criteria::GREATER_THAN);
                $this->parametros .= "&erecfecha_inicio=" . str_replace("/", "-", $this->getRequestParameter('erecfecha_inicio'));
            }
        }
        //***********************************************************************************************
        if (!empty(trim($this->getRequestParameter('subject_mail')))) {
            $c->add(EmailPeer::ASUNTO, '%' . trim($this->getRequestParameter('subject_mail')) . '%', Criteria::LIKE);
            $this->parametros .= "&subject_mail=" . trim($this->getRequestParameter('subject_mail'));
        }
        //***********************************************************************************************
        if (!empty(trim($this->getRequestParameter('body_mail')))) {
            $c->add(EmailPeer::BODY_CLEAN, '%' . trim($this->getRequestParameter('body_mail')) . '%', Criteria::LIKE);
            $this->parametros .= "&body_mail=" . trim($this->getRequestParameter('body_mail'));
        }
        //***********************************************************************************************
        $esta_asignado = trim($this->getRequestParameter('tipo_asignacion')) ? trim($this->getRequestParameter('tipo_asignacion')) : 1;
        if (!empty($esta_asignado)) {
            if($esta_asignado == EmailTypeList::Asignado){
                $c->add(EmailPeer::ESTA_ASIGNADO,EmailTypeList::Asignado);
                $c->add(EmailPeer::USUARIO_ID,$usuariologuiado);
            }else if($esta_asignado == EmailTypeList::NoAsignado){
                $c->add(EmailPeer::ESTA_ASIGNADO,EmailTypeList::Asignado,Criteria::NOT_EQUAL);
            }else if($this->tienePrivilegio('EMAIL_ALL_DOWNLOAD_BY_ACCOUNT')){
                $c->add(EmailPeer::ESTA_ASIGNADO,null,Criteria::ISNOTNULL);
            }else{
                $c->add(EmailPeer::ESTA_ASIGNADO,EmailTypeList::Asignado);
                $c->add(EmailPeer::USUARIO_ID,$usuariologuiado);
            }
            $this->parametros .= "&tipo_asignacion=" . $esta_asignado;
        }
        //***********************************************************************************************
        $c->addDescendingOrderByColumn(EmailPeer::FECHA_CREACION);
        $c->addDescendingOrderByColumn(EmailPeer::EMAIL_ID);
        //***********************************************************************************************
        return $c;
    }
}