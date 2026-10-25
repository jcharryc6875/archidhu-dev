<?php
/**
 * Compatibilidad symfony 1.3 -> 1.5: sfWidgetFormPropelSelect se eliminó en 1.4.
 * En 1.3 era un alias de sfWidgetFormPropelChoice. Lo usan los formularios
 * generados en lib/form/base.
 */
class sfWidgetFormPropelSelect extends sfWidgetFormPropelChoice
{
}