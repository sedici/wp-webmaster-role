<?php

namespace SediciWebmasterRole\Inc;
use SediciWebmasterRole\Admin;

/**
* Clase encargada de gestionar todo lo relacionado al rol webmaster.
*
*/
class RoleManager {

/*
    * Retorna un array con los permisos extra que se le van a asignar al rol Webmaster.
    *
    * @return Array
    */
    private static function get_extra_admin_capabilities() {
        return [
            'edit_theme_options' => true,
            'customize' => true,
            'create_personal' => true,
            'delete_others_personal' => true,
            'delete_others_personales' => true,
            'delete_personal' => true,
            'delete_private_personales' => true,
            'delete_published_personales' => true,
            'edit_others_personal' => true,
            'edit_other_personales' => true,
            'edit_personal' => true,
            'edit_personales' => true,
            'edit_other_personales' => true,
            'edit_private_personales' => true,
            'edit_published_personales' => true,
            'publish_personales' => true,
            'read_private_personales' => true,
            'read_personal' => true,
            'manage_options' => true,
        ];
    }

    /*
    * Si no esta creado, crea el rol Webmaster con las capacidades de Editor + capacidades extra de administrador y otros plugins.
    *
    * @return void
    */
    private static function create_rol() {
        if (!get_role('webmaster')) {
            $editor_role = get_role('editor');
            if ($editor_role) {
                $capabilities = array_merge(
                    $editor_role->capabilities,
                    self::get_extra_admin_capabilities(),
                );
                add_role('webmaster', 'Webmaster', $capabilities);
            }
        }
    }

    /*
    * Crea el rol Webmaster en todo el multisitio
    *
    * @return void
    */
    private static function create_rol_multisite() {
        self::run_on_all_sites([self::class, 'create_rol']);
    }

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
     * Setea el rol Webmaster a los usuarios con rol Editor en el sitio
     * @return void
     */
    private static function set_webmaster_role_to_editors() {

        $editores = get_users(['role' => 'editor']);  
        
        foreach ($editores as $usuario) {
            $usuario->set_role('webmaster');
        }
    }

    /**
     * Setea el rol Webmaster a todos los usuarios en un entorno multisitio.
     * @return void
     */
    public static function set_webmaster_role_multisite() {
        if( is_super_admin()) {
            self::run_on_all_sites([self::class, 'set_webmaster_role_to_editors']);
        }
        else {
            return;
        }
    }

    /**
     * Le quita el rol Webmaster a todos los usuarios en un entorno multisitio.
     * @return void
     */
    public static function remove_webmaster_role_multisite() {
        if( is_super_admin() ) {
            self::run_on_all_sites([self::class, 'set_editor_role_to_webmasters']);
        }
        else {
            return;
        }
    }


    /**
     * Ejecuta una función específica (callback) en todos los sitios de la red multisitio.
     *
     * @param callable $callback La función a ejecutar dentro del contexto de cada sitio.
     * @return void
     */
    private static function run_on_all_sites( callable $callback ) {
        if ( ! is_multisite() ) {
            return; // Seguridad: Si no es multisitio, salimos.
        }

        $blog_id_actual = get_current_blog_id();
        $sitios = get_sites();

        foreach ( $sitios as $sitio ) {
            switch_to_blog( $sitio->blog_id );
            
            // Ejecutamos la función que nos pasaron por parámetro
            call_user_func( $callback );
            
            restore_current_blog();
        }

        switch_to_blog( $blog_id_actual );
    }


    /*
    * Función de desactivación del plugin.
    * Le quita el rol Webmaster a todos los usuarios en un entorno multisitio, elimina el rol Webmaster y elimina el flag creado en la activación.
    *
    * @param string $network_wide  Si el plugin se está activando a nivel de red.
    * @return void
    */
	public static function deactivate($network_wide) {
        if (is_multisite() && $network_wide && is_super_admin()) {

            self::run_on_all_sites( function() {
                self::set_editor_role_to_webmasters();
                remove_role('webmaster');
            });
            
            delete_network_option(get_current_network_id(),'webmaster_role_switched_flag');
        }
    }

    /*
    * Función de activación del plugin. Chequea que se esté activando a nivel de red y que sea una instalación Multisitio. 
    * Crea el rol Webmaster y un flag
    *
    * @param string $network_wide  Si el plugin se está activando a nivel de red.
    * @return void
    */
	public static function activate($network_wide) {
        if ( ! is_multisite() ) {
            deactivate_plugins( plugin_basename(SEDICI_WEBMASTER_PLUGIN_DIR . 'webmaster-role.php') );
            wp_die( 'Este plugin requiere una instalación Multisitio para funcionar.' );
        }

        if ( ! $network_wide ) {
            // Si el usuario intentó activarlo solo en un sitio individual:
            deactivate_plugins( plugin_basename(SEDICI_WEBMASTER_PLUGIN_DIR . 'webmaster-role.php') );
            wp_die( 
                '<strong>WP Webmaster Role:</strong> Este plugin solo puede ser activado a nivel de red (Network Activate). 
                Por favor, ve al Administrador de la Red para activarlo.' 
            );
        }

        if( is_super_admin() ) {
            self::create_rol_multisite();
            // Crea un flag para saber si el rol Webmaster fue seteado a los usuarios o no.
            add_network_option(get_current_network_id(),'webmaster_role_switched_flag', 0);
        }
    }


}

