<?php

/**
 * Subclass for performing query and update operations on the 'PARAMETRO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ParametroPeer extends BaseParametroPeer
{
    public static function getNewConsecutivo()
    {
        $cons = ParametroPeer::retrieveByPk(8);
        $regId = $cons->getValorNumerico();
        if($regId > 0){
            $cons->setValorNumerico($regId+1);
            $cons->save();
        }
        return $regId;		
    }
}
