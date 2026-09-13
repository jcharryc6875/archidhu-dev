<?php

/**
 * Entidad filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEntidadFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'      => new sfWidgetFormFilterInput(),
      'DIRECTORIO_NAME'  => new sfWidgetFormFilterInput(),
      'ES_ACTUAL'        => new sfWidgetFormFilterInput(),
      'CODIGO'           => new sfWidgetFormFilterInput(),
      'LOGO_HEADER'      => new sfWidgetFormFilterInput(),
      'LOGO_CORPORATIVO' => new sfWidgetFormFilterInput(),
      'USAR_MEMBRETE'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'      => new sfValidatorPass(array('required' => false)),
      'DIRECTORIO_NAME'  => new sfValidatorPass(array('required' => false)),
      'ES_ACTUAL'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CODIGO'           => new sfValidatorPass(array('required' => false)),
      'LOGO_HEADER'      => new sfValidatorPass(array('required' => false)),
      'LOGO_CORPORATIVO' => new sfValidatorPass(array('required' => false)),
      'USAR_MEMBRETE'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('entidad_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Entidad';
  }

  public function getFields()
  {
    return array(
      'ENTIDAD_ID'       => 'Number',
      'DESCRIPCION'      => 'Text',
      'DIRECTORIO_NAME'  => 'Text',
      'ES_ACTUAL'        => 'Number',
      'CODIGO'           => 'Text',
      'LOGO_HEADER'      => 'Text',
      'LOGO_CORPORATIVO' => 'Text',
      'USAR_MEMBRETE'    => 'Number',
    );
  }
}
