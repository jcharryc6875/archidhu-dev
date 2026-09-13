<?php

/**
 * tipo_procedimiento actions.
 *
 * @package    simad
 * @subpackage tipo_procedimiento
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class tipo_procedimientoActions extends autotipo_procedimientoActions
{
	public function executeList()
  {
    $this->processSort();

    $this->processFilters();

    $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/tipo_procedimiento/filters');

    // pager
    $this->pager = new sfPropelPager('TipoProcedimiento', 4);
    $c = new Criteria();
    //$this->addSortCriteria($c);
    $this->addFiltersCriteria($c);
    $this->pager->setCriteria($c);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
  }
}
