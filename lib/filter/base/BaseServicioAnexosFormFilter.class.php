<?php

/**
 * ServicioAnexos filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseServicioAnexosFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERVICIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Servicio', 'add_empty' => true)),
      'USUARIO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'DESCRIPCION'       => new sfWidgetFormFilterInput(),
      'RUTA'              => new sfWidgetFormFilterInput(),
      'FOLIOS'            => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'ES_ACTUAL'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'SERVICIO_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Servicio', 'column' => 'SERVICIO_ID')),
      'USUARIO_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DESCRIPCION'       => new sfValidatorPass(array('required' => false)),
      'RUTA'              => new sfValidatorPass(array('required' => false)),
      'FOLIOS'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_CREACION'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'ES_ACTUAL'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('servicio_anexos_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServicioAnexos';
  }

  public function getFields()
  {
    return array(
      'SERVICIOANEXOS_ID' => 'Number',
      'SERVICIO_ID'       => 'ForeignKey',
      'USUARIO_ID'        => 'ForeignKey',
      'DESCRIPCION'       => 'Text',
      'RUTA'              => 'Text',
      'FOLIOS'            => 'Number',
      'FECHA_CREACION'    => 'Date',
      'ES_ACTUAL'         => 'Number',
    );
  }
}
