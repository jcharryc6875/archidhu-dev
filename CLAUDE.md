# CLAUDE.md — Reglas del proyecto

Stack legado: **PHP 7.4 · Symfony 1.4 · Propel 1.7 · SQL Server 2019+ · Bootstrap 3**.
Este NO es Symfony 2+/Flex ni Propel 2. No sugieras ni introduzcas APIs de esas versiones.

## 1. Versiones y compatibilidad (no negociable)

- PHP **7.4** exacto. Prohibido: `match`, `enum`, `readonly`, named arguments, union types, `str_contains()`, constructor promotion, `?->`, atributos `#[...]`. Sí permitido: typed properties, arrow functions, `??=`, spread en arrays.
- Symfony **1.4** (fork mantenido `friendsofsymfony1/symfony1` o `lexpress/symfony1`, que es el que corre sobre PHP 7.4). Nada de `symfony/*` 2+, ni contenedor de servicios, ni Twig, ni Doctrine, ni atributos de rutas.
- Propel **1.7** vía `sfPropelORMPlugin`. Nada de Propel 2 (`\Propel\Runtime\...`).
- SQL Server **2019+** con driver **`pdo_sqlsrv`** (extensión oficial de Microsoft). DSN Propel: `sqlsrv:server=HOST,1433;Database=NOMBRE`. No usar `dblib`/FreeTDS ni el adaptador `mssql` salvo instrucción expresa.
- Bootstrap **3.4.x** + jQuery **1.12/3.x** + Bootstrap 3 JS. Nada de Bootstrap 4/5 (`.ml-*`, `.d-flex`, `data-bs-*`, `.form-control-sm` con `.input-group-sm` mezclados, etc.).

Antes de proponer cualquier función de PHP, comprueba que existe en 7.4. Antes de usar cualquier método de Propel, comprueba que existe en la 1.7 (Query API + Peer clásico).

## 2. Estructura de Symfony 1.4

```
apps/<app>/config/          app.yml, routing.yml, security.yml, settings.yml, view.yml
apps/<app>/modules/<mod>/actions/actions.class.php
apps/<app>/modules/<mod>/templates/<accion>Success.php, _parcial.php
apps/<app>/modules/<mod>/config/security.yml, view.yml, generator.yml
apps/<app>/lib/             helpers, filtros, myUser.class.php
apps/<app>/templates/layout.php
config/                     databases.yml, ProjectConfiguration.class.php, propel.ini
config/schema.yml           ← fuente de verdad del modelo (Propel)
lib/model/                  clases generadas + clases custom (Article, ArticleQuery, ArticlePeer)
lib/form/                   BaseXxxForm generados + XxxForm custom
lib/filter/                 filtros admin generator
lib/task/                   tareas `symfony` propias
plugins/                    sfPropelORMPlugin, sfGuardPlugin, etc.
web/                        index.php, frontend_dev.php, css/, js/, uploads/
```

- Un módulo = una carpeta. Acciones en `actions.class.php` como `public function executeNombre(sfWebRequest $request)`.
- Nunca edites clases `Base*` en `lib/model/om/`, `lib/model/map/`, `lib/form/base/` ni `lib/filter/base/`: se regeneran. Personaliza en la clase hija.
- Tras cambiar `schema.yml`: `php symfony propel:build --all-classes` (y `propel:build-sql` / `propel:insert-sql` o migraciones con `propel:generate-migration` + `propel:migrate`). Nunca escribas a mano DDL fuera de ese flujo salvo scripts explícitos.
- Limpiar caché tras tocar config/routing/plugins: `php symfony cc`.

## 3. Controladores (actions)

- Recupera parámetros con `$this->getRequestParameter('id')` o `$request->getParameter('id')`; nunca `$_GET`/`$_POST`/`$_REQUEST` directo.
- Cargar objetos: `$this->forward404Unless($obj = ArticlePeer::retrieveByPk('id'))`.
- Verifica método HTTP con `$request->isMethod(sfRequest::POST)` para operaciones que escriben.
- Redirecciones con `$this->redirect('modulo/accion?id=' . $obj->getId())` o rutas nombradas `$this->redirect('@ruta', array('id' => …))`.
- Flash: `$this->getUser()->setFlash('notice', 'Guardado.')` / `setFlash('error', …)`.
- Retorna `sfView::NONE` en respuestas JSON/AJAX y construye la salida con `$this->getResponse()->setContentType('application/json')` + `return $this->renderText(json_encode($data))`.
- Los `execute*` deben ser cortos: la lógica de negocio va a clases de `lib/` (servicios sencillos, `Peer` custom, métodos del modelo).
- Seguridad por módulo en `config/security.yml` (`is_secure`, `credentials`). No reinventes control de acceso;

## 4. Propel 1.7 y SQL Server

### Consultas
- Usa siempre la **Query API**: `ArticlePeer::doSelect()`.
- `retrieveByPk()`, `doCount()`.
- Prohibido `mysql_*`, `mssql_*`, `sqlsrv_query()` directos. SQL crudo solo con `Propel::getConnection()->prepare()` y parámetros enlazados.
- No uses `Criteria` a mano salvo para compatibilidad con código existente.
- N+1: en listados usa `->joinWith()` o `->with()` para cargar relaciones.
- Transacciones: `$con = Propel::getConnection(ArticlePeer::DATABASE_NAME); $con->beginTransaction(); try { … $con->commit(); } catch (Exception $e) { $con->rollBack(); throw $e; }`.

### Particularidades de SQL Server
- Paginación: Propel genera `OFFSET … FETCH`/`ROW_NUMBER()` según adaptador. **Toda consulta con `limit()`/`offset()` debe tener `orderBy()`**; sin orden, SQL Server devuelve resultados inestables.
- Tipos en `schema.yml`: `BOOLEAN`→`BIT`, `TIMESTAMP`→`DATETIME2`, `VARCHAR` con `size` obligatorio (sin size = `VARCHAR(1)` en algunas builds), `LONGVARCHAR`→`VARCHAR(MAX)`, `CLOB`→`NVARCHAR(MAX)`. Usa `NVARCHAR` para texto con acentos/ñ o configura la collation de la BD como `Latin1_General_100_CI_AI_SC_UTF8`.
- Identidad: `autoIncrement: true` → `IDENTITY(1,1)`. No insertes valores en columnas identity.
- Fechas: pasa `DateTime` o cadenas ISO `Y-m-d H:i:s`. Nunca `d/m/Y` a la BD. Ojo con `GETDATE()` vs zona horaria de PHP (`date_default_timezone_set` en `ProjectConfiguration`).
- Nombres reservados en SQL Server (`user`, `order`, `group`, `key`, `index`, `plan`): evítalos como nombre de tabla/columna; si ya existen, Propel los entrecomilla con `[ ]` solo si el adaptador lo hace — verifica el SQL generado con el `sfWebDebugToolbar` o `propel:build-sql`.
- `LIKE` es case-insensitive con collation `_CI_`; no uses `ILIKE` ni `LOWER()` innecesario.
- Sin `LIMIT` en SQL crudo: usa `TOP n` o `OFFSET … ROWS FETCH NEXT … ROWS ONLY`.
- Bloqueos: lecturas largas en reportes con `WITH (NOLOCK)` solo si se acepta lectura sucia y se documenta.
- `databases.yml` de ejemplo:
  ```yaml
  all:
    propel:
      class: sfPropelDatabase
      param:
        classname: DebugPDO      # PropelPDO en prod
        dsn: 'sqlsrv:server=%DB_HOST%,1433;Database=%DB_NAME%'
        username: %DB_USER%
        password: %DB_PASS%
        encoding: utf8
        persistent: false
        pooling: true
  ```
  Credenciales reales nunca en el repo; usa variables de entorno o un `databases.yml` ignorado por git.

## 5. Vistas y templates

- PHP plano con helpers, sin Twig. Escapa siempre: `output_escaping_strategy: true` con `ESC_SPECIALCHARS` en `settings.yml`; si está activado, usa `$sf_data->getRaw('var')` solo cuando sea HTML confiable.
- Helpers habituales: `use_helper('Date', 'Number', 'Text')`, `url_for('modulo/accion?id=' . $id)`, `link_to('Texto', '@ruta?id=' . $id, array('class' => 'btn btn-default'))`, `image_tag()`, `include_partial('modulo/parcial', array('x' => $x))`, `include_component()`, `format_date()`, `format_number()`.
- Slots para layout: `slot('title')` / `include_slot('title')`. Metadatos y assets en `view.yml`, `use_stylesheet()`, `use_javascript()` en el template.
- Los templates no contienen lógica de negocio ni consultas Propel; reciben datos ya cargados desde la acción.

## 6. Bootstrap 3

- Grid: `.container` / `.container-fluid` → `.row` → `.col-xs-* .col-sm-* .col-md-* .col-lg-*`. Nada de `.col` sin sufijo.
- Formularios: `.form-group` > `label` + `.form-control`; `.form-horizontal` con `.control-label col-sm-2` y `.col-sm-10`; `.help-block` para ayuda; `.has-error` en el `.form-group` para errores; `.checkbox`/`.radio` como wrappers.
- Botones: `.btn .btn-default|primary|success|info|warning|danger|link`, tamaños `.btn-lg|sm|xs`.
- Tablas: `.table .table-striped .table-bordered .table-hover .table-condensed`, envueltas en `.table-responsive`.
- Componentes: `.panel .panel-default > .panel-heading/.panel-body/.panel-footer`, `.alert .alert-*` con `.alert-dismissible` y `data-dismiss="alert"`, `.navbar .navbar-default`, `.modal` con `data-toggle="modal"` y `data-target`, iconos `.glyphicon .glyphicon-*` (o Font Awesome 4 si está incluido).
- Utilidades: `.pull-left/.pull-right`, `.text-center`, `.hidden-xs`, `.visible-md`, `.clearfix`, `.sr-only`. Nada de `.d-none`, `.ml-2`, `.float-right`.
- JS: `$('#modal').modal('show')`, `$('[data-toggle="tooltip"]').tooltip()`. Los plugins JS de Bootstrap 3 requieren jQuery cargado antes.
- Mantén los estilos custom en `web/css/app.css`; no inline salvo excepciones justificadas.

## 7. JavaScript / AJAX

- jQuery clásico, sin frameworks modernos ni build steps (sin Webpack/Vite/npm) a menos que el proyecto ya los tenga.
- Peticiones AJAX a acciones que devuelven JSON: `$.ajax({ url: '<?php echo url_for('modulo/accion') ?>', type: 'POST', data: {...}, dataType: 'json' })`. Incluye el token CSRF si la acción lo exige.
- Sintaxis JS compatible con navegadores objetivo del proyecto; si hay soporte para IE11, nada de `fetch`, `let/const` sin transpilar, arrow functions ni template literals.

## 8. Tareas, logs y depuración

- Tareas CLI en `lib/task/*Task.class.php` heredando de `sfBaseTask`; ejecuta con `php symfony namespace:nombre --env=prod`.
- Log: `$this->logMessage('texto', 'info')` en acciones o `sfContext::getInstance()->getLogger()->err(...)` en lib. Nunca `var_dump`/`print_r`/`die()` en código commiteado.
- Entorno dev: `web/frontend_dev.php` con `sfWebDebugToolbar` para ver SQL generado. En prod desactiva `web_debug` y `no_script_name: true` para la app principal.
- Errores 500: revisar `log/<app>_<env>.log` y `php -l` sobre el archivo tocado.

## 9. Estilo de código

- PSR-2/PSR-12 para código nuevo, respetando el estilo existente en el archivo tocado (Symfony 1.x usa llaves en línea nueva en clases/métodos y 2 espacios de indentación en el core; sigue lo que ya haya en el proyecto).
- `declare(strict_types=1)` solo en archivos nuevos de `lib/` que no interactúen con las clases base generadas (estas no están tipadas y pueden romper).
- Tipos de retorno y parámetros escalares donde no choque con la firma heredada de Symfony/Propel (no cambies firmas de `execute*`, `configure()`, `save()`, `preSave()`, etc.).
- Cadenas de texto visibles al usuario en español; nombres de clases, métodos y variables en inglés siguiendo la convención existente.
- Comentarios solo cuando explican un *por qué* no evidente (workaround de SQL Server, deuda técnica, comportamiento raro de Propel).
- Sin `@` para silenciar errores. Sin `eval`. Sin `extract()`.

## 10. Seguridad

- Salida escapada; HTML confiable solo vía `getRaw()` y documentado.
- Subidas de archivos con `sfValidatorFile` (`mime_types`, `max_size`) y guardado fuera de `web/` o con nombres aleatorios.
- Secretos fuera del repositorio. Revisa `.gitignore` antes de añadir archivos de config.
- No expongas `frontend_dev.php` ni `backend_dev.php` en producción.

## 13. Qué hacer antes de dar por terminado un cambio

1. `php -l` en cada archivo PHP modificado.
2. Si cambió `schema.yml`: `propel:build --all-classes` y migración/`build-sql` correspondiente; revisar el DDL para SQL Server.
3. `php symfony cc`.
4. Probar la vista en anchos `xs` y `md` si se tocó HTML/Bootstrap.
5. Confirmar que no se introdujo sintaxis de PHP ≥ 8.0, Propel 2, Symfony ≥ 2 ni Bootstrap ≥ 4.

## 14. Cuando no estés seguro

- Pregunta antes de: cambiar `schema.yml` en tablas con datos en producción, alterar rutas existentes, modificar `security.yml`, tocar el layout global o añadir dependencias (Composer/plugins).
- Si una funcionalidad requeriría una versión más nueva del stack, dilo explícitamente y propón la alternativa compatible en lugar de asumir la actualización.
- Nunca regeneres el modelo automaticamente, esta actividad la hace el desarrollador
