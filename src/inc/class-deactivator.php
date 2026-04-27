<?php

namespace SediciWebmasterRole\Inc;
use SediciWebmasterRole\Admin;

class Deactivator {

    public static function remove_role_webmaster_individual_site() {
        
        $users = get_users(['role' => 'webmaster']);

        // Cambiar los usuarios con el rol 'webmaster' a 'editor'
        foreach ($users as $user) {
            $user->set_role('editor');
        }

        // Eliminar el rol
        remove_role('webmaster');
    }

    public static function remove_webmaster_role_multisite() {
        $blog_id_actual = get_current_blog_id();
        $sitios = get_sites();

        foreach ($sitios as $sitio) {
            switch_to_blog($sitio->blog_id);
            self::remove_role_webmaster_individual_site();
            restore_current_blog();
        }

        switch_to_blog($blog_id_actual);
    }

	public static function deactivate($network_wide) {
        if (is_multisite() && $network_wide) {
            self::remove_webmaster_role_multisite();
        } else {
            self::remove_role_webmaster_individual_site();
        }

    }
        
}
