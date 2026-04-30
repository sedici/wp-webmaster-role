<?php

namespace SediciWebmasterRole\Admin;
use SediciWebmasterRole\Inc\Deactivator;

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

    /**
     * Registra las funciones en los hooks que utilizará el plugin.
     * @return void
     */
    public function run() {

        if (is_multisite()) add_action('network_admin_menu', array($this, 'add_plugin_admin_menu'));

        add_action( 'admin_post_switch_roles_webmaster_plugin', [ $this, 'switch_roles' ] );
        


        //add_action('admin_init', array($this, 'create_and_set_webmaster_role_multisite'));
        //add_action('admin_init', array($this, 'create_and_set_webmaster_role_individual_site'));
    }

    /**
     * Agregar el menú del plugin en el escritorio de wordpress.
     * @return void
     */
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

    /**
     * Incluye el formulario para cambiar los roles.
     * @return void
     */
    public function display_switcher_form() {
        include_once dirname(__DIR__) . '/admin/views/role-switcher-form.php';
    }

    /**
     * Setea el rol Webmaster a todos los usuarios en un entorno multisitio.
     * @return void
     */
    private function set_webmaster_role_multisite() {

        if (is_multisite()) {
            $blog_id_actual = get_current_blog_id();
            $sitios = get_sites();

            foreach ($sitios as $sitio) {
                switch_to_blog($sitio->blog_id);
                $this->set_webmaster_role_to_editors();
                restore_current_blog();
            }

            switch_to_blog($blog_id_actual);
        }
    }

    /**
     * Setea el rol Webmaster a los usuarios con rol Editor en el sitio
     * @return void
     */
    private function set_webmaster_role_to_editors() {

        $editores = get_users(['role' => 'editor']);  
        
        foreach ($editores as $usuario) {
            $usuario->set_role('webmaster');
        }
    }

    /**
     * Método para procesar el cambio de roles solicitado por el usuario.
     * @return void
     */
    public function switch_roles() {
        if ( ! isset( $_POST['webmaster_role_nonce'] ) || ! wp_verify_nonce( $_POST['webmaster_role_nonce'], 'webmaster_switch_action' ) ) {
            wp_die( 'Nonce no válido. Por favor, inténtalo de nuevo.' );
        }

        $change_request = isset($_POST['webmaster_role_flag']) ? $_POST['webmaster_role_flag'] : 0;

        $flag = get_network_option(get_current_network_id(), 'webmaster_role_switched_flag') == 1;

        if( ($change_request == 1) && ($flag == 0) ) {
            $this->set_webmaster_role_multisite();
            update_network_option(get_current_network_id(), 'webmaster_role_switched_flag', 1);
        } elseif( ($change_request == 0) && ($flag == 1) ) {
            Deactivator::remove_webmaster_role_multisite();
            update_network_option(get_current_network_id(), 'webmaster_role_switched_flag', 0);
        }
        else {
                wp_redirect(add_query_arg(['settings-updated' => 'false'], wp_get_referer()));
                exit;
        }

        wp_redirect(add_query_arg(['settings-updated' => 'true'], wp_get_referer()));
        exit;

    }

    


}