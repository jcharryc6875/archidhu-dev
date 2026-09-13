<?php

/**
 * subserie actions.
 *
 * @package    simad
 * @subpackage subserie
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class subserieActions extends autosubserieActions
{
  public function executeList()
  {
    $this->processSort();

    $this->processFilters();
    $serie_id=$this->getRequestParameter('serie_id');
    if($serie_id){
		$c2=new Criteria();
		$c2->add(SubseriePeer::SERIE_ID, $serie_id);
	    $this->pager = new sfPropelPager('Subserie', 20);
        //$c = new Criteria();
        $this->addSortCriteria($c2);
        $this->addFiltersCriteria($c2);
        $this->pager->setCriteria($c2);
        $this->pager->setPage($this->getRequestParameter('page', 1));
        $this->pager->init();	
		//$subserie = SubseriePeer::doSelectOne($c2);
	}else{
	// pager
    $this->pager = new sfPropelPager('Subserie', 20);
    $c = new Criteria();
    $this->addSortCriteria($c);
    $this->addFiltersCriteria($c);
    $this->pager->setCriteria($c);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();	
		
	}


    
  }
}
