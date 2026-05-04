<?php
/**
 * Vista del formulario para cambiar roles.
*/
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Obtenemos el estado actual del flag
$is_switched = get_network_option(get_current_network_id(), 'webmaster_role_switched_flag');

?>
<div class="wrap">
    <h1>Gestión de Rol Webmaster</h1>

    <?php if ( isset( $_GET['settings-updated'] ) ) : ?>
        <?php if ( $_GET['settings-updated'] === 'true' ) : ?>
            <div class="notice notice-success is-dismissible">
                <p>Los roles han sido actualizados correctamente.</p>
            </div>
        <?php elseif ( $_GET['settings-updated'] === 'false' ) : ?>
            <div class="notice notice-error is-dismissible">
                <p>No se realizaron cambios en los roles.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card" style="max-width: 600px; margin-top: 20px;">
        <h2>Cambiar Editores a Webmasters</h2>
        <p>Al activar esta opción, todos los usuarios con el rol de <strong>Editor</strong> pasarán a ser <strong>Webmasters</strong>.</p>
        <p>Al desactivarla, los Webmasters volverán a ser Editores.</p>

        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <?php wp_nonce_field('webmaster_switch_action', 'webmaster_role_nonce'); ?>
            <input type="hidden" name="action" value="switch_roles_webmaster_plugin">
            
            <table class="form-table">
                <tr>
                    <th scope="row">Estado del Rol</th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="webmaster_role_flag" value="1" <?php checked($is_switched, 1); ?>>
                            Activar rol Webmaster para los Editores
                        </label>
                    </td>
                </tr>
            </table>

            <?php submit_button('Aplicar Cambios'); ?>
        </form>
    </div>
    
    <div class="description" style="margin-top: 20px;">
        <p><strong>Nota:</strong> Esta acción afecta a toda la red de sitios </p>
    </div>
</div>
