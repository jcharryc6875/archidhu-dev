<?php

/**
 * Subclass for representing a row from the 'regional' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Regional extends BaseRegional
{
    function __toString(){		
      return $this->getDescripcion()." - ".$this->getEntidad()->getDescripcion();
    }
 
    public function getNombEntidad()
    {
      return $this->getEntidad()->getDescripcion();
    }        
    
    public function getRegionalAll()
    {
	    //return $this->getRegionalId().' - '.$this->getDescripcion();
      return $this->getDescripcion();
    }

}
