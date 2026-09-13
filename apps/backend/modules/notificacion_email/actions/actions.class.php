<?php

/**
 * factura_receptor actions.
 *
 * @package    simad
 * @subpackage factura_receptor
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class notificacion_emailActions extends autonotificacion_emailActions
{

    public function executeEdit($request)
    {
        $this->notificacion_email = $this->getNotificacionEmailOrCreate();

        if ($request->isMethod(sfRequest::POST))
        {
            $notificacion_email_old = clone $this->notificacion_email;
            $this->updateNotificacionEmailFromRequest();

            try
            {
                $resp_save = $this->saveNotificacionEmail($this->notificacion_email);
                //*******************************************************************************
                if($resp_save === true){
                    AuditLogPeer::guardarAuditoriaLite(NotificacionEmailPeer::getOMClass(),$notificacion_email_old,$this->notificacion_email,
                        ModulesEnable::Seguridad,$this->notificacion_email->getPrimaryKey());
                }else{
                    $request->setError('edit', $resp_save);
                    return $this->forward('notificacion_email', 'list');
                }
            }
            catch (PropelException $e)
            {
                $request->setError('edit', 'Could not save the edited Notificacion emails.');
                return $this->forward('notificacion_email', 'list');
            }

            $this->getUser()->setFlash('notice', 'Your modifications have been saved');

            if ($this->getRequestParameter('save_and_add'))
            {
                return $this->redirect('notificacion_email/create');
            }
            else if ($this->getRequestParameter('save_and_list'))
            {
                return $this->redirect('notificacion_email/list');
            }
            else
            {
                return $this->redirect('notificacion_email/edit?notificacionemail_id='.$this->notificacion_email->getNotificacionemailId());
            }
        }
        else
        {
            $this->labels = $this->getLabels();
        }
    }

    protected function getNotificacionEmailOrCreate($notificacionemail_id = 'notificacionemail_id')
    {
        if ($this->getRequestParameter($notificacionemail_id) === ''
        || $this->getRequestParameter($notificacionemail_id) === null)
        {
            $notificacion_email = new NotificacionEmail();
        }
        else
        {
            $notificacion_email = NotificacionEmailPeer::retrieveByPk($this->getRequestParameter($notificacionemail_id));
            /*if(!empty($notificacion_email->getEmailPass())){
                //$notificacion_email->setEmailPass(SED::decryption($notificacion_email->getEmailPass()));
            }*/
            $this->forward404Unless($notificacion_email);
        }

        return $notificacion_email;
    }

    protected function saveNotificacionEmail($notificacion_email)
    {
        $domains_config = simad_util::readConfigFileApp(['domains_enable']);
        $domains_enable = array();
        if($domains_config != null && isset($domains_config['domains_enable'])){
            $domains_enable = preg_split("/[,]+/", $domains_config['domains_enable'], -1, PREG_SPLIT_NO_EMPTY);
        }
        //************************************************************************************************
        $current_email = $notificacion_email->getEmailLogin();
        //************************************************************************************************
        if (!filter_var($current_email, FILTER_VALIDATE_EMAIL)) {
            return 'El correo electronico suministrador, no es una cuenta de correo valida';
        }
        //************************************************************************************************
        $partes = explode("@", $current_email);
        if(!in_array($partes[1], $domains_enable)){
            return 'El correo electronico suministrador, no esta permitido';
        }
        //************************************************************************************************
        $modifiedColumns = $notificacion_email->getModifiedColumns();
        if(in_array(NotificacionEmailPeer::EMAIL_PASS, $modifiedColumns)){
            $notificacion_email->setEmailPass(SED::encryption($notificacion_email->getEmailPass()));
        }
        //************************************************************************************************
        $notificacion_email->save();
        //************************************************************************************************
        return true;
    }
}
