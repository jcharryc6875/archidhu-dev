<?php

/**
 * usuario actions.
 *
 * @package    simad
 * @subpackage usuario
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class usuarioActions extends sfActions 
//autousuarioActions
{
  public function executeShow()
  {
     $this->usuario = UsuarioPeer::retrieveByPk($this->getRequestParameter('usuario_id'));
    $this->forward404Unless($this->usuario);
  	
  }		
	
  public function executeEdit()
  {
    $this->usuario = $this->getUsuarioOrCreate();

    if ($this->getRequest()->getMethod() == sfRequest::POST)
    {
      $this->updateUsuarioFromRequest();
      
      
      if(! $this->usuario->getFechaCreacion()){
	      $this->usuario->setFechaCreacion( date('Y-m-d G:i:s'));	
	  }

      $this->saveUsuario($this->usuario);

      $this->getUser()->setFlash('notice', 'Your modifications have been saved');
      
      
      /* Inicio Manejo del historico de usuario */
      
      $hist=new HistUsuario();
      $hist->fromArray($this->usuario->toArray());
      $hist->setFechaModificacion( date('Y-m-d G:i:s'));
      $hist->save();
      
      /* Fin Manejo del historico de usuario */
      

      if ($this->getRequestParameter('save_and_add'))
      {
        return $this->redirect($this->getRequest()->getScriptName().'/usuario/create');
      }
      else if ($this->getRequestParameter('save_and_list'))
      {
        return $this->redirect($this->getRequest()->getScriptName().'/usuario/list');
      }
      else
      {
        return $this->redirect($this->getRequest()->getScriptName().'/usuario/edit?usuario_id='.$this->usuario->getUsuarioId());
      }
    }
    else
    {
      $this->labels = $this->getLabels();
    }
  }
  public function executeIndex()
  {
    return $this->forward('usuario', 'list');
  }

  public function executeList()
  {
    $this->processSort();

    $this->processFilters();


    // pager
    $this->pager = new sfPropelPager('Usuario', 20);
    $c = new Criteria();
    $this->addSortCriteria($c);
    $this->addFiltersCriteria($c);
    $this->pager->setCriteria($c);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
  }

  public function executeCreate()
  {
    return $this->forward('usuario', 'edit');
  }

  public function executeSave()
  {
    return $this->forward('usuario', 'edit');
  }

/*
  public function executeEdit()
  {
    $this->usuario = $this->getUsuarioOrCreate();

    if ($this->getRequest()->getMethod() == sfRequest::POST)
    {
      $this->updateUsuarioFromRequest();

      $this->saveUsuario($this->usuario);

      $this->getUser()->setFlash('notice', 'Your modifications have been saved');

      if ($this->getRequestParameter('save_and_add'))
      {
        return $this->redirect($this->getRequest()->getScriptName().'/usuario/create');
      }
      else if ($this->getRequestParameter('save_and_list'))
      {
        return $this->redirect($this->getRequest()->getScriptName().'/usuario/list');
      }
      else
      {
        return $this->redirect($this->getRequest()->getScriptName().'/usuario/edit?usuario_id='.$this->usuario->getUsuarioId());
      }
    }
    else
    {
      $this->labels = $this->getLabels();
    }
  }
*/

  public function executeDelete()
  {
    $this->usuario = UsuarioPeer::retrieveByPk($this->getRequestParameter('usuario_id'));
    $this->forward404Unless($this->usuario);

    try
    {
      $this->deleteUsuario($this->usuario);
    }
    catch (PropelException $e)
    {
      $this->getRequest()->setError('delete', 'Could not delete the selected Usuario. Make sure it does not have any associated items.');
      return $this->forward('usuario', 'list');
    }

      $currentFile = sfConfig::get('sf_upload_dir')."/imagenes/fotos/".$this->usuario->getRutaFoto();
      if (is_file($currentFile))
      {
        unlink($currentFile);
      }

    return $this->redirect($this->getRequest()->getScriptName().'/usuario/list');
  }

  public function handleErrorEdit()
  {
    $this->preExecute();
    $this->usuario = $this->getUsuarioOrCreate();
    $this->updateUsuarioFromRequest();

    $this->labels = $this->getLabels();

    return sfView::SUCCESS;
  }

  protected function saveUsuario($usuario)
  {
    $usuario->save();

  }

  protected function deleteUsuario($usuario)
  {
    $usuario->delete();
  }

  protected function updateUsuarioFromRequest()
  {
    $usuario = $this->getRequestParameter('usuario');

    if (isset($usuario['cedula']))
    {
      $this->usuario->setCedula($usuario['cedula']);
    }
    if (isset($usuario['apellido']))
    {
      $this->usuario->setApellido($usuario['apellido']);
    }
    if (isset($usuario['nombre']))
    {
      $this->usuario->setNombre($usuario['nombre']);
    }
    if (isset($usuario['email']))
    {
      $this->usuario->setEmail($usuario['email']);
    }
    $currentFile = sfConfig::get('sf_upload_dir')."/imagenes/fotos/".$this->usuario->getRutaFoto();
    if (!$this->getRequest()->hasErrors() && isset($usuario['ruta_foto_remove']))
    {
      $this->usuario->setRutaFoto('');
      if (is_file($currentFile))
      {
        unlink($currentFile);
      }
    }

    if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('usuario[ruta_foto]'))
    {
      $fileName = md5($this->getRequest()->getFileName('usuario[ruta_foto]').time().rand(0, 99999));
      $ext = $this->getRequest()->getFileExtension('usuario[ruta_foto]');
      if (is_file($currentFile))
      {
        unlink($currentFile);
      }
      $this->getRequest()->moveFile('usuario[ruta_foto]', sfConfig::get('sf_upload_dir')."/imagenes/fotos/".$fileName.$ext);
      $this->usuario->setRutaFoto($fileName.$ext);
    }
    if (isset($usuario['dependencia_id']))
    {
    $this->usuario->setDependenciaId($usuario['dependencia_id'] ? $usuario['dependencia_id'] : null);
    }
    if (isset($usuario['regional_id']))
    {
    $this->usuario->setRegionalId($usuario['regional_id'] ? $usuario['regional_id'] : null);
    }
    if (isset($usuario['user_name']))
    {
      $this->usuario->setUserName($usuario['user_name']);
    }
    if (isset($usuario['password']))
    {
      $this->usuario->setPassword($usuario['password']);
    }
    if (isset($usuario['estadousuario_id']))
    {
    $this->usuario->setEstadousuarioId($usuario['estadousuario_id'] ? $usuario['estadousuario_id'] : null);
    }
  }

  protected function getUsuarioOrCreate($usuario_id = 'usuario_id')
  {
    if (!$this->getRequestParameter($usuario_id))
    {
      $usuario = new Usuario();
    }
    else
    {
      $usuario = UsuarioPeer::retrieveByPk($this->getRequestParameter($usuario_id));

      $this->forward404Unless($usuario);
    }

    return $usuario;
  }

  protected function processFilters()
  {
  }

  protected function processSort()
  {
    if ($this->getRequestParameter('sort'))
    {
      $this->getUser()->setAttribute('sort', $this->getRequestParameter('sort'), 'sf_admin/usuario/sort');
      $this->getUser()->setAttribute('type', $this->getRequestParameter('type', 'asc'), 'sf_admin/usuario/sort');
    }

    if (!$this->getUser()->getAttribute('sort', null, 'sf_admin/usuario/sort'))
    {
    }
  }

  protected function addFiltersCriteria($c)
  {
  }

  protected function addSortCriteria($c)
  {
    if ($sort_column = $this->getUser()->getAttribute('sort', null, 'sf_admin/usuario/sort'))
    {
      $sort_column = UsuarioPeer::translateFieldName($sort_column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);
      if ($this->getUser()->getAttribute('type', null, 'sf_admin/usuario/sort') == 'asc')
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
      'usuario{usuario_id}' => 'Usuario:',
      'usuario{cedula}' => 'Cedula:',
      'usuario{apellido}' => 'Apellido:',
      'usuario{nombre}' => 'Nombre:',
      'usuario{email}' => 'Email:',
      'usuario{ruta_foto}' => 'Ruta foto:',
      'usuario{dependencia_id}' => 'Dependencia:',
      'usuario{regional_id}' => 'Regional:',
      'usuario{user_name}' => 'User name:',
      'usuario{password}' => 'Password:',
      'usuario{estadousuario_id}' => 'Estadousuario:',
      'usuario{fecha_creacion}' => 'Fecha creacion:',
    );
  }

  
  

}
