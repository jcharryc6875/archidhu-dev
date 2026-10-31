REM Reporte consolidado de fallas de envio de correo (programar 2 veces al dia)
cd C:\inetpub\wwwroot\sgdeapruebas

call php symfony notificacion:reporte_fallas --env=prod
