<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
    }

    /**
     * Webmaster plugin Autoloader (PSR-4).
     * * Mapea el namespace WebmasterRole a la carpeta /src/
     */

    spl_autoload_register(function ($class) {
        
        // Prefijo del namespace del plugin
        $prefix = 'SediciWebmasterRole\\';
        // Carpeta base donde están las clases
        $base_dir = SEDICI_WEBMASTER_PLUGIN_DIR . 'src/';

        $len = strlen($prefix);

        // ¿La clase utiliza nuestro prefijo?
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        // Obtener el nombre de la clase relativo al prefijo
        $relative_class = substr($class, $len);

        // Separa el namespace en partes usando \ como separador
        $parts = explode('\\', $relative_class);

        // Toma el nombre de la clase (última parte)
        $class_name = array_pop($parts); 
        // Convierte el resto del namespace en una ruta de carpetas (en minúsculas)
        $sub_path = strtolower(implode(DIRECTORY_SEPARATOR, $parts));

        // 1. Insertamos un guion antes de cada mayúscula (excepto la primera)
        $kebab_class_name = preg_replace('/(?<!^)[A-Z]/', '-$0', $class_name);
        // 2. Pasamos todo a minúsculas y cambiamos guiones bajos por medios
        $clean_class_name = strtolower(str_replace('_', '-', $kebab_class_name));

        // 3. Construimos el nombre del archivo siguiendo el formato class-{nombre-clase}.php
        $file_name = 'class-' . $clean_class_name . '.php';

        $full_path = $base_dir . ( !empty($sub_path) ? $sub_path . DIRECTORY_SEPARATOR : '' ) . $file_name;
    
        // Si el archivo existe, cargarlo
        if (file_exists($full_path)) {
            require_once $full_path;
        }
    });

?>
