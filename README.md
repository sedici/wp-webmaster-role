# WP Webmaster Role

**Versión:** 1.0  
**Autor:** SEDICI 

## Descripción

Este plugin de WordPress crea un nuevo rol de usuario llamado **Webmaster**, que hereda todas las capacidades del rol **Editor**, y además incorpora algunas capacidades avanzadas propias del rol **Administrador**.

Al desactivar el plugin, todos los usuarios que posean el rol de **Webmaster** son reasignados automáticamente al rol de **Editor** para mantener la consistencia del sistema.

## Características

- Crea el rol `webmaster` con:
  - Todas las capacidades de un **Editor**.
  - Capacidades adicionales de un **Administrador**.
- Reasigna a los usuarios con rol `webmaster` al rol `editor` al desactivar el plugin.
- Limpieza automática: elimina el rol `webmaster` al desactivarse.

## Instalación

1. Subí la carpeta `wp-webmaster-role` a tu directorio `wp-content/plugins/`.
2. Asegurate de que el archivo principal sea `wp-webmaster-role.php`.
3. Activá el plugin desde el panel de administración de WordPress (Plugins > Plugins instalados).

## Uso

Una vez activado, podés asignar el nuevo rol **Webmaster** a cualquier usuario desde la sección "Usuarios" en el panel de WordPress.

## Personalización

Si necesitás agregar o quitar capacidades al rol `webmaster`, podés modificar la función `get_extra_admin_capabilities()` en el archivo principal del plugin (`wp-webmaster-role.php`).

```php
function get_extra_admin_capabilities() {
    return [
        'manage_options',
        'edit_theme_options',
        'list_users',
        'promote_users',
    ];
}


Un usuario en wp puede no tener un role (None) pero no podra ni siquiera iniciar sesion porque no tendra acceso