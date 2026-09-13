<?php

/**
 * DirectorioExternoHist filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDirectorioExternoHistFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DIRECTORIOEXTERNO_ID'     => new sfWidgetFormPropelChoice(array('model' => 'DirectorioExterno', 'add_empty' => true)),
      'USUARIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'CIUDAD_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => true)),
      'TIPOPETICIONARIO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'TipoPeticionario', 'add_empty' => true)),
      'TIPOIDENTIFICACION_ID'    => new sfWidgetFormPropelChoice(array('model' => 'TipoIdentificacion', 'add_empty' => true)),
      'NOMBRE'                   => new sfWidgetFormFilterInput(),
      'DIRECCION'                => new sfWidgetFormFilterInput(),
      'FUNCIONARIO'              => new sfWidgetFormFilterInput(),
      'CARGO'                    => new sfWidgetFormFilterInput(),
      'TELEFONO'                 => new sfWidgetFormFilterInput(),
      'FAX'                      => new sfWidgetFormFilterInput(),
      'EMAIL'                    => new sfWidgetFormFilterInput(),
      'PREFIJO'                  => new sfWidgetFormFilterInput(),
      'ES_PUBLICO'               => new sfWidgetFormFilterInput(),
      'NIT'                      => new sfWidgetFormFilterInput(),
      'BANCO_ID'                 => new sfWidgetFormFilterInput(),
      'BANCO_ABREVIATURA'        => new sfWidgetFormFilterInput(),
      'BANCO_CODIGO'             => new sfWidgetFormFilterInput(),
      'ES_BANCO'                 => new sfWidgetFormFilterInput(),
      'CONT_EDICION'             => new sfWidgetFormFilterInput(),
      'FECHA_MODIFICACION'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'DIRECTORIOEXTERNO_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DirectorioExterno', 'column' => 'DIRECTORIOEXTERNO_ID')),
      'USUARIO_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CIUDAD_ID'                => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'TIPOPETICIONARIO_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoPeticionario', 'column' => 'TIPOPETICIONARIO_ID')),
      'TIPOIDENTIFICACION_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoIdentificacion', 'column' => 'TIPOIDENTIFICACION_ID')),
      'NOMBRE'                   => new sfValidatorPass(array('required' => false)),
      'DIRECCION'                => new sfValidatorPass(array('required' => false)),
      'FUNCIONARIO'              => new sfValidatorPass(array('required' => false)),
      'CARGO'                    => new sfValidatorPass(array('required' => false)),
      'TELEFONO'                 => new sfValidatorPass(array('required' => false)),
      'FAX'                      => new sfValidatorPass(array('required' => false)),
      'EMAIL'                    => new sfValidatorPass(array('required' => false)),
      'PREFIJO'                  => new sfValidatorPass(array('required' => false)),
      'ES_PUBLICO'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'NIT'                      => new sfValidatorPass(array('required' => false)),
      'BANCO_ID'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'BANCO_ABREVIATURA'        => new sfValidatorPass(array('required' => false)),
      'BANCO_CODIGO'             => new sfValidatorPass(array('required' => false)),
      'ES_BANCO'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CONT_EDICION'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_MODIFICACION'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('directorio_externo_hist_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DirectorioExternoHist';
  }

  public function getFields()
  {
    return array(
      'DIRECTORIOEXTERNOHIST_ID' => 'Number',
      'DIRECTORIOEXTERNO_ID'     => 'ForeignKey',
      'USUARIO_ID'               => 'ForeignKey',
      'CIUDAD_ID'                => 'ForeignKey',
      'TIPOPETICIONARIO_ID'      => 'ForeignKey',
      'TIPOIDENTIFICACION_ID'    => 'ForeignKey',
      'NOMBRE'                   => 'Text',
      'DIRECCION'                => 'Text',
      'FUNCIONARIO'              => 'Text',
      'CARGO'                    => 'Text',
      'TELEFONO'                 => 'Text',
      'FAX'                      => 'Text',
      'EMAIL'                    => 'Text',
      'PREFIJO'                  => 'Text',
      'ES_PUBLICO'               => 'Number',
      'NIT'                      => 'Text',
      'BANCO_ID'                 => 'Number',
      'BANCO_ABREVIATURA'        => 'Text',
      'BANCO_CODIGO'             => 'Text',
      'ES_BANCO'                 => 'Number',
      'CONT_EDICION'             => 'Number',
      'FECHA_MODIFICACION'       => 'Date',
    );
  }
}
