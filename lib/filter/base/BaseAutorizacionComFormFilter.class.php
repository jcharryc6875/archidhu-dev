<?php

/**
 * AutorizacionCom filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseAutorizacionComFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FECHA_INICIAL_AUT_COM' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_FINAL_AUT_COM'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'FECHA_INICIAL_AUT_COM' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_FINAL_AUT_COM'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('autorizacion_com_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AutorizacionCom';
  }

  public function getFields()
  {
    return array(
      'AUTORIZACIONCOM_ID'    => 'Number',
      'FECHA_INICIAL_AUT_COM' => 'Date',
      'FECHA_FINAL_AUT_COM'   => 'Date',
    );
  }
}
