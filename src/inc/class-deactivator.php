<?php

namespace SediciWebmasterRole\Inc;
use SediciWebmasterRole\Admin;

class Deactivator {

    public static function set_editor_role_to_webmasters() {
        
        $users = get_users(['role' => 'webmaster']);

        // Cambiar los usuarios con el rol 'webmaster' a 'editor'
        foreach ($users as $user) {
            $user->set_role('editor');
        }
    }

    public static function remove_webmaster_role_multisite() {
        $blog_id_actual = get_current_blog_id();
        $sitios = get_sites();

        foreach ($sitios as $sitio) {
            switch_to_blog($sitio->blog_id);
            self::set_editor_role_to_webmasters();
            restore_current_blog();
        }

        switch_to_blog($blog_id_actual);
    }

	public static function deactivate($network_wide) {
        if (is_multisite() && $network_wide) {
            self::remove_webmaster_role_multisite();
            remove_role('webmaster');
            delete_site_option('webmaster_role_switched_flag');
        }
    }
        
}
