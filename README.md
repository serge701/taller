# Taller Mecánico Reyes

Sistema de administración para un taller mecánico: clientes, servicios, ventas,
trabajos largos (reparaciones), reportes y usuarios. PHP puro con arquitectura
MVC ligera (sin framework), MySQL, AdminLTE4 + Bootstrap 5 + DataTables (vía CDN).

## Requisitos

- PHP 8.1+ con extensiones `pdo_mysql` y `gd`
- MySQL / MariaDB
- Apache con `mod_rewrite` habilitado (usa `.htaccess`)

## Instalación

1. Copia `.env.example` a `.env` y ajusta los valores (base de datos, `APP_DEBUG`, etc).
2. Crea la base de datos y aplica las migraciones **en orden**, forzando utf8mb4
   para que los acentos no se corrompan:

   ```
   mysql --default-character-set=utf8mb4 -u root < migrations/001_init.sql
   mysql --default-character-set=utf8mb4 -u root taller < migrations/002_users_foto.sql
   mysql --default-character-set=utf8mb4 -u root taller < migrations/003_metodo_pago_tarjetas.sql
   mysql --default-character-set=utf8mb4 -u root taller < migrations/004_trabajos.sql
   mysql --default-character-set=utf8mb4 -u root taller < migrations/005_configuracion.sql
   ```

3. Apunta el document root del sitio (o un alias de Apache) a esta carpeta.
4. Asegúrate de que el usuario del servidor web pueda escribir en:
   - `storage/logs/`
   - `assets/img/avatars/`
5. Entra a `/login` con el usuario inicial `admin` / `admin123` y **cambia la
   contraseña de inmediato** desde "Mi Perfil".
6. Ve a **Configuración** (menú Administración) y captura los datos reales del
   negocio (nombre, RFC, dirección, teléfono, email) — se usan junto al logo y
   en el recibo impreso de las ventas.

## Estructura

```
app/Core/         Router, Auth, Database (PDO), Config, Csrf, helpers globales
app/Controllers/  Un controlador por recurso (Cliente, Servicio, Venta, Trabajo, ...)
app/Models/       Acceso a datos (extienden BaseModel)
app/Views/        Vistas PHP planas, layouts/ y partials/ compartidos
migrations/       SQL versionado, aplicar en orden
routes.php        Todas las rutas de la app
```

## Roles

- **Admin**: acceso total, incluye Usuarios, Reportes y Configuración.
- **Usuario**: operación diaria (clientes, servicios, ventas, trabajos, reportes),
  sin las cifras financieras generales del Dashboard (semana/mes/año).
