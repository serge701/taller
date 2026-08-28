<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\VinConsulta;

class VinController extends Controller
{
    // API pública de NHTSA (vPIC), sin costo y sin API key. DecodeVinValues regresa
    // un solo objeto plano (a diferencia de DecodeVin, que regresa pares variable/valor).
    private const API_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinValues/';

    public function index(array $params): void
    {
        Auth::require();
        $this->render('vin/index', [
            'pageTitle' => 'Buscar VIN',
        ]);
    }

    public function consultas(array $params): void
    {
        Auth::require();
        $consultas = (new VinConsulta())->recientes();
        $this->render('vin/consultas', [
            'pageTitle'    => 'Historial de consultas VIN',
            'pageSubtitle' => count($consultas) . ' ' . (count($consultas) === 1 ? 'consulta registrada' : 'consultas registradas'),
            'consultas'    => $consultas,
        ]);
    }

    public function buscar(array $params): void
    {
        Auth::require();

        $vin = strtoupper(trim((string) $this->input('vin')));
        if ($vin === '') {
            $this->json(['ok' => false, 'error' => 'Ingresa un VIN.'], 422);
            return;
        }
        // Los VIN estándar (1981+) tienen 17 caracteres y no usan I, O ni Q para
        // evitar confusión con 1/0. Se acepta un rango más amplio (5-17) para no
        // bloquear VINs de vehículos muy viejos que la API a veces sí reconoce.
        if (!preg_match('/^[A-HJ-NPR-Z0-9]{5,17}$/', $vin)) {
            $this->json(['ok' => false, 'error' => 'Ese VIN no tiene un formato válido (letras/números, sin I, O ni Q).'], 422);
            return;
        }

        $vinLog = new VinConsulta();

        $resultado = $this->consultarNhtsa($vin);
        if ($resultado === null) {
            $error = 'No se pudo contactar al servicio de NHTSA. Intenta de nuevo en unos segundos.';
            $vinLog->registrar((int) Auth::id(), $vin, false, $error);
            $this->json(['ok' => false, 'error' => $error], 502);
            return;
        }

        $datos = $resultado['Results'][0] ?? null;
        if (!$datos) {
            $error = 'El servicio no devolvió información para ese VIN.';
            $vinLog->registrar((int) Auth::id(), $vin, false, $error);
            $this->json(['ok' => false, 'error' => $error], 404);
            return;
        }

        // La API regresa ~150 campos, la mayoría vacíos para cualquier vehículo dado.
        // Se filtran los vacíos para no mandar ruido al frontend.
        $limpio = array_filter($datos, static fn ($v) => $v !== null && trim((string) $v) !== '');

        $vinLog->registrar((int) Auth::id(), $vin, true, null, $limpio);

        $this->json(['ok' => true, 'vin' => $vin, 'data' => $limpio]);
    }

    /**
     * @return array<string, mixed>|null null si la llamada falla (red, timeout, JSON inválido).
     */
    private function consultarNhtsa(string $vin): ?array
    {
        $url = self::API_URL . rawurlencode($vin) . '?format=json';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);
        $respuesta = curl_exec($ch);
        $error     = curl_errno($ch);
        curl_close($ch);

        if ($error !== 0 || $respuesta === false) {
            return null;
        }

        $data = json_decode((string) $respuesta, true);
        return is_array($data) ? $data : null;
    }
}
