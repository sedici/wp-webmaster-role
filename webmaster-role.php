<?php
/**
 * Plugin Name: WP Webmaster Role
 * Description: Crea un rol personalizado al activar el plugin. Compatible con multisite y single site.
 * Version: 1.0
 * Author: SEDICI
 * Author URI: http://sedici.unlp.edu.ar/
 * Text Domain:   
 * Copyright (c) 2015 SEDICI UNLP, http://sedici.unlp.edu.ar
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.	
*/

function activar_crear_rol_personalizado() {
    $crear_rol = function () {
        if (!get_role('mi_rol_personalizado')) {
            add_role('mi_rol_personalizado', 'Mi Rol Personalizado', [
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
                // Agregá más capacidades si querés
            ]);
        }
    };

    if (is_multisite()) {
        $blog_id_actual = get_current_blog_id();
        $sitios = get_sites();

        foreach ($sitios as $sitio) {
            switch_to_blog($sitio->blog_id);
            $crear_rol();
            restore_current_blog();
        }

        switch_to_blog($blog_id_actual);
    } else {
        $crear_rol();
    }
}

register_activation_hook(__FILE__, 'activar_crear_rol_personalizado');