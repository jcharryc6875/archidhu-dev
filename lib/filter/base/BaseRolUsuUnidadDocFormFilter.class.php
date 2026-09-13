<?php

/**
 * RolUsuUnidadDoc filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRolUsuUnidadDocFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_usu_unidad_doc_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolUsuUnidadDoc';
  }

  public function getFields()
  {
    return array(
      'ROLUSUUNIDADDOC_ID' => 'Number',
      'DESCRIPCION'        => 'Text',
    );
  }
}
