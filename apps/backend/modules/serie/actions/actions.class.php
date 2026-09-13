<?php

/**
 * serie actions.
 *
 * @package    simad
 * @subpackage serie
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class serieActions extends autoserieActions
{

  public function executeShowSubserie()
  {
    $serie_id=$this->getRequestParameter("serie_id");
    $url="subserie?serie_id=".$serie_id;
    $this->redirect($url);


	/*
    $c = new Criteria();
    $c->add(SubseriePeer::SERIE_ID, $serie_id);
    $subserie = SubseriePeer::doSelectOne($c);
    
getSubseries($criteria = null, $con = null)
    if($subserie){
	   $url='subserie';
         //$this->forward404Unless($url);	
         $this->redirect($url);
    }
    */

  }		
	

}
