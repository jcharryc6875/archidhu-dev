<?php

/**
 * main actions.
 *
 * @package    simad
 * @subpackage main
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class mainActions extends automainActions
{
  public function executeShow()
  { $this->setLayout(false);
  	$forma_id=$this->getRequestParameter("forma_id");
  	
  	$c = new Criteria();
    $c->add(FormaPeer::FORMA_ID, $forma_id);
    $forma = FormaPeer::doSelectOne($c);
    if($forma){
	   $url=$forma->getRuta();
       //$this->forward404Unless($url);	
       $this->redirect($url);
	}
    
  }
}
