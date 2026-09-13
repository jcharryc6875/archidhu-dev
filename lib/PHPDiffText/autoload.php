<?php
// C:\AppServ\www\php-diff-6.10.5\autoload.php

spl_autoload_register(function(string $class): void {
    // 1) Clases Jfcherng\Diff\*
    $p1 = 'Jfcherng\\Diff\\';
    if (strncmp($p1, $class, strlen($p1)) === 0) {
        $rel = substr($class, strlen($p1));                   // e.g. "Renderer\RendererConstant"
        $file = __DIR__ . '/src/' . str_replace('\\','/',$rel) . '.php';
    }
    // 2) Clases Jfcherng\Utility\*
    elseif (strncmp('Jfcherng\\Utility\\', $class, 17) === 0) {
        $rel = substr($class, 17);                             // e.g. "MbString"
        $file = __DIR__ . '/src/Utility/' . str_replace('\\','/',$rel) . '.php';
    }
    else {
        // No nos interesa otro namespace
        return;
    }

    if (is_file($file)) {
        require_once $file;
    } else {
        error_log("[Autoload] No encontrado: $file");
    }
});
