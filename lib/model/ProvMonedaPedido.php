<?php

/**
 * Subclass for representing a row from the 'PROV_MONEDA_PEDIDO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ProvMonedaPedido extends BaseProvMonedaPedido
{
	public function __toString(){
		return $this->getCodigo()." - ".$this->getNombre();
	}
}
