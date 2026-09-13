<?php

/**
 * Subclass for representing a row from the 'WF_INSTANCIA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfInstancia extends BaseWfInstancia
{
    public static function  createPath($cadena){	
    	return simad_util::createPath($cadena);
	}//function createPath
}
