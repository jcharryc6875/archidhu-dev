<?php

/**
 * DirectorioExtended filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDirectorioExtendedFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DIRECTORIOEXTERNO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'DirectorioExterno', 'add_empty' => true)),
      'FUNCIONARIO'           => new sfWidgetFormFilterInput(),
      'CARGO'                 => new sfWidgetFormFilterInput(),
      'DIRECCION'             => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DIRECTORIOEXTERNO_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DirectorioExterno', 'column' => 'DIRECTORIOEXTERNO_ID')),
      'FUNCIONARIO'           => new sfValidatorPass(array('required' => false)),
      'CARGO'                 => new sfValidatorPass(array('required' => false)),
      'DIRECCION'             => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('directorio_extended_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DirectorioExtended';
  }

  public function getFields()
  {
    return array(
      'DIRECTORIOEXTENDED_ID' => 'Number',
      'DIRECTORIOEXTERNO_ID'  => 'ForeignKey',
      'FUNCIONARIO'           => 'Text',
      'CARGO'                 => 'Text',
      'DIRECCION'             => 'Text',
    );
  }
}
