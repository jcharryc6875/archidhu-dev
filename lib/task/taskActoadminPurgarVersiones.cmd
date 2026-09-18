REM Navegar al directorio del proyecto Symfony
cd C:\inetpub\wwwroot\sgdeapruebas

REM Ejecutar la tarea de Symfony (UARIV-202605 - depuracion de versiones preliminares)
call php symfony actoadmin:purgar-versiones --env=prod
