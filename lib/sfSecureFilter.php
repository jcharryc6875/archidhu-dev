<?php

// apps/frontend/lib/sfSecureFilter.class.php
// or for all apps
// lib/sfSecureFilter.class.php

class sfSecureFilter extends sfFilter
{
    /**
     * Implementa un redireccionamiento global de la versión 'http' a la 'https'.
     * 
     * @param type $filterChain
     * @return type
     */
    public function execute($filterChain)
    {
        $context = $this->getContext();
        $request = $context->getRequest();

        if (!$request->isSecure())
        {
            $secure_url = str_replace('http', 'https', $request->getUri());

            return $context->getController()->redirect($secure_url);
        }
        else
        {
            $filterChain->execute();
        }
    }
}