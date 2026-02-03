<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class QrController extends Controller
{
    // Credenciales de PagoFácil (desde .env)
    private $tokenService;
    private $tokenSecret;
    private $baseUrl = "https://masterqr.pagofacil.com.bo/api/services/v2";

    public function __construct()
    {
        $this->tokenService = env('PAGOFACIL_TOKEN_SERVICE');
        $this->tokenSecret = env('PAGOFACIL_TOKEN_SECRET');

        if (!$this->tokenService || !$this->tokenSecret) {
            Log::warning('⚠️ Credenciales de PagoFácil no configuradas en .env');
        }
    }

    /**
     * Obtener token de autenticación (con cache)
     */
    private function getAuthToken()
    {
        // Intentar obtener token del cache
        $cachedToken = Cache::get('pagofacil_token');
        if ($cachedToken) {
            Log::info('🔑 Usando token cacheado');
            return $cachedToken;
        }

        Log::info('🔑 Obteniendo nuevo token de PagoFácil');

        try {
            $client = new Client();
            $response = $client->post($this->baseUrl . '/login', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'tcTokenService' => $this->tokenService,
                    'tcTokenSecret' => $this->tokenSecret,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents());

            if ($result->error !== 0) {
                throw new \Exception('Error de autenticación: ' . $result->message);
            }

            $token = $result->values->accessToken;
            $expiresInMinutes = $result->values->expiresInMinutes;

            // Guardar en cache (restar 5 minutos para renovar antes)
            Cache::put('pagofacil_token', $token, now()->addMinutes($expiresInMinutes - 5));

            Log::info('✅ Token obtenido, válido por ' . $expiresInMinutes . ' minutos');

            return $token;

        } catch (\Exception $e) {
            Log::error('❌ Error al obtener token:', ['message' => $e->getMessage()]);
            throw new \Exception('No se pudo autenticar con PagoFácil: ' . $e->getMessage());
        }
    }

    public function generarQR(Request $request)
    {
        try {
            Log::info('=== GENERANDO QR (API V2) ===');
            Log::info('Request data:', $request->all());

            // Validación de datos
            $request->validate([
                'telefono' => 'required|string',
                'razon_social' => 'required|string',
                'nit' => 'required|string',
                'total' => 'required|numeric|min:0.01',
                'correo' => 'required|email',
            ]);

            // LIMPIAR DATOS
            $telefonoLimpio = preg_replace('/[^0-9]/', '', $request->telefono);
            if (strlen($telefonoLimpio) > 8 && substr($telefonoLimpio, 0, 3) === '591') {
                $telefonoLimpio = substr($telefonoLimpio, 3);
            }

            $nitLimpio = preg_replace('/[^0-9]/', '', $request->nit);
            if (empty($nitLimpio)) {
                $nitLimpio = '0';
            }

            $montoFinal = round(floatval($request->total), 2);
            $razonSocial = trim($request->razon_social);

            Log::info('Datos limpiados:', [
                'telefono' => $telefonoLimpio,
                'nit' => $nitLimpio,
                'monto' => $montoFinal,
                'razon_social' => $razonSocial
            ]);

            // OBTENER TOKEN DE AUTENTICACIÓN
            $token = $this->getAuthToken();

            // PREPARAR BODY SEGÚN NUEVA API V2
            $paymentNumber = "taller-" . time() . "-" . rand(100000, 999999);

            $body = [
                "paymentMethod" => 2, // 1 = QR Simple (consultar con PagoFácil)
                "clientName" => $razonSocial,
                "documentType" => 1, // 1 = CI, 2 = NIT, 3 = Pasaporte
                "documentId" => $nitLimpio,
                "phoneNumber" => $telefonoLimpio,
                "email" => $request->correo,
                "paymentNumber" => $paymentNumber,
                "amount" => $montoFinal,
                "currency" => 2, // 2 = BOB
                "clientCode" => $request->pago_id ?? 'CLIENTE-' . time(),
                "callbackUrl" => url('https://www.tecnoweb.org.bo/inf513/grupo01sa/'),
                "orderDetail" => [
                    [
                        "serial" => 1,
                        "product" => "Servicio Taller Mecanico",
                        "quantity" => 1,
                        "price" => $montoFinal,
                        "discount" => 0,
                        "total" => $montoFinal
                    ]
                ]
            ];

            Log::info('Body para PagoFácil V2:', $body);

            // LLAMAR A LA API V2
            $client = new Client();
            $response = $client->post($this->baseUrl . '/generate-qr', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'Response-Language' => 'es'
                ],
                'json' => $body,
                'timeout' => 30
            ]);

            $responseBody = $response->getBody()->getContents();
            Log::info('Respuesta de PagoFácil V2:', ['response' => $responseBody]);

            $result = json_decode($responseBody);

            // VERIFICAR RESPUESTA
            if ($result->error !== 0) {
                throw new \Exception('PagoFácil: ' . $result->message);
            }

            // EXTRAER DATOS DE LA RESPUESTA
            $qrBase64 = $result->values->qrBase64;
            $transactionId = $result->values->transactionId;
            $expirationDate = $result->values->expirationDate;

            Log::info('✅ QR generado exitosamente', [
                'transactionId' => $transactionId,
                'paymentNumber' => $paymentNumber,
                'expiration' => $expirationDate
            ]);

            return response()->json([
                'success' => true,
                'qrImage' => $qrBase64,
                'nroPago' => $paymentNumber,
                'transactionId' => $transactionId,
                'expirationDate' => $expirationDate,
                'message' => 'QR generado exitosamente'
            ]);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'Sin respuesta';

            Log::error('❌ Error HTTP al generar QR:', [
                'message' => $e->getMessage(),
                'response' => $errorBody
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con PagoFácil: ' . $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('❌ Error al generar QR:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        try {
            Log::info('📥 Callback de PagoFácil V2:', $request->all());

            // Estructura del callback según la documentación:
            // {
            //   "PedidoID": "...",
            //   "Fecha": "...",
            //   "Hora": "...",
            //   "MetodoPago": "...",
            //   "Estado": "..."
            // }

            $pedidoId = $request->input('PedidoID');
            $estado = $request->input('Estado');
            $metodoPago = $request->input('MetodoPago');
            $fecha = $request->input('Fecha');
            $hora = $request->input('Hora');

            // Aquí procesas la confirmación del pago
            // Actualizar estado del pago en tu base de datos usando $pedidoId

            Log::info('✅ Pago confirmado', [
                'pedido_id' => $pedidoId,
                'estado' => $estado,
                'metodo' => $metodoPago
            ]);

            // Respuesta esperada según documentación
            return response()->json([
                'error' => 0,
                'status' => 1,
                'message' => 'Callback procesado correctamente',
                'values' => true
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error en callback:', [
                'message' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => 'Error al procesar callback',
                'values' => false
            ], 500);
        }
    }
}
