<?php

namespace SediciWebmasterRole\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Admin {

    private static $instance = null;

    public static function get_instance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run() {

        if (is_multisite()) {
            add_action('network_admin_menu', array($this, 'add_plugin_admin_menu'));
        }
        else add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

        //add_action('admin_init', array($this, 'create_and_set_webmaster_role_multisite'));
        //add_action('admin_init', array($this, 'create_and_set_webmaster_role_individual_site'));
    }

    public function add_plugin_admin_menu() {
        add_submenu_page(
            'users.php',                 // Slug del menú padre (Usuarios)
            'Webmaster Role',            // Título de la página
            'Webmaster Role',            // Título del menú
            'promote_users',            // Capacidad requerida (Solo Administradores)
            'webmaster-role-switcher',   // Slug de la página
            array($this, 'display_switcher_form') // Función que renderiza la vista
        );
    }

    public function display_switcher_form() {
        include_once dirname(__DIR__) . '/admin/views/role-switcher-form.php';
    }

    private function create_and_set_webmaster_role_multisite() {

        if (is_multisite()) {
            $blog_id_actual = get_current_blog_id();
            $sitios = get_sites();

            foreach ($sitios as $sitio) {
                switch_to_blog($sitio->blog_id);
                create_rol();
                set_webmaster_role_to_editors();
                restore_current_blog();
            }

            switch_to_blog($blog_id_actual);
        }
    }

    private function create_and_set_webmaster_role_individual_site() {
        create_rol();
        set_webmaster_role_to_editors();
    }
   

    private function set_webmaster_role_to_editors() {

        $editores = get_users(['role' => 'editor']);  
        
        foreach ($editores as $usuario) {
            $usuario->set_role('webmaster');
        }
    }

    


}