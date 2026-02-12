<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { ref, computed, onMounted } from 'vue';

const props = defineProps<{ pago: any }>();

const getCookie = (name: string): string => {
  const match = document.cookie.split('; ').find(r => r.startsWith(name + '='));
  return match ? decodeURIComponent(match.split('=')[1]) : '';
};
const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';
const xsrfToken = getCookie('XSRF-TOKEN');

const pfToken = ref<string>('');
const pfError = ref<string>('');
const consulta = ref<any | null>(null);
const serviciosPF = ref<Array<{ paymentMethodId: number; paymentMethodName: string }>>([]);

const obtenerTokenPagoFacil = async (): Promise<string | null> => {
  try {
    const res = await fetch('/pagofacil/login');
    const data = await res.json();
    const token = data?.values?.accessToken ?? '';
    pfToken.value = token;
    pfError.value = data?.error ? (data?.message || 'Error en login PagoFacil') : '';
    return token || null;
  } catch (e) {
    pfError.value = 'Error obteniendo token PagoFacil';
    return null;
  }
};

const consultarTransaccion = async () => {
  try {
    if (!pfToken.value) {
      await obtenerTokenPagoFacil();
    }
    // Cargar servicios para mapear paymentMethodId a nombre legible
    try {
      const resSvc = await fetch('/pagofacil/list-enabled-services', {
        headers: pfToken.value ? { Authorization: `Bearer ${pfToken.value}` } : {},
      });
      const svcJson = await resSvc.json();
      serviciosPF.value = Array.isArray(svcJson.values) ? svcJson.values : [];
    } catch {}
    if (!props.pago?.pf_transaction_id) {
      pfError.value = 'Este pago no tiene pagofacilTransactionId registrado.';
      return;
    }
    const payload = {
      pago_id: props.pago.id,
      pagofacilTransactionId: props.pago.pf_transaction_id,
    };
    const res = await fetch('/pagofacil/query-transaction', {
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
    const data = await res.json();
    console.log('PagoFacil query-transaction response:', data);
    consulta.value = data;
    if (data?.error) {
      pfError.value = data?.message || 'Error consultando transacción';
    } else {
      pfError.value = '';
    }
  } catch (e) {
    pfError.value = 'Error consultando transacción';
  }
};

const metodoNombre = (id?: number) => {
  if (!id) return '';
  const m = serviciosPF.value.find(s => Number(s.paymentMethodId) === Number(id));
  return m?.paymentMethodName || String(id);
};

const currencyLabel = (id?: number) => {
  if (id === 2) return 'BOB';
  if (id === 1) return 'USD';
  return String(id ?? '');
};

const statusLabel = computed(() => {
  const s = consulta.value?.values?.paymentStatus;
  if (s === 1) return 'Pagado';
  if (s === 0) return 'Pendiente';
  if (s === 2) return 'En proceso';
  if (s === 3) return 'Anulado';
  return typeof s !== 'undefined' ? String(s) : '';
});

const descargarPdf = () => {
  window.print();
};

onMounted(() => {
  consultarTransaccion();
});
</script>

<template>
  <Head :title="`Pago #${pago.id}`" />

  <AppLayout>
    <div class="p-4 max-w-3xl">
      <Card>
        <CardHeader>
          <CardTitle>Detalle del Pago</CardTitle>
          <CardDescription>
            Plan #{{ pago.plan_pago_id }} · Método: {{ pago.metodopago }}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <div class="text-sm text-muted-foreground">Estado</div>
              <div class="font-medium">{{ pago.estado }}</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">Fecha</div>
              <div class="font-medium">{{ pago.fechapago }}</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">Monto</div>
              <div class="font-medium">{{ pago.monto }}</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">Cuota</div>
              <div class="font-medium">{{ pago.numerocuota }}</div>
            </div>
            <div v-if="pago.referencia">
              <div class="text-sm text-muted-foreground">Referencia</div>
              <div class="font-medium">{{ pago.referencia }}</div>
            </div>
            <div v-if="pago.pf_transaction_id">
              <div class="text-sm text-muted-foreground">PagoFacil TransactionId</div>
              <div class="font-medium">{{ pago.pf_transaction_id }}</div>
            </div>
            <div v-if="pago.pf_payment_method_transaction_id">
              <div class="text-sm text-muted-foreground">PaymentMethodTransactionId</div>
              <div class="font-medium">{{ pago.pf_payment_method_transaction_id }}</div>
            </div>
          </div>

          <div v-if="pago.pf_qr_base64" class="mt-6">
            <div class="text-sm text-muted-foreground">QR para pagar</div>
            <div class="mt-2 flex justify-center">
              <img :src="`data:image/png;base64,${pago.pf_qr_base64}`" alt="QR Pago Fácil" class="max-w-xs border rounded shadow-sm" />
            </div>
          </div>

          <div class="mt-6 flex gap-3">
            <Button type="button" variant="default" @click="consultarTransaccion">Consultar estado</Button>
            <Button type="button" variant="outline" @click="obtenerTokenPagoFacil">{{ pfToken ? 'Renovar Token' : 'Obtener Token' }}</Button>
            <Button type="button" variant="outline" @click="descargarPdf">Descargar PDF</Button>
          </div>

          <div v-if="pfError" class="mt-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-800">{{ pfError }}</div>

          <div v-if="consulta && !consulta.error" class="mt-6 border-t pt-4">
            <div class="text-sm text-muted-foreground mb-2">Estado de la transacción</div>
            <div class="grid md:grid-cols-2 gap-3">
              <div>
                <div class="text-xs text-muted-foreground">Estado</div>
                <div class="font-medium">{{ statusLabel }}</div>
              </div>
              <div>
                <div class="text-xs text-muted-foreground">Método</div>
                <div class="font-medium">{{ metodoNombre(consulta.values?.paymentMethodId) }}</div>
              </div>
              <div>
                <div class="text-xs text-muted-foreground">Monto</div>
                <div class="font-medium">{{ consulta.values?.amount }}</div>
              </div>
              <div>
                <div class="text-xs text-muted-foreground">Moneda</div>
                <div class="font-medium">{{ currencyLabel(consulta.values?.currencyId) }}</div>
              </div>
              <div>
                <div class="text-xs text-muted-foreground">Fecha de pago</div>
                <div class="font-medium">{{ consulta.values?.paymentDate }}</div>
              </div>
              <div>
                <div class="text-xs text-muted-foreground">Hora de pago</div>
                <div class="font-medium">{{ consulta.values?.paymentTime }}</div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
