<?php

/**
 * FacturaTipo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFacturaTipoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'    => new sfWidgetFormFilterInput(),
      'ES_VISIBLE'     => new sfWidgetFormFilterInput(),
      'CODIGO'         => new sfWidgetFormFilterInput(),
      'PARTIAL_NAME'   => new sfWidgetFormFilterInput(),
      'CHECKLIST_HTML' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'    => new sfValidatorPass(array('required' => false)),
      'ES_VISIBLE'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CODIGO'         => new sfValidatorPass(array('required' => false)),
      'PARTIAL_NAME'   => new sfValidatorPass(array('required' => false)),
      'CHECKLIST_HTML' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_tipo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaTipo';
  }

  public function getFields()
  {
    return array(
      'FACTURATIPO_ID' => 'Number',
      'DESCRIPCION'    => 'Text',
      'ES_VISIBLE'     => 'Number',
      'CODIGO'         => 'Text',
      'PARTIAL_NAME'   => 'Text',
      'CHECKLIST_HTML' => 'Text',
    );
  }
}
