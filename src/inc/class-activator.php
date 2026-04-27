<?php

namespace SediciWebmasterRole\Inc;
use SediciWebmasterRole\Admin;


class Activator {

    // Capabilities de administrador y de otros plugins que se agregarán al nuevo rol
    private static function get_extra_admin_capabilities() {
        return [
            'edit_theme_options' => true,
            'customize' => true,
            'create_personal' => true,
            'delete_others_personal' => true,
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

    public static function create_rol() {
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

	public static function activate($network_wide) {
        
        if ( ! is_multisite() ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( 'Este plugin requiere una instalación Multisitio para funcionar.' );
        }

        
        if ( ! $network_wide ) {
            // Si el usuario intentó activarlo solo en un sitio individual:
            deactivate_plugins( plugin_basename( __FILE__ ) ); // Opcional: lo desactivamos
            wp_die( 
                '<strong>WP Webmaster Role:</strong> Este plugin solo puede ser activado a nivel de red (Network Activate). 
                Por favor, ve al Administrador de la Red para activarlo.' 
            );
        }

        self::create_rol();
        add_site_option('webmaster_role_switched_flag', false);
    }

}