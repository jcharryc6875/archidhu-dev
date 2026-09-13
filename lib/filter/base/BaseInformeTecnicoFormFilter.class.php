<?php

/**
 * InformeTecnico filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseInformeTecnicoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCUMENTACION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'PAGINACION'              => new sfWidgetFormFilterInput(),
      'ILUSTRACIONES'           => new sfWidgetFormFilterInput(),
      'VOLUMEN'                 => new sfWidgetFormFilterInput(),
      'MATERIAL_COMPLEMENTARIO' => new sfWidgetFormFilterInput(),
      'NOTAS_CONTENIDO'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DOCUMENTACION_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'PAGINACION'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ILUSTRACIONES'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'VOLUMEN'                 => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'MATERIAL_COMPLEMENTARIO' => new sfValidatorPass(array('required' => false)),
      'NOTAS_CONTENIDO'         => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('informe_tecnico_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'InformeTecnico';
  }

  public function getFields()
  {
    return array(
      'INFORMETECNICO_ID'       => 'Number',
      'DOCUMENTACION_ID'        => 'ForeignKey',
      'PAGINACION'              => 'Number',
      'ILUSTRACIONES'           => 'Number',
      'VOLUMEN'                 => 'Number',
      'MATERIAL_COMPLEMENTARIO' => 'Text',
      'NOTAS_CONTENIDO'         => 'Text',
    );
  }
}
