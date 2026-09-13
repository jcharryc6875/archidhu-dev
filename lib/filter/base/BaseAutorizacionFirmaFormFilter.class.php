<?php

/**
 * AutorizacionFirma filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseAutorizacionFirmaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOFIRMAAUTO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'EstadoFirmaAuto', 'add_empty' => true)),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'MODULO_ID'            => new sfWidgetFormFilterInput(),
      'FIRMA_ELECTRONICA'    => new sfWidgetFormFilterInput(),
      'FIRMA_DIGITAL'        => new sfWidgetFormFilterInput(),
      'FIRMA_FISICA'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ESTADOFIRMAAUTO_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoFirmaAuto', 'column' => 'ESTADOFIRMAAUTO_ID')),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'MODULO_ID'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FIRMA_ELECTRONICA'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FIRMA_DIGITAL'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FIRMA_FISICA'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('autorizacion_firma_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AutorizacionFirma';
  }

  public function getFields()
  {
    return array(
      'AUTORIZACIONFIRMA_ID' => 'Number',
      'ESTADOFIRMAAUTO_ID'   => 'ForeignKey',
      'USUARIO_ID'           => 'ForeignKey',
      'MODULO_ID'            => 'Number',
      'FIRMA_ELECTRONICA'    => 'Number',
      'FIRMA_DIGITAL'        => 'Number',
      'FIRMA_FISICA'         => 'Number',
    );
  }
}
