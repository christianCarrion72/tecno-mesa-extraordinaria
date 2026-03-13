<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\PlanPago;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Illuminate\Support\Str;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PlanPago $planPago)
    {
        if (!request()->user()->tienePermiso('pago.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar pagos.');
        }

        return Inertia::render('Pagos/Index', [
            'plan' => $planPago->load('ordenTrabajo'),
            'pagos' => $planPago->pagos()->orderBy('numerocuota')->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PlanPago $planPago)
    {
        if (!request()->user()->tienePermiso('pago.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear pagos.');
        }

        $registrados = $planPago->pagos()->count();
        if ($registrados >= (int) $planPago->numerocuotas) {
            return redirect()->route('plan-pagos.show', $planPago->id)
                ->with('error', 'Ya se registraron todas las cuotas del plan.');
        }
        $siguienteCuota = $registrados + 1;
        return Inertia::render('Pagos/Create', [
            'plan' => $planPago->load(['ordenTrabajo', 'ordenTrabajo.cliente']),
            'siguienteCuota' => $siguienteCuota,
            'metodos' => ['efectivo', 'tarjeta', 'pago facil'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PlanPago $planPago)
    {
        if (!request()->user()->tienePermiso('pago.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear pagos.');
        }

        if ($planPago->pagos()->count() >= (int) $planPago->numerocuotas) {
            return redirect()->route('plan-pagos.show', $planPago->id)
                ->with('error', 'Ya se registraron todas las cuotas del plan.');
        }

        $data = $request->validate([
            'estado' => 'required|string|in:pendiente,en proceso,terminado',
            'fechapago' => 'required|date',
            'metodopago' => 'required|string|in:efectivo,tarjeta,pago facil',
            'monto' => 'required|numeric|min:0',
            'numerocuota' => 'required|integer|min:1|max:' . (int) $planPago->numerocuotas,
            'referencia' => 'nullable|string',
        ]);

        Pago::crearParaPlan($planPago, $data);

        $planPago->refresh()->actualizarEstadoSegunPagos();

        return redirect()->route('plan-pagos.show', $planPago->id)
            ->with('success', 'Pago registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pago $pago)
    {
        if (!request()->user()->tienePermiso('pago.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para ver pagos.');
        }

        return Inertia::render('Pagos/Show', [
            'pago' => $pago->load('planPago'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pago $pago)
    {
        if (!request()->user()->tienePermiso('pago.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar pagos.');
        }

        return Inertia::render('Pagos/Edit', [
            'pago' => $pago->load(['planPago', 'planPago.ordenTrabajo']),
            'metodos' => ['efectivo', 'tarjeta', 'pago facil'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pago $pago)
    {
        if (!request()->user()->tienePermiso('pago.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar pagos.');
        }

        $maxCuotas = (int) optional($pago->planPago)->numerocuotas ?: 1;
        $data = $request->validate([
            'estado' => 'required|string|in:pendiente,en proceso,terminado',
            'fechapago' => 'required|date',
            'metodopago' => 'required|string|in:efectivo,tarjeta,pago facil',
            'monto' => 'required|numeric|min:0',
            'numerocuota' => 'required|integer|min:1|max:' . $maxCuotas,
            'referencia' => 'nullable|string',
        ]);

        $pago->actualizarDesdeFormulario($data);
        optional($pago->planPago)->actualizarEstadoSegunPagos();

        return redirect()->route('plan-pagos.show', $pago->plan_pago_id)
            ->with('success', 'Pago actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pago $pago)
    {
        if (!request()->user()->tienePermiso('pago.eliminar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para eliminar pagos.');
        }

        $planId = $pago->plan_pago_id;
        $pago->delete();
        $plan = PlanPago::find($planId);
        if ($plan) {
            $plan->actualizarEstadoSegunPagos();
        }
        return redirect()->route('plan-pagos.show', $planId)
            ->with('success', 'Pago eliminado.');
    }

    public function stripeConfig()
    {
        return response()->json([
            'publicKey' => config('services.stripe.key'),
            'currency' => config('services.stripe.currency', 'BOB'),
        ]);
    }

    public function stripeCreatePaymentIntent(Request $request, PlanPago $planPago)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.5',
            'numerocuota' => 'required|integer|min:1',
        ]);

        $amount = (float) $data['amount'];
        $numerocuota = (int) $data['numerocuota'];

        $token = Str::uuid()->toString();

        $nuevo = [
            'estado' => 'pendiente',
            'fechapago' => now()->toDateString(),
            'metodopago' => 'tarjeta',
            'monto' => $amount,
            'numerocuota' => $numerocuota,
            'referencia' => $token,
        ];

        $pago = Pago::crearParaPlan($planPago, $nuevo);

        $stripe = new StripeClient(config('services.stripe.secret'));
        $currency = strtolower(config('services.stripe.currency', 'BOB'));

        $intent = $stripe->paymentIntents->create([
            'amount' => (int) round($amount * 100),
            'currency' => $currency,
            'metadata' => [
                'pago_id' => $pago->id,
                'plan_pago_id' => $planPago->id,
                'numerocuota' => $numerocuota,
            ],
        ]);

        $pago->update([
            'pf_transaction_id' => $intent->id,
            'pf_status' => 0,
        ]);

        $checkoutUrl = route('pagos.stripe.checkout', ['token' => $token]);

        return response()->json([
            'checkoutUrl' => $checkoutUrl,
        ]);
    }

    public function stripeConfirmPago(Request $request, PlanPago $planPago)
    {
        $data = $request->validate([
            'payment_intent_id' => 'required|string',
            'amount' => 'required|numeric|min:0.5',
            'numerocuota' => 'required|integer|min:1',
        ]);

        $stripe = new StripeClient(config('services.stripe.secret'));
        $intent = $stripe->paymentIntents->retrieve($data['payment_intent_id']);

        if ($intent->status !== 'succeeded') {
            return response()->json([
                'error' => 1,
                'message' => 'El pago aún no está confirmado en Stripe.',
                'status' => $intent->status,
            ], 422);
        }

        $nuevo = [
            'estado' => 'terminado',
            'fechapago' => now()->toDateString(),
            'metodopago' => 'tarjeta',
            'monto' => (float) $data['amount'],
            'numerocuota' => (int) $data['numerocuota'],
            'referencia' => $intent->id,
        ];

        $pago = Pago::crearParaPlan($planPago, $nuevo);

        $pago->update([
            'pf_transaction_id' => $intent->id,
            'pf_payment_method_transaction_id' => $intent->payment_method ?? null,
            'pf_status' => 1,
            'pf_expiration_date' => null,
            'pf_qr_base64' => null,
        ]);

        $planPago->refresh()->actualizarEstadoSegunPagos();

        return response()->json([
            'success' => true,
            'pago_id' => $pago->id,
            'redirect' => route('plan-pagos.show', $planPago->id),
        ]);
    }

    public function stripeCheckoutPublic(string $token)
    {
        $pago = Pago::where('referencia', $token)
            ->where('metodopago', 'tarjeta')
            ->firstOrFail();

        $stripe = new StripeClient(config('services.stripe.secret'));
        $intent = $stripe->paymentIntents->retrieve($pago->pf_transaction_id);

        return Inertia::render('Pagos/StripeCheckout', [
            'token' => $token,
            'monto' => $pago->monto,
            'numerocuota' => $pago->numerocuota,
            'currency' => config('services.stripe.currency', 'BOB'),
            'stripePublicKey' => config('services.stripe.key'),
            'clientSecret' => $intent->client_secret,
            'confirmUrl' => route('pagos.stripe.confirm.public', ['token' => $token]),
        ]);
    }

    public function stripeConfirmPublic(Request $request, string $token)
    {
        $pago = Pago::where('referencia', $token)
            ->where('metodopago', 'tarjeta')
            ->firstOrFail();

        $data = $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $stripe = new StripeClient(config('services.stripe.secret'));
        $intent = $stripe->paymentIntents->retrieve($data['payment_intent_id']);

        if ($intent->status !== 'succeeded') {
            return response()->json([
                'error' => 1,
                'message' => 'El pago aún no está confirmado en Stripe.',
                'status' => $intent->status,
            ], 422);
        }

        $pago->update([
            'estado' => 'terminado',
            'pf_transaction_id' => $intent->id,
            'pf_payment_method_transaction_id' => $intent->payment_method ?? null,
            'pf_status' => 1,
            'pf_expiration_date' => null,
            'pf_qr_base64' => null,
        ]);

        optional($pago->planPago)->refresh()->actualizarEstadoSegunPagos();

        return response()->json([
            'success' => true,
            'redirect' => route('plan-pagos.show', $pago->plan_pago_id),
        ]);
    }

    public function pagofacilLogin(Request $request)
    {
        $baseUrlRaw = config('services.pagofacil.base_url') ?: env('PAGOFACIL_API_URL');
        $serviceRaw = $request->header('tcTokenService') ?: (config('services.pagofacil.token_service') ?: env('PAGOFACIL_TOKEN_SERVICE'));
        $secretRaw = $request->header('tcTokenSecret') ?: (config('services.pagofacil.token_secret') ?: env('PAGOFACIL_TOKEN_SECRET'));

        $baseUrl = rtrim(trim((string) $baseUrlRaw, " \t\n\r\0\x0B`\"'"), '/');
        $service = trim((string) $serviceRaw);
        $secret = trim((string) $secretRaw);

        if ($baseUrl === '' || $service === '' || $secret === '') {
            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => 'Configuración de PagoFacil incompleta. Defina PAGOFACIL_API_URL, PAGOFACIL_TOKEN_SERVICE y PAGOFACIL_TOKEN_SECRET.',
            ], 500);
        }

        $response = Http::withHeaders([
            'tcTokenService' => $service,
            'tcTokenSecret' => $secret,
        ])->post($baseUrl.'/login');

        $json = $response->json();
        Log::info('PagoFacil login response', ['response' => $json]);

        return response()->json($json, $response->status());
    }

    public function pagofacilListEnabledServices(Request $request)
    {
        $baseUrlRaw = config('services.pagofacil.base_url') ?: env('PAGOFACIL_API_URL');
        $serviceRaw = config('services.pagofacil.token_service') ?: env('PAGOFACIL_TOKEN_SERVICE');
        $secretRaw = config('services.pagofacil.token_secret') ?: env('PAGOFACIL_TOKEN_SECRET');

        $baseUrl = rtrim(trim((string) $baseUrlRaw, " \t\n\r\0\x0B`\"'"), '/');
        $service = trim((string) $serviceRaw);
        $secret = trim((string) $secretRaw);

        if ($baseUrl === '' || $service === '' || $secret === '') {
            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => 'Configuración de PagoFacil incompleta. Defina PAGOFACIL_API_URL, PAGOFACIL_TOKEN_SERVICE y PAGOFACIL_TOKEN_SECRET.',
            ], 500);
        }

        $authHeader = (string) $request->header('Authorization', '');
        $token = '';
        if (stripos($authHeader, 'Bearer ') === 0) {
            $token = trim(substr($authHeader, 7));
        }

        if ($token === '') {
            $login = Http::withHeaders([
                'tcTokenService' => $service,
                'tcTokenSecret' => $secret,
            ])->post($baseUrl.'/login');

            $loginJson = $login->json();
            if (!is_array($loginJson) || ($loginJson['error'] ?? 1) !== 0) {
                return response()->json($loginJson ?: [
                    'error' => 1,
                    'status' => 0,
                    'message' => 'No se pudo autenticar en PagoFacil',
                ], $login->status());
            }

            $token = (string) data_get($loginJson, 'values.accessToken', '');
            if ($token === '') {
                return response()->json([
                    'error' => 1,
                    'status' => 0,
                    'message' => 'Token de acceso no encontrado',
                ], 500);
            }
        }

        $list = Http::withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post($baseUrl.'/list-enabled-services');

        $json = $list->json();
        Log::info('PagoFacil list-enabled-services response', ['response' => $json]);
        return response()->json($json, $list->status());
    }

    public function pagofacilGenerateQr(Request $request)
    {
        $baseUrlRaw = config('services.pagofacil.base_url') ?: env('PAGOFACIL_API_URL');
        $serviceRaw = config('services.pagofacil.token_service') ?: env('PAGOFACIL_TOKEN_SERVICE');
        $secretRaw = config('services.pagofacil.token_secret') ?: env('PAGOFACIL_TOKEN_SECRET');

        $baseUrl = rtrim(trim((string) $baseUrlRaw, " \t\n\r\0\x0B`\"'"), '/');
        $service = trim((string) $serviceRaw);
        $secret = trim((string) $secretRaw);

        if ($baseUrl === '' || $service === '' || $secret === '') {
            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => 'Configuración de PagoFacil incompleta. Defina PAGOFACIL_API_URL, PAGOFACIL_TOKEN_SERVICE y PAGOFACIL_TOKEN_SECRET.',
            ], 500);
        }

        $data = $request->validate([
            'plan_pago_id' => 'required|integer|exists:plan_pagos,id',
            'payment_method_id' => 'required_without:paymentMethod',
            'paymentMethod' => 'required_without:payment_method_id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|integer',
            'callbackUrl' => 'nullable|string',
            'payment_number' => 'nullable|string',
            'paymentNumber' => 'nullable|string',
            'pago_id' => 'nullable|integer|exists:pagos,id',
            'numerocuota' => 'nullable|integer|min:1',
            'clientName' => 'nullable|string',
            'documentType' => 'nullable|integer',
            'documentId' => 'nullable|string',
            'phoneNumber' => 'nullable|string',
            'email' => 'nullable|string',
            'clientCode' => 'nullable|string',
            'orderDetail' => 'nullable|array',
            'orderDetail.*.serial' => 'nullable|integer',
            'orderDetail.*.product' => 'nullable|string',
            'orderDetail.*.quantity' => 'nullable|integer',
            'orderDetail.*.price' => 'nullable|numeric',
            'orderDetail.*.discount' => 'nullable|numeric',
            'orderDetail.*.total' => 'nullable|numeric',
        ]);

        $plan = PlanPago::with(['ordenTrabajo.cliente'])->findOrFail($data['plan_pago_id']);
        $orden = $plan->ordenTrabajo;
        $cliente = optional($orden)->cliente;

        $authHeader = (string) $request->header('Authorization', '');
        $token = '';
        if (stripos($authHeader, 'Bearer ') === 0) {
            $token = trim(substr($authHeader, 7));
        }
        if ($token === '') {
            $login = Http::withHeaders([
                'tcTokenService' => $service,
                'tcTokenSecret' => $secret,
            ])->post($baseUrl.'/login');
            $loginJson = $login->json();
            if (!is_array($loginJson) || ($loginJson['error'] ?? 1) !== 0) {
                return response()->json($loginJson ?: [
                    'error' => 1,
                    'status' => 0,
                    'message' => 'No se pudo autenticar en PagoFacil',
                ], $login->status());
            }
            $token = (string) data_get($loginJson, 'values.accessToken', '');
            if ($token === '') {
                return response()->json([
                    'error' => 1,
                    'status' => 0,
                    'message' => 'Token de acceso no encontrado',
                ], 500);
            }
        }

        $pm = $data['payment_method_id'] ?? $data['paymentMethod'] ?? null;
        if ($pm === null) {
            return response()->json([
                'error' => 1,
                'status' => 422,
                'message' => 'El campo paymentMethod es requerido',
            ], 422);
        }

        $payload = [
            'paymentMethod' => is_numeric($pm) ? (int) $pm : $pm,
            'clientName' => (string) ($data['clientName'] ?? (optional($cliente)->nombre ?: 'Cliente')),
            'documentType' => (int) ($data['documentType'] ?? 1),
            'documentId' => (string) ($data['documentId'] ?? (optional($cliente)->documento ?? optional($cliente)->telefono ?? '')),
            'phoneNumber' => (string) ($data['phoneNumber'] ?? (optional($cliente)->telefono ?? '')),
            'email' => (string) ($data['email'] ?? (optional($cliente)->email ?? '')),
            'paymentNumber' => (string) ($data['payment_number'] ?? ($data['paymentNumber'] ?? ('plan-'.$plan->id.'-'.uniqid()))),
            'amount' => (float) $data['amount'],
            'currency' => (int) ($data['currency'] ?? 2),
            'clientCode' => (string) ($data['clientCode'] ?? ($orden->cliente_id ?? '')),
            'callbackUrl' => (string) (($data['callbackUrl'] ?? config('services.pagofacil.callback_url')) ?: url('/pagofacil/callback')),
            'orderDetail' => !empty($data['orderDetail']) ? $data['orderDetail'] : [
                [
                    'serial' => 1,
                    'product' => 'Pago Plan #'.$plan->id,
                    'quantity' => 1,
                    'price' => (float) $data['amount'],
                    'discount' => 0,
                    'total' => (float) $data['amount'],
                ],
            ],
        ];

        $resp = Http::withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post($baseUrl.'/generate-qr', $payload);

        $json = $resp->json();
        Log::info('PagoFacil generate-qr response', ['response' => $json]);

        $pagoId = $data['pago_id'] ?? null;
        if (is_array($json) && ($json['error'] ?? 1) === 0) {
            $values = (array) ($json['values'] ?? []);
            if ($pagoId) {
                $pago = Pago::find($pagoId);
                if ($pago) {
                    $pago->update([
                        'pf_transaction_id' => (string) ($values['transactionId'] ?? ''),
                        'pf_payment_method_transaction_id' => (string) ($values['paymentMethodTransactionId'] ?? ''),
                        'pf_status' => isset($values['status']) ? (int) $values['status'] : null,
                        'pf_expiration_date' => (string) ($values['expirationDate'] ?? null),
                        'pf_qr_base64' => (string) ($values['qrBase64'] ?? ''),
                    ]);
                    $json['values'] = array_merge((array) ($json['values'] ?? []), ['pagoId' => $pago->id]);
                }
            } else {
                $nuevo = [
                    'estado' => 'pendiente',
                    'fechapago' => now()->toDateString(),
                    'metodopago' => 'pago facil',
                    'monto' => (float) $data['amount'],
                    'numerocuota' => (int) ($data['numerocuota'] ?? ($plan->pagos()->count() + 1)),
                    'referencia' => (string) $data['payment_method_id'],
                ];
                $pago = Pago::crearParaPlan($plan, $nuevo);
                $pago->update([
                    'pf_transaction_id' => (string) ($values['transactionId'] ?? ''),
                    'pf_payment_method_transaction_id' => (string) ($values['paymentMethodTransactionId'] ?? ''),
                    'pf_status' => isset($values['status']) ? (int) $values['status'] : null,
                    'pf_expiration_date' => (string) ($values['expirationDate'] ?? null),
                    'pf_qr_base64' => (string) ($values['qrBase64'] ?? ''),
                ]);
                $json['values'] = array_merge((array) ($json['values'] ?? []), ['pagoId' => $pago->id]);
            }
        }

        return response()->json($json, $resp->status());
    }

    public function pagofacilCallbackUrl()
    {
        $cb = config('services.pagofacil.callback_url') ?: url('/pagofacil/callback');
        return response()->json(['callbackUrl' => $cb]);
    }

    public function pagofacilCallback(Request $request)
    {
        $payload = $request->all();
        Log::info('PagoFacil callback received', ['payload' => $payload]);

        $pedidoId = (string) data_get($payload, 'PedidoID', '');
        $estadoCb = (string) data_get($payload, 'Estado', '');
        $fechaCb = (string) data_get($payload, 'Fecha', now()->toDateString());
        $horaCb = (string) data_get($payload, 'Hora', now()->format('H:i:s'));
        $metodoCb = (string) data_get($payload, 'MetodoPago', '');

        $pago = null;
        if ($pedidoId !== '') {
            $pago = Pago::where('pf_payment_method_transaction_id', $pedidoId)->first();
            if (!$pago) {
                $pago = Pago::where('referencia', $pedidoId)->first();
            }
        }

        if ($pago) {
            $terminadoValues = ['aprobado','completado','pagado','success','paid','1'];
            $estadoSistema = in_array(strtolower($estadoCb), $terminadoValues, true) ? 'terminado' : 'en proceso';
            $pago->update([
                'estado' => $estadoSistema,
                'fechapago' => $fechaCb,
            ]);
        }

        return response()->json([
            'error' => 0,
            'status' => 1,
            'message' => 'Notificación recibida',
            'values' => true,
        ], 200);
    }

    public function pagofacilQueryTransaction(Request $request)
    {
        $baseUrlRaw = config('services.pagofacil.base_url') ?: env('PAGOFACIL_API_URL');
        $serviceRaw = config('services.pagofacil.token_service') ?: env('PAGOFACIL_TOKEN_SERVICE');
        $secretRaw = config('services.pagofacil.token_secret') ?: env('PAGOFACIL_TOKEN_SECRET');

        $baseUrl = rtrim(trim((string) $baseUrlRaw, " \t\n\r\0\x0B`\"'"), '/');
        $service = trim((string) $serviceRaw);
        $secret = trim((string) $secretRaw);

        if ($baseUrl === '' || $service === '' || $secret === '') {
            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => 'Configuración de PagoFacil incompleta. Defina PAGOFACIL_API_URL, PAGOFACIL_TOKEN_SERVICE y PAGOFACIL_TOKEN_SECRET.',
            ], 500);
        }

        $data = $request->validate([
            'pago_id' => 'nullable|integer|exists:pagos,id',
            'pagofacilTransactionId' => 'nullable|string',
            'companyTransactionId' => 'nullable|string',
        ]);

        $pago = null;
        if (!empty($data['pago_id'])) {
            $pago = Pago::find($data['pago_id']);
        }

        $pagofacilTransactionId = (string) ($data['pagofacilTransactionId'] ?? ($pago?->pf_transaction_id ?? ''));
        $companyTransactionId = (string) ($data['companyTransactionId'] ?? ($pago?->referencia ?? ''));

        if ($pagofacilTransactionId === '' && $companyTransactionId === '') {
            return response()->json([
                'error' => 1,
                'status' => 422,
                'message' => 'Se requiere pagofacilTransactionId o companyTransactionId',
            ], 422);
        }

        $authHeader = (string) $request->header('Authorization', '');
        $token = '';
        if (stripos($authHeader, 'Bearer ') === 0) {
            $token = trim(substr($authHeader, 7));
        }
        if ($token === '') {
            $login = Http::withHeaders([
                'tcTokenService' => $service,
                'tcTokenSecret' => $secret,
            ])->post($baseUrl.'/login');
            $loginJson = $login->json();
            if (!is_array($loginJson) || ($loginJson['error'] ?? 1) !== 0) {
                return response()->json($loginJson ?: [
                    'error' => 1,
                    'status' => 0,
                    'message' => 'No se pudo autenticar en PagoFacil',
                ], $login->status());
            }
            $token = (string) data_get($loginJson, 'values.accessToken', '');
            if ($token === '') {
                return response()->json([
                    'error' => 1,
                    'status' => 0,
                    'message' => 'Token de acceso no encontrado',
                ], 500);
            }
        }

        $payload = [];
        if ($pagofacilTransactionId !== '') {
            $payload['pagofacilTransactionId'] = $pagofacilTransactionId;
        } elseif ($companyTransactionId !== '') {
            $payload['companyTransactionId'] = $companyTransactionId;
        }

        $resp = Http::withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post($baseUrl.'/query-transaction', $payload);

        $json = $resp->json();
        Log::info('PagoFacil query-transaction response', ['response' => $json]);

        if (!is_array($json)) {
            return response()->json([
                'error' => 1,
                'status' => $resp->status(),
                'message' => 'Respuesta inválida de PagoFacil',
            ], $resp->status());
        }

        if (($json['error'] ?? 1) === 0 && $pago) {
            $values = (array) ($json['values'] ?? []);
            $estadoPago = $values['paymentStatus'] ?? null;

            // Según docs de PagoFácil, paymentStatus es un Id numérico de estado de transacción.
            // Tomamos 2 (y variantes textuales) como pagado/completado.
            $statusString = is_string($estadoPago) ? strtolower($estadoPago) : (string) $estadoPago;

            $pagado = in_array($statusString, ['2', 'pagado', 'completed', 'completado'], true)
                || $estadoPago === 2;

            if ($pagado) {
                $updates = [
                    'estado' => 'terminado',
                ];

                if (isset($values['paymentStatus']) && is_numeric($values['paymentStatus'])) {
                    $updates['pf_status'] = (int) $values['paymentStatus'];
                }

                if (!empty($values['paymentDate'])) {
                    $updates['fechapago'] = substr((string) $values['paymentDate'], 0, 10);
                }

                $pago->update($updates);

                optional($pago->planPago)->refresh()->actualizarEstadoSegunPagos();
            }
        }

        return response()->json($json, $resp->status());
    }
}
