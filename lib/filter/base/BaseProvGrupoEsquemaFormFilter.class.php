<?php

/**
 * ProvGrupoEsquema filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvGrupoEsquemaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CODIGO'                => new sfWidgetFormFilterInput(),
      'DESCRIPCION'           => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'CODIGO'                => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_grupo_esquema_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvGrupoEsquema';
  }

  public function getFields()
  {
    return array(
      'PROV_GRUPO_ESQUEMA_ID' => 'Number',
      'CODIGO'                => 'Text',
      'DESCRIPCION'           => 'Text',
    );
  }
}
