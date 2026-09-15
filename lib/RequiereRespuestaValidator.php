<?php

/**
 * Regla compartida (Comunicaciones Internas y Externas Recibidas): al archivar/vincular
 * a expediente, el usuario debe indicar si la comunicacion requiere respuesta; si no
 * requiere, debe diligenciar una observacion valida (no vacia, no solo espacios/simbolos).
 */
class RequiereRespuestaValidator
{
    const MSG_DEBE_INDICAR = 'Debe indicar si la comunicación requiere respuesta.';
    const MSG_OBSERVACION_OBLIGATORIA = 'Debe diligenciar las observaciones cuando la comunicación no requiere respuesta.';

    /**
     * @param bool|null $requiereRespuesta valor ya guardado/enviado (null = aun no se eligio)
     * @param string|null $observacion     observacion asociada (solo obligatoria si $requiereRespuesta es false)
     *
     * @return string|null mensaje de error, o null si la combinacion es valida
     */
    public static function validar($requiereRespuesta, $observacion)
    {
        if ($requiereRespuesta === null) {
            return self::MSG_DEBE_INDICAR;
        }
        if (!$requiereRespuesta) {
            $obs = trim((string) $observacion);
            if ($obs === '' || !preg_match('/[\p{L}\p{N}]/u', $obs)) {
                return self::MSG_OBSERVACION_OBLIGATORIA;
            }
        }

        return null;
    }

    /**
     * La observacion de "no requiere respuesta" solo tiene sentido cuando $requiereRespuesta
     * es false: si el usuario marca "Si" tras haber tenido una observacion cargada (o el campo
     * oculto sigue trayendo el valor anterior desde el POST), se descarta para no dejar
     * observaciones huerfanas asociadas a una comunicacion que si requiere respuesta.
     *
     * @param bool|string|null $requiereRespuesta valor crudo tal como llega del request (checkbox_tag envia '1'/'0')
     * @param string|null      $observacion       valor crudo del textarea
     *
     * @return string|null observacion recortada, o null si no aplica o quedo vacia
     */
    public static function normalizarObservacion($requiereRespuesta, $observacion)
    {
        if ($requiereRespuesta) {
            return null;
        }

        $obs = trim((string) $observacion);

        return $obs !== '' ? $obs : null;
    }

    /**
     * Concatena usuario y fecha al texto de la observacion, para que quede legible
     * directamente en el campo (ademas de quedar en el audit log por el guardado normal).
     */
    public static function construirObservacionConTrazabilidad($observacion, Usuario $usuario)
    {
        $nombreUsuario = trim($usuario->getNombre().' '.$usuario->getApellido());

        return trim($observacion).' | Registrado por: '.$nombreUsuario.' el '.date('Y-m-d H:i:s');
    }
}
