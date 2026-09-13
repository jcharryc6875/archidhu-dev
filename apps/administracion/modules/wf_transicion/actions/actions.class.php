<?php

/**
 * wf_transicion actions.
 *
 * @package    simad
 * @subpackage wf_transicion
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class wf_transicionActions extends autowf_transicionActions
{
  
  public function executePermisos(sfWebRequest $request)
  {
    $this->redirect('wf_permiso_transicion_actividad/index?wf_transicion_id='.$request->getParameter('wf_transicion_id'));
  }
  
}
