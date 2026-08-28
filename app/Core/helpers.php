<?php
declare(strict_types=1);

use App\Core\Config;
use App\Core\Csrf;
use App\Core\Auth;

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        $base = Config::baseUrl();
        return $path === '' ? $base . '/' : $base . '/' . $path;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path = ''): void
    {
        header('Location: ' . url($path));
        exit;
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('view')) {
    function view(string $name, array $data = [], string $layout = ''): void
    {
        $viewsDir = dirname(__DIR__) . '/Views/';
        $viewFile = $viewsDir . str_replace('.', '/', $name) . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo "Vista no encontrada: " . e($name);
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === '') {
            echo $content;
            return;
        }

        $layoutFile = $viewsDir . 'layouts/' . $layout . '.php';
        if (!is_file($layoutFile)) {
            echo $content;
            return;
        }
        require $layoutFile;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(Csrf::token()) . '">';
    }
}

if (!function_exists('flash')) {
    function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }
}

if (!function_exists('get_flash')) {
    function get_flash(string $key): ?string
    {
        $msg = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }
}

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        return e($_SESSION['_old'][$key] ?? $default);
    }
}

if (!function_exists('set_old')) {
    function set_old(array $data): void
    {
        unset($data['_csrf'], $data['password']);
        $_SESSION['_old'] = $data;
    }
}

if (!function_exists('clear_old')) {
    function clear_old(): void
    {
        unset($_SESSION['_old']);
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return Auth::user();
    }
}

if (!function_exists('negocio')) {
    /**
     * Datos del negocio (Configuración): nombre, RFC, dirección, teléfono, email.
     * Se usa junto al logo, en el recibo impreso, etc. Cacheado por request.
     * Si la tabla aún no existe (p. ej. migración pendiente) cae en el nombre del config.php.
     */
    function negocio(): array
    {
        static $cache = null;
        if ($cache === null) {
            try {
                $cache = (new \App\Models\Configuracion())->obtener();
            } catch (\Throwable $e) {
                $cache = [
                    'id'            => 1,
                    'nombre_taller' => Config::get('app')['name'] ?? 'Taller',
                    'rfc'           => null,
                    'direccion'     => null,
                    'telefono'      => null,
                    'email'         => null,
                ];
            }
        }
        return $cache;
    }
}

if (!function_exists('active')) {
    function active(string $segment): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        return str_contains($uri, '/' . $segment) ? 'active' : '';
    }
}

if (!function_exists('formato_moneda')) {
    function formato_moneda(float|string|null $monto, string $symbol = '$'): string
    {
        return $symbol . number_format((float) $monto, 2, '.', ',');
    }
}

if (!function_exists('fecha_legible')) {
    function fecha_legible(?string $fecha): string
    {
        if (empty($fecha)) return '';
        try {
            return (new DateTime($fecha))->format('d/m/Y H:i');
        } catch (\Throwable $e) {
            return '';
        }
    }
}

if (!function_exists('catalogo_regimenes_fiscales')) {
    /**
     * Catálogo SAT c_RegimenFiscal (CFDI 4.0) — regímenes vigentes para personas
     * físicas y morales en México, usado en el select de "Régimen fiscal" del cliente.
     */
    function catalogo_regimenes_fiscales(): array
    {
        return [
            '601 - General de Ley Personas Morales',
            '603 - Personas Morales con Fines no Lucrativos',
            '605 - Sueldos y Salarios e Ingresos Asimilados a Salarios',
            '606 - Arrendamiento',
            '607 - Régimen de Enajenación o Adquisición de Bienes',
            '608 - Demás ingresos',
            '610 - Residentes en el Extranjero sin Establecimiento Permanente en México',
            '611 - Ingresos por Dividendos (socios y accionistas)',
            '612 - Personas Físicas con Actividades Empresariales y Profesionales',
            '614 - Ingresos por intereses',
            '615 - Régimen de los ingresos por obtención de premios',
            '616 - Sin obligaciones fiscales',
            '620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos',
            '621 - Incorporación Fiscal',
            '622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
            '623 - Opcional para Grupos de Sociedades',
            '624 - Coordinados',
            '625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas',
            '626 - Régimen Simplificado de Confianza (RESICO)',
        ];
    }
}

if (!function_exists('catalogo_colores_vehiculo')) {
    /**
     * Colores de vehículo más comunes, con su hex, para el selector con swatch
     * en Ventas/Trabajos. "Otro color" se maneja aparte en el formulario.
     */
    function catalogo_colores_vehiculo(): array
    {
        return [
            ['nombre' => 'Blanco',        'hex' => '#FFFFFF'],
            ['nombre' => 'Negro',         'hex' => '#000000'],
            ['nombre' => 'Gris',          'hex' => '#808080'],
            ['nombre' => 'Plata',         'hex' => '#C0C0C0'],
            ['nombre' => 'Rojo',          'hex' => '#DC2626'],
            ['nombre' => 'Vino',          'hex' => '#7B1E3A'],
            ['nombre' => 'Azul',          'hex' => '#2563EB'],
            ['nombre' => 'Azul marino',   'hex' => '#1E3A8A'],
            ['nombre' => 'Verde',         'hex' => '#16A34A'],
            ['nombre' => 'Amarillo',      'hex' => '#FACC15'],
            ['nombre' => 'Naranja',       'hex' => '#F97316'],
            ['nombre' => 'Café',          'hex' => '#7C4A26'],
            ['nombre' => 'Beige',         'hex' => '#E8DCC5'],
            ['nombre' => 'Dorado',        'hex' => '#D4AF37'],
            ['nombre' => 'Morado',        'hex' => '#7C3AED'],
            ['nombre' => 'Rosa',          'hex' => '#EC4899'],
            ['nombre' => 'Turquesa',      'hex' => '#14B8A6'],
            ['nombre' => 'Champagne',     'hex' => '#F0E4C8'],
        ];
    }
}

if (!function_exists('catalogo_anios_vehiculo')) {
    /**
     * Años para el selector de "Año" del vehículo: del actual hacia 50 años atrás.
     */
    function catalogo_anios_vehiculo(): array
    {
        $actual = (int) date('Y');
        return range($actual, $actual - 50);
    }
}

if (!function_exists('vehiculo_resumen')) {
    /**
     * Texto corto "Marca Modelo (Año) · Color" para listados y detalle.
     * Tolera registros previos a la migración de modelo/año (quedan en NULL).
     */
    function vehiculo_resumen(array $row): string
    {
        $partes = [trim((string) ($row['vehiculo_marca'] ?? ''))];
        if (!empty($row['vehiculo_modelo'])) {
            $partes[] = $row['vehiculo_modelo'];
        }
        $vehiculo = trim(implode(' ', array_filter($partes)));

        if (!empty($row['vehiculo_anio'])) {
            $vehiculo .= ' (' . (int) $row['vehiculo_anio'] . ')';
        }

        $color = trim((string) ($row['vehiculo_color'] ?? ''));
        return $color !== '' ? trim($vehiculo . ' · ' . $color, ' ·') : $vehiculo;
    }
}

if (!function_exists('metodo_pago_badge')) {
    function metodo_pago_badge(?string $metodo): string
    {
        [$bg, $label] = match ($metodo) {
            'Efectivo'           => ['success', 'Efectivo'],
            'Tarjeta de Débito'  => ['primary', 'Tarjeta de Débito'],
            'Tarjeta de Crédito' => ['warning', 'Tarjeta de Crédito'],
            'Transferencia'      => ['info',    'Transferencia'],
            default              => ['secondary', $metodo ?? '—'],
        };
        return '<span class="badge text-bg-' . $bg . '">' . e($label) . '</span>';
    }
}

if (!function_exists('trabajo_estado_badge')) {
    function trabajo_estado_badge(?string $estado): string
    {
        [$bg, $label] = match ($estado) {
            'Abierto' => ['warning', 'Abierto'],
            'Cerrado' => ['success', 'Cerrado'],
            default   => ['secondary', $estado ?? '—'],
        };
        return '<span class="badge text-bg-' . $bg . '">' . e($label) . '</span>';
    }
}

if (!function_exists('procesar_subida_avatar')) {
    /**
     * Valida un archivo subido por <input type="file">, lo recorta al centro a un
     * cuadrado y lo guarda como JPG en assets/img/avatars. Compartido por Perfil
     * (foto propia) y Usuarios (un admin sube la foto de otro usuario). No borra
     * el archivo anterior ni toca la BD — eso lo hace el controlador que llama.
     *
     * @return array{ok: bool, archivo: ?string, error: ?string}
     */
    function procesar_subida_avatar(?array $file, string $prefijo): array
    {
        $dir      = BASE_DIR . '/assets/img/avatars';
        $maxBytes = 3 * 1024 * 1024; // 3MB
        $tamano   = 400; // px, imagen cuadrada final

        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['ok' => false, 'archivo' => null, 'error' => 'Selecciona una imagen.'];
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'archivo' => null, 'error' => 'Ocurrió un error al subir la imagen.'];
        }
        if ($file['size'] > $maxBytes) {
            return ['ok' => false, 'archivo' => null, 'error' => 'La imagen no debe superar 3MB.'];
        }

        $info = @getimagesize($file['tmp_name']);
        if ($info === false) {
            return ['ok' => false, 'archivo' => null, 'error' => 'El archivo no es una imagen válida.'];
        }

        $origen = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($file['tmp_name']),
            'image/png'  => @imagecreatefrompng($file['tmp_name']),
            'image/webp' => @imagecreatefromwebp($file['tmp_name']),
            default      => null,
        };
        if ($origen === false || $origen === null) {
            return ['ok' => false, 'archivo' => null, 'error' => 'Formato de imagen no soportado. Usa JPG, PNG o WEBP.'];
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $nombreArchivo = $prefijo . '_' . time() . '_' . random_int(1000, 9999) . '.jpg';
        $destino       = $dir . '/' . $nombreArchivo;

        $anchoOrig = imagesx($origen);
        $altoOrig  = imagesy($origen);
        $lado      = min($anchoOrig, $altoOrig);
        $srcX      = intdiv($anchoOrig - $lado, 2);
        $srcY      = intdiv($altoOrig - $lado, 2);

        $destinoImg = imagecreatetruecolor($tamano, $tamano);
        imagecopyresampled($destinoImg, $origen, 0, 0, $srcX, $srcY, $tamano, $tamano, $lado, $lado);
        imagejpeg($destinoImg, $destino, 85);

        imagedestroy($destinoImg);
        imagedestroy($origen);

        return ['ok' => true, 'archivo' => $nombreArchivo, 'error' => null];
    }
}

if (!function_exists('eliminar_avatar_anterior')) {
    /** Borra del disco un archivo de avatar previo, si existe. */
    function eliminar_avatar_anterior(?string $archivo): void
    {
        if (!$archivo) {
            return;
        }
        $ruta = BASE_DIR . '/assets/img/avatars/' . $archivo;
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }
}
