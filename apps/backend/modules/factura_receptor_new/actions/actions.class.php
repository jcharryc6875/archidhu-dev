<?php

/**
 * autoFactura_receptor actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage autoFactura_receptor
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 9926 2008-06-27 12:25:54Z noel $
 */
class Factura_receptorActions extends sfActions
{
  public function executeIndex()
  {
    return $this->forward('factura_receptor', 'list');
  }

  public function executeList()
  {
    $this->processSort();

    $this->processFilters();


    // pager
    $this->pager = new sfPropelPager('FacturaReceptor', 20);
    $c = new Criteria();
    $this->addSortCriteria($c);
    $this->addFiltersCriteria($c);
    $this->pager->setCriteria($c);
    $this->pager->setPage($this->getRequestParameter('page', $this->getUser()->getAttribute('page', 1, 'sf_admin/factura_receptor')));
    $this->pager->init();
    // save page
    if ($this->getRequestParameter('page')) {
        $this->getUser()->setAttribute('page', $this->getRequestParameter('page'), 'sf_admin/factura_receptor');
    }
  }

  public function executeCreate()
  {
    return $this->forward('factura_receptor', 'edit');
  }

  public function executeSave()
  {
    return $this->forward('factura_receptor', 'edit');
  }


  public function executeDeleteSelected()
  {
    $this->selectedItems = $this->getRequestParameter('sf_admin_batch_selection', array());

    try
    {
      foreach (FacturaReceptorPeer::retrieveByPks($this->selectedItems) as $object)
      {
        $object->delete();
      }
    }
    catch (PropelException $e)
    {
      $this->getRequest()->setError('delete', 'Could not delete the selected Factura receptors. Make sure they do not have any associated items.');
      return $this->forward('factura_receptor', 'list');
    }

    return $this->redirect($this->getRequest()->getScriptName().'/factura_receptor/list');
  }

  public function executeEdit()
  {
    $this->factura_receptor = $this->getFacturaReceptorOrCreate();

    if ($this->getRequest()->isMethod('post'))
    {
      $this->updateFacturaReceptorFromRequest();

      try
      {
        $this->saveFacturaReceptor($this->factura_receptor);
      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('edit', 'Could not save the edited Factura receptors.');
        return $this->forward('factura_receptor', 'list');
      }

      $this->getUser()->setFlash('notice', 'Your modifications have been saved');

      if ($this->getRequestParameter('save_and_add'))
      {
        return $this->redirect($this->getRequest()->getScriptName().'/factura_receptor/create');
      }
      else if ($this->getRequestParameter('save_and_list'))
      {
        return $this->redirect($this->getRequest()->getScriptName().'/factura_receptor/list');
      }
      else
      {
        return $this->redirect($this->getRequest()->getScriptName().'/factura_receptor/edit?facturareceptor_id='.$this->factura_receptor->getFacturareceptorId());
      }
    }
    else
    {
      $this->labels = $this->getLabels();
    }
  }

  public function executeDelete()
  {
    $this->factura_receptor = FacturaReceptorPeer::retrieveByPk($this->getRequestParameter('facturareceptor_id'));
    $this->forward404Unless($this->factura_receptor);

    try
    {
      $this->deleteFacturaReceptor($this->factura_receptor);
    }
    catch (PropelException $e)
    {
      $this->getRequest()->setError('delete', 'Could not delete the selected Factura receptor. Make sure it does not have any associated items.');
      return $this->forward('factura_receptor', 'list');
    }

    return $this->redirect($this->getRequest()->getScriptName().'/factura_receptor/list');
  }

  public function handleErrorEdit()
  {
    $this->preExecute();
    $this->factura_receptor = $this->getFacturaReceptorOrCreate();
    $this->updateFacturaReceptorFromRequest();

    $this->labels = $this->getLabels();

    return sfView::SUCCESS;
  }

  protected function saveFacturaReceptor($factura_receptor)
  {
    $factura_receptor->save();

  }

  protected function deleteFacturaReceptor($factura_receptor)
  {
    $factura_receptor->delete();
  }

  protected function updateFacturaReceptorFromRequest()
  {
    $factura_receptor = $this->getRequestParameter('factura_receptor');

    if (isset($factura_receptor['facturaproceso_id']))
    {
    $this->factura_receptor->setFacturaprocesoId($factura_receptor['facturaproceso_id'] ? $factura_receptor['facturaproceso_id'] : null);
    }
    if (isset($factura_receptor['regional_id']))
    {
    $this->factura_receptor->setRegionalId($factura_receptor['regional_id'] ? $factura_receptor['regional_id'] : null);
    }
    if (isset($factura_receptor['usuario_id']))
    {
    $this->factura_receptor->setUsuarioId($factura_receptor['usuario_id'] ? $factura_receptor['usuario_id'] : null);
    }
  }

  protected function getFacturaReceptorOrCreate($facturareceptor_id = 'facturareceptor_id')
  {
    if ($this->getRequestParameter($facturareceptor_id) === ''
     || $this->getRequestParameter($facturareceptor_id) === null)
    {
      $factura_receptor = new FacturaReceptor();
    }
    else
    {
      $factura_receptor = FacturaReceptorPeer::retrieveByPk($this->getRequestParameter($facturareceptor_id));

      $this->forward404Unless($factura_receptor);
    }

    return $factura_receptor;
  }

  protected function processFilters()
  {
  }

  protected function processSort()
  {
    if ($this->getRequestParameter('sort'))
    {
      $this->getUser()->setAttribute('sort', $this->getRequestParameter('sort'), 'sf_admin/factura_receptor/sort');
      $this->getUser()->setAttribute('type', $this->getRequestParameter('type', 'asc'), 'sf_admin/factura_receptor/sort');
    }

    if (!$this->getUser()->getAttribute('sort', null, 'sf_admin/factura_receptor/sort'))
    {
    	$this->getUser()->setAttribute('sort', $this->getRequestParameter('sort'), 'sf_admin/factura_receptor/sort');
    	$this->getUser()->setAttribute('type', $this->getRequestParameter('type', 'asc'), 'sf_admin/factura_receptor/sort');
    }
  }

  protected function addFiltersCriteria($c)
  {
  }

  protected function addSortCriteria($c)
  {
    if ($sort_column = $this->getUser()->getAttribute('sort', null, 'sf_admin/factura_receptor/sort'))
    {
      $sort_column = sfInflector::camelize(mb_strtolower($sort_column));
      $sort_column = FacturaReceptorPeer::translateFieldName($sort_column, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME);
      if ($this->getUser()->getAttribute('type', null, 'sf_admin/factura_receptor/sort') == 'asc')
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
      'factura_receptor{facturareceptor_id}' => 'Facturareceptor:',
      'factura_receptor{facturaproceso_id}' => 'Facturaproceso:',
      'factura_receptor{regional_id}' => 'Regional:',
      'factura_receptor{usuario_id}' => 'Usuario:',
    );
  }
}
