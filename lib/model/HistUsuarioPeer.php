<?php

/**
 * Subclass for performing query and update operations on the 'HIST_USUARIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class HistUsuarioPeer extends BaseHistUsuarioPeer
{
    public static function addNewHistUser($info = array(),$usuario_modifica,$usuario_modificado)
    {
        //guarda en historico
        $hist = new HistUsuario();
        $hist->fromArray($info);
        $hist->setFechaModificacion(date('Y-m-d G:i:s'));
        $hist->save();
        //*********************************************************************************************
        $hist_id = $hist->getHistusuarioId();
        $rolhist = new UsUsHistorico();
        $rolhist->setUsuarioId($usuario_modifica);
        $rolhist->setRolushistoricoid(1);
        $rolhist->setHistusuarioId($hist_id);
        $rolhist->save();
        //*********************************************************************************************
        $rolhist = new UsUsHistorico();
        $rolhist->setUsuarioId($usuario_modificado);
        $rolhist->setRolushistoricoid(2);
        $rolhist->setHistusuarioId($hist_id);
        $rolhist->save();
    }
}
