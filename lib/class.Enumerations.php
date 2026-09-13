<?php

abstract class BasicEnum {
    private static $constCacheArray = NULL;

    private static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    public static function getAll() 
    {
        return self::getConstants();
    }

    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value, $strict = true) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }

    public static function getEnumName($value) {
        $constants = self::getConstants();
        $constName = null;
        foreach ( $constants as $name => $item )
        {
            if ( $value == $item )
            {
                $constName = $name;
                break;
            }
        }

        return $constName;
    }
}

abstract class RolComTypeNotify extends BasicEnum {
    const FirmaDocsEnviada = 0;
    const CheckDocsEnviada = 1;
    const RevisaDocsEnviada = 2;
    const FirmaDocsInterna = 3;
    const CheckDocsInterna = 4;
    const TrustInicioSesionday = 5;
    const CambioPassword = 6;
    const RecuperacionPassword = 7;
}

abstract class ModuleEnableNotify extends BasicEnum {
    const Seguridad = 1;
    const ComInterna = 2;
    const ComRecibida = 3;
	const ComEnviada = 4;
    const Archivo = 5;
    const Servicios = 7;
	const ActosAdministrativos = 17;
    const Interesados = 18;    
    const Facturas = 13;
    const CorreoElectronico = 19;
    const CarteleraInformativa = 20;
}

abstract class AppApiExternal extends BasicEnum {
    const PQRSD = "PQRSD";
    const FIRMA_GSE = "FIRMA_GSE";
    const FIRMA_ANDES = "FIRMA_ANDES";
	const FACHADA_UARIV = "FACHADA_UARIV";
    const SIPOS = "SIPOS";
    const SISCLINET = "SISCLINET";
}

abstract class ModulesEnable extends BasicEnum {
    const Seguridad = 1;
    const ComInterna = 2;
    const ComRecibida = 3;
	const ComEnviada = 4;
    const Archivo = 5;
    const Servicios = 7;
    const ActosAdministrativos = 17;
    const Interesados = 18;
    const Facturas = 13;
    const CorreoElectronico = 19;
    const CarteleraInformativa = 20;
}

abstract class ModulesDenomination extends BasicEnum 
{
    const ComInterna = 'comunicacion interna';
    const ComRecibida = 'comunicacion recibida';
	const ComEnviada = 'comunicacion enviada';
    const Archivo = 'unidad documental';
    const Servicios = 'servicios';
}

abstract class ModulesExpedienteAutomatize extends BasicEnum {
    const ComInterna = 2;
    const ComRecibida = 3;
	const ComEnviada = 4;
    const ActosAdministrativos = 17;
}

abstract class SignDigitalOptions extends BasicEnum {
    const None = 0;
	const OnlySign = 1;
    const SingAndStamp = 2;
    const OnlyStamp = 3;
}

abstract class EmailStatus extends BasicEnum {
    const Descargado = 1;
	const Radicado = 2;
    const Anulado = 3;
    const ErrorProceso = 4;
    const Asignado = 5;
}

abstract class UserAuthType extends BasicEnum {
    const Nativa = 1;
	const FederadaSSO = 2;
    const OAuth2 = 3;
    const LdapNativo = 4;
    const OTP = 5;
}

abstract class StatusDocsVersion extends BasicEnum {
    const Actual = "Actual";
	const Borrador = "Borrador";
    const Archivada = "Archivada";
    const None = "Ninguna";
}

abstract class FasesArchivo extends BasicEnum {
    const Gestion = 1;
	const Central = 2;
    const Historico = 3;
}

abstract class EmailTypeList extends BasicEnum {
    const Asignado = 1;
	const NoAsignado = 2;
    const None = 3;
}

abstract class EmailTypeAuth extends BasicEnum {
    const Password = 1;
	const OAuth = 2;
}

abstract class EmailProviders extends BasicEnum {
    const Imap = 1;
	const Outlook = 2;
	const Gmail = 3;
}

abstract class StatusPublicacion extends BasicEnum {
    const Publicado = 1;
	const Despublicado = 2;
}

abstract class OficinaVirtualProcess extends BasicEnum {
    const Radicacion = 1;
    const Distribucion = 2;
    const Gestion = 3;
	const Respuesta = 4;
    const Notificacion = 5;
}

abstract class ServicioAppExterna extends BasicEnum {
    const CertiMail = "CertiMail";
    const CorreoFisicoNacional = "CorreoFisicoNacional";
}

abstract class CourrierEnable extends BasicEnum {
    const ServiciosPostales = 6;
	const None = null;
}

abstract class SrvMensajeriaStatus extends BasicEnum {
    const Ejecutada = 3;
	const EjecutadoDevuelto = 9;
	const Devuelto = 7;
}

abstract class TipoAutomatizacionUnidadDoc extends BasicEnum {
    const Automatico = 1;
	const Segerencia = 2;
}

abstract class OrigenTransferenciaCom extends BasicEnum {
    const ComInterna = 1;
	const ComRecibida = 2;
	const ComEnviada = 3;
	const ActosAdministrativos = 8;
}

abstract class ComTipoProceso extends BasicEnum {
    const Radicador = 1;
	const Distribuidor = 2;
	const Gestor = 3;
	const Revisor = 4;
	const Firmante = 5;
	const ControlCalidad = 6;
}

abstract class ResponseDocTypeCom extends BasicEnum {
    const Pdf = 1;
	const Word = 2;
	const None = 0;
}

abstract class TaskInstanceStatusBpmn extends BasicEnum
{
    const pending = 1;
    const assigned = 2;
    const in_progress = 3;
    const completed = 4;
    const failed = 5;
    const skipped = 6;
    const cancelled = 7;
    const rejected = 8;
}

abstract class WorkflowInstanceStatusBpmn extends BasicEnum
{
    const running = 1;
    const completed = 2;
    const suspended = 3;
    const failed = 4;
    const terminated = 5;
}

abstract class CertiEmailProviders extends BasicEnum
{
    const Andes = 'Andes';
    const RMail = 'RMail';
}