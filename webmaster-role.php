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

namespace SediciWebmasterRole;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/* Definición de constantes globales */
define('SEDICI_WEBMASTER_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Carga del Autoloader 
require_once SEDICI_WEBMASTER_PLUGIN_DIR . 'src/inc/class-autoloader.php';
// Carga del archivo de la clase Admin 
require_once SEDICI_WEBMASTER_PLUGIN_DIR . 'src/admin/class-admin.php';

register_activation_hook(__FILE__, array('SediciWebmasterRole\Inc\Activator', 'activate'));
register_deactivation_hook(__FILE__, array('SediciWebmasterRole\Inc\Deactivator', 'deactivate'));


Class WebmasterRole {

    static $admin;
	/**
	 * Inicia el plugin
	 *
	 */
	public static function init()
	{
		$plugin = \SediciWebmasterRole\Admin\Admin::get_instance();
        $plugin->run();
	}
}

function webmaster_role_init() {
    return WebmasterRole::init();
}

webmaster_role_init();
