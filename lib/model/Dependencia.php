<?php

/**
 * Subclass for representing a row from the 'dependencia' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Dependencia extends BaseDependencia
{
	public function __toString(){
		return $this->getNombre().' - '.$this->getCodigo().' - '.$this->getEntidad(). ' - '.$this->getTipoTabla(). ' - ' .$this->getVersionInst();
	}
    
	public function getNombEntidad(){
      return $this->getEntidad()->getDescripcion();
    }

	public function getNombreCustom(){
		return $this->getCodigo(). ' - ' .$this->getNombre(). ' ' .$this->getVersionInst();
	}
	
	public function getNombreCustomFull(){
		$dependencia_nombre = trim($this->getNombre());
		$nombre_compuesto = (trim($this->getCodigo()) ? trim($this->getCodigo())." - ".$dependencia_nombre." - " : $dependencia_nombre." - ").$this->getTipoTabla()." - ".$this->getEntidad();
		$nombre_compuesto .=  ' - ' .$this->getVersionInst();
		return $nombre_compuesto;
	}
}
