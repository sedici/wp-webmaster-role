<?php

namespace SediciWebmasterRole\Inc;
use SediciWebmasterRole\Admin;

class Deactivator {

    /**
     * Setea a los usuarios con el rol Webmaster el rol Editor.
     * @return void
     */
    private static function set_editor_role_to_webmasters() {
        
        $users = get_users(['role' => 'webmaster']);

        // Cambiar los usuarios con el rol 'webmaster' a 'editor'
        foreach ($users as $user) {
            $user->set_role('editor');
        }
    }

    /**
     * Le quita el rol Webmaster a todos los usuarios en un entorno multisitio.
     * @return void
     */
    public static function remove_webmaster_role_multisite() {
        $blog_id_actual = get_current_blog_id();
        $sitios = get_sites();

        foreach ($sitios as $sitio) {
            switch_to_blog($sitio->blog_id);
            self::set_editor_role_to_webmasters();
            remove_role('webmaster');
            restore_current_blog();
        }

        switch_to_blog($blog_id_actual);
    }

    /*
    * Función de desactivación del plugin.
    * Le quita el rol Webmaster a todos los usuarios en un entorno multisitio, elimina el rol Webmaster y elimina el flag creado en la activación.
    *
    * @param string $network_wide  Si el plugin se está activando a nivel de red.
    * @return void
    */
	public static function deactivate($network_wide) {
        if (is_multisite() && $network_wide) {
            self::remove_webmaster_role_multisite();
            delete_network_option(get_current_network_id(),'webmaster_role_switched_flag');
        }
    }
        
}
