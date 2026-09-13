<?php

/**
 * autoTipo_impuesto actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage autoTipo_impuesto
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 9926 2008-06-27 12:25:54Z noel $
 */
class Tipo_impuestoActions extends sfActions
{
  public function executeIndex()
  {
    return $this->forward('tipo_impuesto', 'list');
  }

  public function executeList()
  {
    $this->processSort();

    $this->processFilters();


    // pager
    $this->pager = new sfPropelPager('TipoImpuesto', 20);
    $c = new Criteria();
    $c->setDistinct();
    $c->addAscendingOrderByColumn(TipoImpuestoPeer::DESCRIPCION);
    $this->addSortCriteria($c);
    $this->addFiltersCriteria($c);
    $this->pager->setCriteria($c);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
    // save page
    /*if ($this->getRequestParameter('page')) {
        $this->getUser()->setAttribute('page', $this->getRequestParameter('page'), 'sf_admin/tipo_impuesto');
    }*/
  }

  public function executeCreate()
  {
    return $this->forward('tipo_impuesto', 'edit');
  }

  public function executeSave()
  {
    return $this->forward('tipo_impuesto', 'edit');
  }


  public function executeDeleteSelected()
  {
    $this->selectedItems = $this->getRequestParameter('sf_admin_batch_selection', array());

    try
    {
      foreach (TipoImpuestoPeer::retrieveByPks($this->selectedItems) as $object)
      {
        $object->delete();
      }
    }
    catch (PropelException $e)
    {
      $this->getRequest()->setError('delete', 'Could not delete the selected Tipo impuestos. Make sure they do not have any associated items.');
      return $this->forward('tipo_impuesto', 'list');
    }

    return $this->redirect($this->getRequest()->getScriptName().'/tipo_impuesto/list');
  }

  public function executeEdit()
  {
    $this->tipo_impuesto = $this->getTipoImpuestoOrCreate();

    if ($this->getRequest()->isMethod('post'))
    {
      $this->updateTipoImpuestoFromRequest();

      try
      {
        $this->saveTipoImpuesto($this->tipo_impuesto);
      }
      catch (PropelException $e)
      {
        $this->getRequest()->setError('edit', 'Could not save the edited Tipo impuestos.');
        return $this->forward('tipo_impuesto', 'list');
      }

      $this->getUser()->setFlash('notice', 'Your modifications have been saved');

      if ($this->getRequestParameter('save_and_add'))
      {
        return $this->redirect($this->getRequest()->getScriptName().'/tipo_impuesto/create');
      }
      else if ($this->getRequestParameter('save_and_list'))
      {
        return $this->redirect($this->getRequest()->getScriptName().'/tipo_impuesto/list');
      }
      else
      {
        return $this->redirect($this->getRequest()->getScriptName().'/tipo_impuesto/edit?tipoimpuesto_id='.$this->tipo_impuesto->getTipoimpuestoId());
      }
    }
    else
    {
      $this->labels = $this->getLabels();
    }
  }

  public function executeDelete()
  {
    $this->tipo_impuesto = TipoImpuestoPeer::retrieveByPk($this->getRequestParameter('tipoimpuesto_id'));
    $this->forward404Unless($this->tipo_impuesto);

    try
    {
      $this->deleteTipoImpuesto($this->tipo_impuesto);
    }
    catch (PropelException $e)
    {
      $this->getRequest()->setError('delete', 'Could not delete the selected Tipo impuesto. Make sure it does not have any associated items.');
      return $this->forward('tipo_impuesto', 'list');
    }

    return $this->redirect($this->getRequest()->getScriptName().'/tipo_impuesto/list');
  }

  public function handleErrorEdit()
  {
    $this->preExecute();
    $this->tipo_impuesto = $this->getTipoImpuestoOrCreate();
    $this->updateTipoImpuestoFromRequest();

    $this->labels = $this->getLabels();

    return sfView::SUCCESS;
  }

  protected function saveTipoImpuesto($tipo_impuesto)
  {
    $tipo_impuesto->save();

  }

  protected function deleteTipoImpuesto($tipo_impuesto)
  {
    $tipo_impuesto->delete();
  }

  protected function updateTipoImpuestoFromRequest()
  {
    $tipo_impuesto = $this->getRequestParameter('tipo_impuesto');

    if (isset($tipo_impuesto['descripcion']))
    {
      $this->tipo_impuesto->setDescripcion($tipo_impuesto['descripcion']);
    }
  }

  protected function getTipoImpuestoOrCreate($tipoimpuesto_id = 'tipoimpuesto_id')
  {
    if ($this->getRequestParameter($tipoimpuesto_id) === ''
     || $this->getRequestParameter($tipoimpuesto_id) === null)
    {
      $tipo_impuesto = new TipoImpuesto();
    }
    else
    {
      $tipo_impuesto = TipoImpuestoPeer::retrieveByPk($this->getRequestParameter($tipoimpuesto_id));

      $this->forward404Unless($tipo_impuesto);
    }

    return $tipo_impuesto;
  }

  protected function processFilters()
  {
  }

  protected function processSort()
  {
    if ($this->getRequestParameter('sort'))
    {
      $this->getUser()->setAttribute('sort', $this->getRequestParameter('sort'), 'sf_admin/tipo_impuesto/sort');
      $this->getUser()->setAttribute('type', $this->getRequestParameter('type', 'asc'), 'sf_admin/tipo_impuesto/sort');
    }

    if (!$this->getUser()->getAttribute('sort', null, 'sf_admin/tipo_impuesto/sort'))
    {
    }
  }

  protected function addFiltersCriteria($c)
  {
  }

  protected function addSortCriteria($c)
  {
    if ($sort_column = $this->getUser()->getAttribute('sort', null, 'sf_admin/tipo_impuesto/sort'))
    {
      $sort_column = sfInflector::camelize(mb_strtolower($sort_column));
      $sort_column = TipoImpuestoPeer::translateFieldName($sort_column, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME);
      if ($this->getUser()->getAttribute('type', null, 'sf_admin/tipo_impuesto/sort') == 'asc')
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
      'tipo_impuesto{tipoimpuesto_id}' => 'Tipoimpuesto:',
      'tipo_impuesto{descripcion}' => 'Descripcion:',
    );
  }
}
