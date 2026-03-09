<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Combobox from '@/Components/ui/Combobox.vue';

const props = defineProps<{ plan: any; siguienteCuota: number; metodos: string[] }>();
const metodosItems = computed(() => props.metodos.map(m => ({ id: m, label: m })));

const form = useForm({
    estado: 'pendiente',
    fechapago: '',
    metodopago: props.metodos[0] ?? 'efectivo',
    monto: props.plan.montoporcuota != null ? String(props.plan.montoporcuota) : '0',
    numerocuota: props.siguienteCuota != null ? String(props.siguienteCuota) : '1',
    referencia: '',
});

const submit = async () => {
    if (esTarjeta.value) {
        await procesarPagoStripe();
    } else {
        form.post(route('plan-pagos.pagos.store', props.plan.id));
    }
};

const getCookie = (name: string): string => {
    const match = document.cookie.split('; ').find(r => r.startsWith(name + '='));
    return match ? decodeURIComponent(match.split('=')[1]) : '';
};
const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';
const xsrfToken = getCookie('XSRF-TOKEN');

const pfToken = ref<string>('');
const pfLoginError = ref<string>('');
const obtenerTokenPagoFacil = async (): Promise<string | null> => {
    try {
        const res = await fetch('/pagofacil/login');
        const data = await res.json();
        console.log('PagoFacil login response:', data);
        const token = data?.values?.accessToken ?? '';
        pfToken.value = token;
        pfLoginError.value = data?.error ? (data?.message || 'Error en login PagoFacil') : '';
        return token || null;
    } catch (e) {
        console.error('Error obteniendo token PagoFacil:', e);
        pfLoginError.value = 'Error obteniendo token PagoFacil';
        return null;
    }
};

const serviciosPF = ref<any[]>([]);
const selectedPaymentMethodId = ref<string>('');
const loadingPF = ref(false);
const pfItems = computed(() => serviciosPF.value.map(s => ({ id: String(s.paymentMethodId), label: s.paymentMethodName })));
const pfQrBase64 = ref<string>('');
const pfClientName = ref<string>('');
const pfDocumentType = ref<string>('1');
const pfDocumentId = ref<string>('');
const pfPhoneNumber = ref<string>('');
const pfEmail = ref<string>('');
const pfPaymentNumber = ref<string>('');
const pfAmount = ref<string>(String(Number(form.monto) || 0));
const pfCurrency = ref<string>('2');
const pfClientCode = ref<string>('');
const pfOrderSerial = ref<string>('1');
const pfOrderProduct = ref<string>('');
const pfOrderQuantity = ref<string>('1');
const pfOrderPrice = ref<string>(String(Number(form.monto) || 0));
const pfOrderDiscount = ref<string>('0');
const pfOrderTotal = ref<string>(String(Number(form.monto) || 0));

const cargarPFServicios = async () => {
    loadingPF.value = true;
    try {
        if (!pfToken.value) {
            await obtenerTokenPagoFacil();
        }
        const res = await fetch('/pagofacil/list-enabled-services', {
            headers: pfToken.value ? { Authorization: `Bearer ${pfToken.value}` } : {},
        });
        const data = await res.json();
        console.log('PagoFacil list-enabled-services response:', data);
        serviciosPF.value = Array.isArray(data.values) ? data.values : [];
        console.log('[PF] serviciosPF:', serviciosPF.value);
    } catch (e) {
        console.error('Error listando métodos PagoFacil:', e);
        serviciosPF.value = [];
    } finally {
        loadingPF.value = false;
    }
};

const prefillPFCliente = () => {
    const cli = props.plan?.orden_trabajo?.cliente;
    if (cli) {
        if (!pfClientName.value) pfClientName.value = cli.nombre || '';
        if (!pfPhoneNumber.value) pfPhoneNumber.value = cli.telefono || '';
        if (!pfClientCode.value) pfClientCode.value = String(props.plan?.orden_trabajo?.cliente_id ?? '');
    }
};

const generarQrPF = async () => {
    try {
        if (!pfToken.value) {
            await obtenerTokenPagoFacil();
        }
        const cbRes = await fetch('/pagofacil/callback-url');
        const cbJson = await cbRes.json();
        const callbackUrl = cbJson?.callbackUrl || '';
        console.log('[PF] Callback URL usada:', callbackUrl);
        const payload = {
            plan_pago_id: props.plan.id,
            payment_method_id: selectedPaymentMethodId.value,
            amount: Number(pfAmount.value),
            currency: Number(pfCurrency.value),
            payment_number: pfPaymentNumber.value || `plan-${props.plan.id}-cuota-${form.numerocuota}`,
            numerocuota: Number(form.numerocuota),
            clientName: String(pfClientName.value || ''),
            documentType: Number(pfDocumentType.value),
            documentId: String(pfDocumentId.value || ''),
            phoneNumber: String(pfPhoneNumber.value || ''),
            email: String(pfEmail.value || ''),
            clientCode: String(pfClientCode.value || ''),
            callbackUrl,
            orderDetail: [
                {
                    serial: Number(pfOrderSerial.value),
                    product: pfOrderProduct.value || `Pago Plan #${props.plan.id}`,
                    quantity: Number(pfOrderQuantity.value),
                    price: Number(pfOrderPrice.value),
                    discount: Number(pfOrderDiscount.value),
                    total: Number(pfOrderTotal.value),
                },
            ],
        };
        const res = await fetch('/pagofacil/generate-qr', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
                ...(pfToken.value ? { Authorization: `Bearer ${pfToken.value}` } : {}),
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });
        const ct = res.headers.get('content-type') || '';
        const data = ct.includes('application/json') ? await res.json() : { error: 1, status: res.status, message: 'Respuesta no válida' };
        console.log('PagoFacil generate-qr response:', data);
        if (data?.error) {
            pfLoginError.value = data?.message || 'Error generando QR';
        } else {
            const pagoId = data?.values?.pagoId;
            if (pagoId) {
                router.visit(`/pagos/${pagoId}`);
                return;
            }
            pfQrBase64.value = data?.values?.qrBase64 || '';
        }
    } catch (e) {
        console.error('Error generando QR PagoFacil:', e);
        pfLoginError.value = 'Error generando QR PagoFacil';
    }
};

// Computed para detectar si el método seleccionado es Pago Fácil
const esPagoFacil = computed(() => {
    const metodo = String(form.metodopago).toLowerCase().trim();
    console.log('[DEBUG] Método actual:', metodo, 'Es pago facil?', metodo === 'pago facil' || metodo === 'pago fácil');
    return metodo === 'pago facil' || metodo === 'pago fácil';
});

const clientePlan = computed(() => props.plan?.orden_trabajo?.cliente || null);
watch(esPagoFacil, (isPF) => {
    if (isPF) {
        pfClientName.value = clientePlan.value?.nombre || pfClientName.value || '';
        pfPhoneNumber.value = clientePlan.value?.telefono || pfPhoneNumber.value || '';
        pfClientCode.value = String(props.plan?.orden_trabajo?.cliente_id ?? pfClientCode.value ?? '');
    }
});

watch(() => form.metodopago, async (m) => {
    console.log('[PF] método seleccionado:', m);
    if (String(m).toLowerCase().trim() === 'pago facil') {
        prefillPFCliente();
        await obtenerTokenPagoFacil();
        await cargarPFServicios();
    } else {
        serviciosPF.value = [];
        selectedPaymentMethodId.value = '';
    }
});

watch(() => selectedPaymentMethodId.value, (id) => {
    if (String(form.metodopago).toLowerCase().trim() === 'pago facil') {
        form.referencia = id || '';
    }
});

watch(() => form.metodopago, (m) => {
    if (String(m).toLowerCase().trim() !== 'pago facil') {
        pfQrBase64.value = '';
    }
});

watch(() => form.monto, (m) => {
    const val = Number(m) || 0;
    pfAmount.value = String(val);
    pfOrderPrice.value = String(val);
    pfOrderTotal.value = String(val);
});

const esTarjeta = computed(() => {
    const metodo = String(form.metodopago).toLowerCase().trim();
    return metodo === 'tarjeta';
});

const stripe = ref<any | null>(null);
const cardElement = ref<any | null>(null);
const stripeClientSecret = ref<string>('');
const stripeLoading = ref(false);
const stripeError = ref<string>('');

const initStripe = async () => {
    stripeError.value = '';
    try {
        const res = await fetch('/stripe/config');
        const cfg = await res.json();
        if (!cfg.publicKey) {
            stripeError.value = 'Configuración de Stripe incompleta.';
            return;
        }
        const anyWindow = window as any;
        if (!anyWindow.Stripe) {
            stripeError.value = 'Stripe.js no está disponible.';
            return;
        }
        stripe.value = anyWindow.Stripe(cfg.publicKey);
        const elements = stripe.value.elements();
        const card = elements.create('card');
        card.mount('#stripe-card-element');
        cardElement.value = card;
    } catch (e) {
        console.error('Error inicializando Stripe', e);
        stripeError.value = 'No se pudo inicializar Stripe.';
    }
};

watch(esTarjeta, async (isCard) => {
    if (isCard && !stripe.value) {
        await initStripe();
    }
});

const procesarPagoStripe = async () => {
    stripeError.value = '';
    if (!stripe.value || !cardElement.value) {
        await initStripe();
        if (!stripe.value || !cardElement.value) {
            stripeError.value = 'Stripe no está listo.';
            return;
        }
    }
    try {
        stripeLoading.value = true;
        const resIntent = await fetch(route('plan-pagos.pagos.stripe.intent', props.plan.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                amount: Number(form.monto),
                numerocuota: Number(form.numerocuota),
            }),
        });
        const intentData = await resIntent.json();
        if (!resIntent.ok || !intentData.clientSecret) {
            stripeError.value = intentData.message || 'No se pudo crear el pago en Stripe.';
            return;
        }
        stripeClientSecret.value = intentData.clientSecret;
        const result = await stripe.value.confirmCardPayment(stripeClientSecret.value, {
            payment_method: {
                card: cardElement.value,
            },
        });
        if (result.error) {
            console.error(result.error);
            stripeError.value = result.error.message || 'Error procesando la tarjeta.';
            return;
        }
        if (result.paymentIntent.status !== 'succeeded') {
            stripeError.value = 'El pago no fue completado (status: ' + result.paymentIntent.status + ').';
            return;
        }
        const confirmRes = await fetch(route('plan-pagos.pagos.stripe.confirm', props.plan.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                payment_intent_id: result.paymentIntent.id,
                amount: Number(form.monto),
                numerocuota: Number(form.numerocuota),
            }),
        });
        const confirmData = await confirmRes.json();
        if (!confirmRes.ok || !confirmData.success) {
            stripeError.value = confirmData.message || 'No se pudo registrar el pago.';
            return;
        }
        if (confirmData.redirect) {
            window.location.href = confirmData.redirect;
        } else if (confirmData.pago_id) {
            router.visit(`/pagos/${confirmData.pago_id}`);
        } else {
            router.visit(route('plan-pagos.show', props.plan.id));
        }
    } catch (e) {
        console.error('Error procesando pago Stripe', e);
        stripeError.value = 'Error inesperado procesando el pago.';
    } finally {
        stripeLoading.value = false;
    }
};
</script>

<template>
    <Head :title="`Nuevo Pago para Plan #${plan.id}`" />

    <AppLayout>
        <div class="p-4 max-w-3xl">
            <Card>
                <CardHeader>
                    <CardTitle>Registrar Pago</CardTitle>
                    <CardDescription>Plan #{{ plan.id }} · Orden #{{ plan.orden_trabajo_id }}</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label>Estado</Label>
                            <Combobox v-model="form.estado" :items="['pendiente','en proceso','terminado'].map(e => ({ id: e, label: e }))" />
                            <InputError :message="form.errors.estado" />
                        </div>

                        <div>
                            <Label>Fecha de Pago</Label>
                            <TextInput type="date" v-model="form.fechapago" />
                            <InputError :message="form.errors.fechapago" />
                        </div>

                        <div>
                            <Label>Método de Pago</Label>
                            <Combobox v-model="form.metodopago" :items="metodosItems" />
                            <InputError :message="form.errors.metodopago" />
                        </div>

                        <!-- Sección expandible de Pago Fácil -->
                        <div v-if="esPagoFacil" class="md:col-span-2 border border-input rounded-lg p-4 bg-muted space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-foreground">Configuración Pago Fácil</h3>
                                <span v-if="loadingPF" class="text-sm text-blue-600">Cargando...</span>
                            </div>

                            <div v-if="pfLoginError" class="p-3 bg-destructive/15 border border-destructive/40 rounded text-sm text-destructive">
                                {{ pfLoginError }}
                            </div>

                            <div>
                                <Label>Método de Pago Fácil</Label>
                                <Combobox v-model="selectedPaymentMethodId" :items="pfItems" placeholder="Seleccione un método..." />
                                <p v-if="serviciosPF.length === 0 && !loadingPF" class="text-xs text-muted-foreground mt-1">
                                    No hay métodos disponibles
                                </p>
                            </div>

                            <div class="grid md:grid-cols-2 gap-3">
                                <div>
                                <Label>Nombre del Cliente</Label>
                                <TextInput type="text" v-model="pfClientName" placeholder="Nombre completo" />
                                </div>
                                <div>
                                    <Label>Tipo Documento</Label>
                                    <TextInput type="number" v-model="pfDocumentType" placeholder="1 = CI" />
                                </div>
                                <div>
                                    <Label>Nro Documento (CI/NIT)</Label>
                                    <TextInput type="text" v-model="pfDocumentId" placeholder="Ej: 1234567" />
                                </div>
                                <div>
                                    <Label>Teléfono</Label>
                                    <TextInput type="text" v-model="pfPhoneNumber" placeholder="70000000" />
                                </div>
                                <div>
                                    <Label>Email</Label>
                                    <TextInput type="email" v-model="pfEmail" placeholder="correo@ejemplo.com" />
                                </div>
                                <div>
                                    <Label>ID Transacción Empresa</Label>
                                    <TextInput type="text" v-model="pfPaymentNumber" :placeholder="`plan-${plan.id}-cuota-${form.numerocuota}`" />
                                </div>
                                <div>
                                    <Label>Monto</Label>
                                    <TextInput type="number" step="0.01" v-model="pfAmount" />
                                </div>
                                <div>
                                    <Label>Currency (2 = BOB)</Label>
                                    <TextInput type="number" v-model="pfCurrency" placeholder="2" />
                                </div>
                                <div>
                                    <Label>ID Cliente Empresa</Label>
                                    <TextInput type="text" v-model="pfClientCode" placeholder="Opcional" />
                                </div>
                                
                            </div>

                            <div class="border-t border-input pt-3">
                                <h4 class="font-medium text-sm text-muted-foreground mb-2">Detalle de Orden</h4>
                                <div class="grid md:grid-cols-3 gap-3">
                                    <div>
                                        <Label>Serial</Label>
                                        <TextInput type="number" v-model="pfOrderSerial" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <Label>Producto</Label>
                                        <TextInput type="text" v-model="pfOrderProduct" :placeholder="`Pago Plan #${plan.id}`" />
                                    </div>
                                    <div>
                                        <Label>Cantidad</Label>
                                        <TextInput type="number" v-model="pfOrderQuantity" />
                                    </div>
                                    <div>
                                        <Label>Precio</Label>
                                        <TextInput type="number" step="0.01" v-model="pfOrderPrice" />
                                    </div>
                                    <div>
                                        <Label>Descuento</Label>
                                        <TextInput type="number" step="0.01" v-model="pfOrderDiscount" />
                                    </div>
                                    <div>
                                        <Label>Total</Label>
                                        <TextInput type="number" step="0.01" v-model="pfOrderTotal" />
                                    </div>
                                </div>
                            </div>

                            <div v-if="pfQrBase64" class="border-t pt-3">
                                <Label>QR para pagar</Label>
                                <div class="mt-2 flex justify-center">
                                    <img :src="`data:image/png;base64,${pfQrBase64}`" alt="QR Pago Fácil" class="max-w-xs border rounded shadow-sm" />
                                </div>
                            </div>
                        </div>

                        <div v-if="esTarjeta" class="md:col-span-2 border border-input rounded-lg p-4 bg-muted space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-foreground">Pago con Tarjeta</h3>
                                <span v-if="stripeLoading" class="text-sm text-blue-600">Procesando...</span>
                            </div>
                            <div v-if="stripeError" class="p-3 bg-destructive/15 border border-destructive/40 rounded text-sm text-destructive">
                                {{ stripeError }}
                            </div>
                            <p class="text-sm text-muted-foreground">
                                Introduce los datos de tu tarjeta de forma segura.
                            </p>
                            <div id="stripe-card-element" class="px-3 py-2 border rounded-md bg-background"></div>
                        </div>

                        <div>
                            <Label>Monto</Label>
                            <TextInput type="number" step="0.01" v-model="form.monto" />
                            <InputError :message="form.errors.monto" />
                        </div>

                        <div>
                            <Label>Número de Cuota</Label>
                            <TextInput type="number" min="1" v-model="form.numerocuota" />
                            <InputError :message="form.errors.numerocuota" />
                        </div>

                        <div class="md:col-span-2">
                            <Label>Referencia</Label>
                            <TextInput type="text" v-model="form.referencia" />
                            <InputError :message="form.errors.referencia" />
                        </div>

                        <div class="md:col-span-2 flex gap-3 flex-wrap">
                            <Button :disabled="form.processing" type="submit">Guardar Pago</Button>
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(route('plan-pagos.show', plan.id))"
                            >
                                Cancelar
                            </Button>
                            <Button
                                v-if="esPagoFacil"
                                type="button"
                                variant="outline"
                                :disabled="form.processing || loadingPF"
                                @click="obtenerTokenPagoFacil"
                            >
                                {{ pfToken ? 'Renovar Token' : 'Obtener Token' }}
                            </Button>
                            <Button
                                v-if="esPagoFacil && selectedPaymentMethodId"
                                type="button"
                                variant="default"
                                :disabled="form.processing || !selectedPaymentMethodId || !pfClientName"
                                @click="generarQrPF"
                            >
                                Generar QR
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
