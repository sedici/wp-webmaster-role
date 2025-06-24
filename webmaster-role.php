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

// Capabilities de administrador que se agregarán al nuevo rol
function get_extra_admin_capabilities() {
    return [
        'edit_theme_options' => true,
        'customize' => true,
    ];
}

function activar_crear_rol_personalizado() {
    $crear_rol = function () {
        if (!get_role('webmaster')) {
            $editor_role = get_role('editor');
            if ($editor_role) {
                $capabilities = array_merge(
                    $editor_role->capabilities,
                    get_extra_admin_capabilities()
                );
                add_role('webmaster', 'Webmaster', $capabilities);
            }
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

// Al desactivar el plugin
function remove_webmaster_role() {
    $remover_rol = function() {
        $users = get_users(['role' => 'webmaster']);

        // Cambiar los usuarios con el rol 'webmaster' a 'editor'
        foreach ($users as $user) {
            $user->set_role('editor');
        }

        // Eliminar el rol
        remove_role('webmaster');
    };

    if (is_multisite()) {
        $blog_id_actual = get_current_blog_id();
        $sitios = get_sites();

        foreach ($sitios as $sitio) {
            switch_to_blog($sitio->blog_id);
            $remover_rol();
            restore_current_blog();
        }

        switch_to_blog($blog_id_actual);
    } else {
        $remover_rol();
    }
}

register_deactivation_hook(__FILE__, 'remove_webmaster_role');