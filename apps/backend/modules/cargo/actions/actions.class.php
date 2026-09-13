<?php

/**
 * forma actions.
 *
 * @package    simad
 * @subpackage cargo
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class cargoActions extends autocargoActions
{
	public function verificaPrilegio($currentForm)
	{ 
		$usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
		if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado)){
			$this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
		}
	}
  
	public function executeIndex()
	{
		return $this->forward('cargo', 'list');
	}
	
	public function executeList()
	{ 
		$this->verificaPrilegio("cargo/list");

		$this->processSort();

		$this->processFilters();


		// pager
		$this->pager = new sfPropelPager('Cargo', 25);
		$c = new Criteria();
		$this->addSortCriteria($c);
		$this->addFiltersCriteria($c);
		$this->pager->setCriteria($c);
		$this->pager->setPage($this->getRequestParameter('page', $this->getUser()->getAttribute('page', 1, 'sf_admin/cargo')));
		$this->pager->init();
			
		// save page
		if ($this->getRequestParameter('page')) {
			$this->getUser()->setAttribute('page', $this->getRequestParameter('page'), 'sf_admin/cargo');
		}
	}
	
	public function executeCreate()
	{
		return $this->forward('cargo', 'edit');
	}

	public function executeSave()
	{
		return $this->forward('cargo', 'edit');
	}
	
	public function executeEdit()
	{
		$this->cargo = $this->getCargoOrCreate();

		if ($this->getRequest()->getMethod() == sfRequest::POST)
		{
		  $this->updateCargoFromRequest();

		  $this->saveCargo($this->cargo);

		  $this->getUser()->setFlash('notice', 'Your modifications have been saved');

		  if ($this->getRequestParameter('save_and_add'))
		  {
			return $this->redirect($this->getRequest()->getScriptName().'/cargo/create');
		  }
		  else if ($this->getRequestParameter('save_and_list'))
		  {
			return $this->redirect($this->getRequest()->getScriptName().'/cargo/list');
		  }
		  else
		  {
			return $this->redirect($this->getRequest()->getScriptName().'/cargo/edit?cargo_id='.$this->cargo->getCargoId());
		  }
		}
		else
		{
		  $this->labels = $this->getLabels();
		}
	}
	
	public function executeDelete()
	{
		$this->cargo = CargoPeer::retrieveByPk($this->getRequestParameter('cargo_id'));
		$this->forward404Unless($this->cargo);

		try
		{
		  $this->deleteCargo($this->cargo);
		}
		catch (PropelException $e)
		{
		  $this->getRequest()->setError('delete', 'Could not delete the selected Cargo. Make sure it does not have any associated items.');
		  return $this->forward('cargo', 'list');
		}

		return $this->redirect($this->getRequest()->getScriptName().'/cargo/list');
	}
	
	public function handleErrorEdit()
	{
		$this->preExecute();
		$this->cargo = $this->getCargoOrCreate();
		$this->updateCargoFromRequest();

		$this->labels = $this->getLabels();

		return sfView::SUCCESS;
	}

	protected function saveCargo($cargo)
	{
		$cargo->save();
	}
	
	protected function deleteCargo($cargo)
	{
		$cargo->delete();
	}

	protected function updateCargoFromRequest()
	{
		$cargo = $this->getRequestParameter('cargo');

		if (isset($cargo['descripcion']))
		{
		  $this->cargo->setDescripcion($cargo['descripcion']);
		}
	}
	
	protected function getCargoOrCreate($cargo_id = 'cargo_id')
	{
		if (!$this->getRequestParameter($cargo_id))
		{
		  $cargo_id = new Cargo();
		}
		else
		{
		  $cargo_id = CargoPeer::retrieveByPk($this->getRequestParameter($cargo_id));

		  $this->forward404Unless($cargo_id);
		}

		return $cargo_id;
	}

	protected function processFilters()
	{
	}

	protected function processSort()
	{
		if ($this->getRequestParameter('sort'))
		{
		  $this->getUser()->setAttribute('sort', ($this->getRequestParameter('sort')), 'sf_admin/cargo/sort');
		  $this->getUser()->setAttribute('type', $this->getRequestParameter('type', 'asc'), 'sf_admin/cargo/sort');
		}

		if (!$this->getUser()->getAttribute('sort', null, 'sf_admin/cargo/sort'))
		{
			
		}
	}
	
	protected function addFiltersCriteria($c)
	{
	}

	protected function addSortCriteria($c)
	{
		if ($sort_column = $this->getUser()->getAttribute('sort', null, 'sf_admin/cargo/sort'))
		{
		  $sort_column = sfInflector::camelize(mb_strtolower($sort_column));      
		  $sort_column = CargoPeer::translateFieldName($sort_column, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME);
				
		  
		  if ($this->getUser()->getAttribute('type', null, 'sf_admin/cargo/sort') == 'asc')
		  {
			$c->addAscendingOrderByColumn($sort_column);
		  }
		  else
		  {
			$c->addDescendingOrderByColumn($sort_column);
		  }
		}
	}

	protected function getLabels()
	{
		return array(
		  'cargo{cargo_id}' => 'Cargo:',
		  'cargo{descripcion}' => 'Descripcion:',
		);
	}
}
