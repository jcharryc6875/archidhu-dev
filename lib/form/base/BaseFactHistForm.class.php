<?php

/**
 * FactHist form base class.
 *
 * @method FactHist getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFactHistForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTHIST_ID'        => new sfWidgetFormInputHidden(),
      'REGIONAL_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'NUMERO_FACT'        => new sfWidgetFormInputText(),
      'FECHA'              => new sfWidgetFormDateTime(),
      'RADICADO'           => new sfWidgetFormInputText(),
      'EMPRESA_ORIGEN'     => new sfWidgetFormInputText(),
      'CODIGO_BARRAS'      => new sfWidgetFormInputText(),
      'VALOR'              => new sfWidgetFormInputText(),
      'APROBADOR'          => new sfWidgetFormInputText(),
      'UEN'                => new sfWidgetFormInputText(),
      'UO'                 => new sfWidgetFormInputText(),
      'CUENTA_PUC'         => new sfWidgetFormInputText(),
      'CAUSADOR'           => new sfWidgetFormInputText(),
      'VALOR_CAUSADO'      => new sfWidgetFormInputText(),
      'BATCH_CAUSACION'    => new sfWidgetFormInputText(),
      'ID_CAUSACION'       => new sfWidgetFormInputText(),
      'FACTURA_CAUSACION'  => new sfWidgetFormInputText(),
      'LOTE_PAGO'          => new sfWidgetFormInputText(),
      'BATCH_TESORERIA'    => new sfWidgetFormInputText(),
      'PERIODO'            => new sfWidgetFormInputText(),
      'NOTA_CARTERA'       => new sfWidgetFormInputText(),
      'BATCH_CARTERA'      => new sfWidgetFormInputText(),
      'PAGADOR'            => new sfWidgetFormInputText(),
      'COD_BARRAS_GESTION' => new sfWidgetFormInputText(),
      'RUTA'               => new sfWidgetFormInputText(),
      'ESTADO'             => new sfWidgetFormInputText(),
      'ESTADO_DESC'        => new sfWidgetFormInputText(),
      'DESTINO'            => new sfWidgetFormInputText(),
      'SOPORTES'           => new sfWidgetFormInputText(),
      'OBS'                => new sfWidgetFormInputText(),
      'ASUNTO'             => new sfWidgetFormInputText(),
      'TEMPORAL1'          => new sfWidgetFormInputText(),
      'EMPORAL2'           => new sfWidgetFormInputText(),
      'TEMPORAL3'          => new sfWidgetFormInputText(),
      'EMPORAL4'           => new sfWidgetFormInputText(),
      'TEMPORAL5'          => new sfWidgetFormInputText(),
      'DEPENDENCIA_NOMBRE' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTHIST_ID'        => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacthistId()), 'empty_value' => $this->getObject()->getFacthistId(), 'required' => false)),
      'REGIONAL_ID'        => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'NUMERO_FACT'        => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'FECHA'              => new sfValidatorDateTime(array('required' => false)),
      'RADICADO'           => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'EMPRESA_ORIGEN'     => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'CODIGO_BARRAS'      => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'VALOR'              => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'APROBADOR'          => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'UEN'                => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'UO'                 => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'CUENTA_PUC'         => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'CAUSADOR'           => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'VALOR_CAUSADO'      => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'BATCH_CAUSACION'    => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'ID_CAUSACION'       => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'FACTURA_CAUSACION'  => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'LOTE_PAGO'          => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'BATCH_TESORERIA'    => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'PERIODO'            => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'NOTA_CARTERA'       => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'BATCH_CARTERA'      => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'PAGADOR'            => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'COD_BARRAS_GESTION' => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'RUTA'               => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'ESTADO'             => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'ESTADO_DESC'        => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESTINO'            => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'SOPORTES'           => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'OBS'                => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'ASUNTO'             => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'TEMPORAL1'          => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'EMPORAL2'           => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'TEMPORAL3'          => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'EMPORAL4'           => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'TEMPORAL5'          => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'DEPENDENCIA_NOMBRE' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_hist[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactHist';
  }


}
