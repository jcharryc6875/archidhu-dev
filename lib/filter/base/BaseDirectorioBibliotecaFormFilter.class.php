<?php

/**
 * DirectorioBiblioteca filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDirectorioBibliotecaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'             => new sfWidgetFormFilterInput(),
      'URL'                     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'             => new sfValidatorPass(array('required' => false)),
      'URL'                     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('directorio_biblioteca_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DirectorioBiblioteca';
  }

  public function getFields()
  {
    return array(
      'DIRECTORIOBIBLIOTECA_ID' => 'Number',
      'DESCRIPCION'             => 'Text',
      'URL'                     => 'Text',
    );
  }
}
