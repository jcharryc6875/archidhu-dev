<?php

/**
 * Filtro para validar sesiones únicas en Symfony 1.4
 */
class UniqueSessionFilter extends sfFilter
{
    /**
     * Ejecuta el filtro
     * 
     * @param sfFilterChain $filterChain
     */

    public function execute($filterChain)
    {
        // Código que se ejecuta ANTES de la acción
        $this->preExecute();
        //************************************************************************************
        // Obtener contexto
        $context = $this->getContext();
        $request = sfContext::getInstance()->getRequest();
        $isAuthenticated = sfContext::getInstance()->getUser()->isAuthenticated();
        //************************************************************************************
        // Rutas que no requieren validación de sesión
        $excludedRoutes = array(
            '/security/login',
            '/security/logout',
            '/usuario/digitStatus',
            '/com_recibida/imageThumbData',
            '/usuario/cambioPassword',
            '/usuario/updateCambioPassword',
            'sf_guard_signout',
            '/actualizaPassword.php',
			'/no_autorizado.html',
			'/no_autorizado_cerrar.html',
			'/no_file_exists.html',
            '/inicio/errorFile',
            '/inicio/errorToken',
            '/inicio/defaultPermCerrar',
            '/inicio/defaultPerm',
            'homepage',
            'api',
			'/usuario/heartbeat'
        );
        //************************************************************************************
        // Obtener la ruta actual
        $currentRoute = $request->getPathInfo();
        //************************************************************************************
        // Si la ruta está excluida, continuar sin validar
        if (in_array($currentRoute, $excludedRoutes)) {
            $filterChain->execute();
            return;
        }elseif(in_array($_SERVER['PHP_SELF'],$excludedRoutes)){
            $filterChain->execute();
            return;
        }
        //************************************************************************************
        // Si el usuario no está autenticado, continuar
        if(!$isAuthenticated)
        {
            //$filterChain->execute();
			//return;
			
			// Redireccionar al login
            $context->getController()->redirect('/backend.php/security/login');
            return;
        }
        //************************************************************************************
        // Validar sesión única
        if (!SessionManager::validateCurrentSession()) 
        {
            SessionManager::terminateCurrentSession();
            // Redireccionar al login
            $context->getController()->redirect('/backend.php/security/login');
            return;
        }
        //************************************************************************************
        // Si todo está bien, continuar con la ejecución
        $filterChain->execute();
        //************************************************************************************
        // Código que se ejecuta DESPUÉS de la acción
        $this->postExecute();
    }

    protected function preExecute()
    {
        // Tu lógica antes de la ejecución
        $user = $this->getContext()->getUser();
        $request = $this->getContext()->getRequest();

        // Ejemplo: Headers de seguridad
        //$response = $this->getContext()->getResponse();
        //$response->setHttpHeader('X-Frame-Options', 'DENY');
        //$response->setHttpHeader('X-Content-Type-Options', 'nosniff');
    }

    protected function postExecute()
    {
        // Tu lógica después de la ejecución
        $response = $this->getContext()->getResponse();

    }
}