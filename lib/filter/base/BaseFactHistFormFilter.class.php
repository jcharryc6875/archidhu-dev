<?php

/**
 * FactHist filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFactHistFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'REGIONAL_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'NUMERO_FACT'        => new sfWidgetFormFilterInput(),
      'FECHA'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'RADICADO'           => new sfWidgetFormFilterInput(),
      'EMPRESA_ORIGEN'     => new sfWidgetFormFilterInput(),
      'CODIGO_BARRAS'      => new sfWidgetFormFilterInput(),
      'VALOR'              => new sfWidgetFormFilterInput(),
      'APROBADOR'          => new sfWidgetFormFilterInput(),
      'UEN'                => new sfWidgetFormFilterInput(),
      'UO'                 => new sfWidgetFormFilterInput(),
      'CUENTA_PUC'         => new sfWidgetFormFilterInput(),
      'CAUSADOR'           => new sfWidgetFormFilterInput(),
      'VALOR_CAUSADO'      => new sfWidgetFormFilterInput(),
      'BATCH_CAUSACION'    => new sfWidgetFormFilterInput(),
      'ID_CAUSACION'       => new sfWidgetFormFilterInput(),
      'FACTURA_CAUSACION'  => new sfWidgetFormFilterInput(),
      'LOTE_PAGO'          => new sfWidgetFormFilterInput(),
      'BATCH_TESORERIA'    => new sfWidgetFormFilterInput(),
      'PERIODO'            => new sfWidgetFormFilterInput(),
      'NOTA_CARTERA'       => new sfWidgetFormFilterInput(),
      'BATCH_CARTERA'      => new sfWidgetFormFilterInput(),
      'PAGADOR'            => new sfWidgetFormFilterInput(),
      'COD_BARRAS_GESTION' => new sfWidgetFormFilterInput(),
      'RUTA'               => new sfWidgetFormFilterInput(),
      'ESTADO'             => new sfWidgetFormFilterInput(),
      'ESTADO_DESC'        => new sfWidgetFormFilterInput(),
      'DESTINO'            => new sfWidgetFormFilterInput(),
      'SOPORTES'           => new sfWidgetFormFilterInput(),
      'OBS'                => new sfWidgetFormFilterInput(),
      'ASUNTO'             => new sfWidgetFormFilterInput(),
      'TEMPORAL1'          => new sfWidgetFormFilterInput(),
      'EMPORAL2'           => new sfWidgetFormFilterInput(),
      'TEMPORAL3'          => new sfWidgetFormFilterInput(),
      'EMPORAL4'           => new sfWidgetFormFilterInput(),
      'TEMPORAL5'          => new sfWidgetFormFilterInput(),
      'DEPENDENCIA_NOMBRE' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'REGIONAL_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'NUMERO_FACT'        => new sfValidatorPass(array('required' => false)),
      'FECHA'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'RADICADO'           => new sfValidatorPass(array('required' => false)),
      'EMPRESA_ORIGEN'     => new sfValidatorPass(array('required' => false)),
      'CODIGO_BARRAS'      => new sfValidatorPass(array('required' => false)),
      'VALOR'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'APROBADOR'          => new sfValidatorPass(array('required' => false)),
      'UEN'                => new sfValidatorPass(array('required' => false)),
      'UO'                 => new sfValidatorPass(array('required' => false)),
      'CUENTA_PUC'         => new sfValidatorPass(array('required' => false)),
      'CAUSADOR'           => new sfValidatorPass(array('required' => false)),
      'VALOR_CAUSADO'      => new sfValidatorPass(array('required' => false)),
      'BATCH_CAUSACION'    => new sfValidatorPass(array('required' => false)),
      'ID_CAUSACION'       => new sfValidatorPass(array('required' => false)),
      'FACTURA_CAUSACION'  => new sfValidatorPass(array('required' => false)),
      'LOTE_PAGO'          => new sfValidatorPass(array('required' => false)),
      'BATCH_TESORERIA'    => new sfValidatorPass(array('required' => false)),
      'PERIODO'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'NOTA_CARTERA'       => new sfValidatorPass(array('required' => false)),
      'BATCH_CARTERA'      => new sfValidatorPass(array('required' => false)),
      'PAGADOR'            => new sfValidatorPass(array('required' => false)),
      'COD_BARRAS_GESTION' => new sfValidatorPass(array('required' => false)),
      'RUTA'               => new sfValidatorPass(array('required' => false)),
      'ESTADO'             => new sfValidatorPass(array('required' => false)),
      'ESTADO_DESC'        => new sfValidatorPass(array('required' => false)),
      'DESTINO'            => new sfValidatorPass(array('required' => false)),
      'SOPORTES'           => new sfValidatorPass(array('required' => false)),
      'OBS'                => new sfValidatorPass(array('required' => false)),
      'ASUNTO'             => new sfValidatorPass(array('required' => false)),
      'TEMPORAL1'          => new sfValidatorPass(array('required' => false)),
      'EMPORAL2'           => new sfValidatorPass(array('required' => false)),
      'TEMPORAL3'          => new sfValidatorPass(array('required' => false)),
      'EMPORAL4'           => new sfValidatorPass(array('required' => false)),
      'TEMPORAL5'          => new sfValidatorPass(array('required' => false)),
      'DEPENDENCIA_NOMBRE' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_hist_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactHist';
  }

  public function getFields()
  {
    return array(
      'FACTHIST_ID'        => 'Number',
      'REGIONAL_ID'        => 'ForeignKey',
      'NUMERO_FACT'        => 'Text',
      'FECHA'              => 'Date',
      'RADICADO'           => 'Text',
      'EMPRESA_ORIGEN'     => 'Text',
      'CODIGO_BARRAS'      => 'Text',
      'VALOR'              => 'Number',
      'APROBADOR'          => 'Text',
      'UEN'                => 'Text',
      'UO'                 => 'Text',
      'CUENTA_PUC'         => 'Text',
      'CAUSADOR'           => 'Text',
      'VALOR_CAUSADO'      => 'Text',
      'BATCH_CAUSACION'    => 'Text',
      'ID_CAUSACION'       => 'Text',
      'FACTURA_CAUSACION'  => 'Text',
      'LOTE_PAGO'          => 'Text',
      'BATCH_TESORERIA'    => 'Text',
      'PERIODO'            => 'Number',
      'NOTA_CARTERA'       => 'Text',
      'BATCH_CARTERA'      => 'Text',
      'PAGADOR'            => 'Text',
      'COD_BARRAS_GESTION' => 'Text',
      'RUTA'               => 'Text',
      'ESTADO'             => 'Text',
      'ESTADO_DESC'        => 'Text',
      'DESTINO'            => 'Text',
      'SOPORTES'           => 'Text',
      'OBS'                => 'Text',
      'ASUNTO'             => 'Text',
      'TEMPORAL1'          => 'Text',
      'EMPORAL2'           => 'Text',
      'TEMPORAL3'          => 'Text',
      'EMPORAL4'           => 'Text',
      'TEMPORAL5'          => 'Text',
      'DEPENDENCIA_NOMBRE' => 'Text',
    );
  }
}
