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
