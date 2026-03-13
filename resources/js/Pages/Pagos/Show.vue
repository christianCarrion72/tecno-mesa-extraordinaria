<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { computed, ref } from 'vue';

const props = defineProps<{ pago: any }>();

const descargarPdf = () => {
  window.print();
};

const metodoNormalizado = computed(() =>
  String(props.pago.metodopago || '').toLowerCase().trim(),
);

const esPagoFacil = computed(
  () => metodoNormalizado.value === 'pago facil' || metodoNormalizado.value === 'pago fácil',
);

const esTarjeta = computed(() => metodoNormalizado.value === 'tarjeta');
const esEfectivo = computed(() => metodoNormalizado.value === 'efectivo');

const getCookie = (name: string): string => {
  const match = document.cookie.split('; ').find((r) => r.startsWith(name + '='));
  return match ? decodeURIComponent(match.split('=')[1]) : '';
};

const csrfToken =
  (document.querySelector('meta[name=\"csrf-token\"]') as HTMLMetaElement | null)?.content || '';
const xsrfToken = getCookie('XSRF-TOKEN');

const consultandoPagoFacil = ref(false);

const consultarEstadoPagoFacil = async () => {
  if (consultandoPagoFacil.value) {
    return;
  }
  consultandoPagoFacil.value = true;
  try {
    const res = await fetch(route('pagofacil.query'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
        ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        pago_id: props.pago.id,
      }),
    });
    const data = await res.json();
    if (!res.ok || data?.error) {
      console.error(data);
      alert(data?.message || 'No se pudo consultar el estado del pago.');
      return;
    }
    alert(data?.message || 'Estado del pago actualizado correctamente.');
    window.location.reload();
  } catch (e) {
    console.error('Error consultando estado PagoFacil', e);
    alert('Error consultando el estado del pago.');
  } finally {
    consultandoPagoFacil.value = false;
  }
};
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
          </div>

          <div v-if="esPagoFacil" class="mt-6 border-t pt-4 space-y-3">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-muted-foreground">Detalles Pago Fácil</h3>
              <Button
                type="button"
                size="sm"
                variant="outline"
                :disabled="consultandoPagoFacil"
                @click="consultarEstadoPagoFacil"
              >
                {{ consultandoPagoFacil ? 'Consultando...' : 'Consultar estado' }}
              </Button>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
              <div v-if="pago.pf_transaction_id">
                <div class="text-sm text-muted-foreground">ID Transacción PagoFácil</div>
                <div class="font-medium break-all">{{ pago.pf_transaction_id }}</div>
              </div>
              <div v-if="pago.pf_payment_method_transaction_id">
                <div class="text-sm text-muted-foreground">ID Transacción Empresa</div>
                <div class="font-medium break-all">{{ pago.pf_payment_method_transaction_id }}</div>
              </div>
              <div v-if="pago.pf_status !== null && pago.pf_status !== undefined">
                <div class="text-sm text-muted-foreground">Estado PagoFácil</div>
                <div class="font-medium">{{ pago.pf_status }}</div>
              </div>
              <div v-if="pago.pf_expiration_date">
                <div class="text-sm text-muted-foreground">Vence</div>
                <div class="font-medium">{{ pago.pf_expiration_date }}</div>
              </div>
            </div>
            <div v-if="pago.pf_qr_base64" class="mt-4">
              <div class="text-sm text-muted-foreground mb-2">QR generado por PagoFácil</div>
              <img
                :src="`data:image/png;base64,${pago.pf_qr_base64}`"
                alt="QR Pago Fácil"
                class="max-w-xs border rounded shadow-sm"
              />
            </div>
          </div>

          <div v-else-if="esTarjeta" class="mt-6 border-t pt-4 space-y-3">
            <h3 class="text-sm font-semibold text-muted-foreground">Detalles pago con tarjeta (Stripe)</h3>
            <div class="grid gap-4 md:grid-cols-2">
              <div v-if="pago.pf_transaction_id">
                <div class="text-sm text-muted-foreground">Stripe PaymentIntent ID</div>
                <div class="font-medium break-all">{{ pago.pf_transaction_id }}</div>
              </div>
              <div v-if="pago.pf_payment_method_transaction_id">
                <div class="text-sm text-muted-foreground">Stripe PaymentMethod ID</div>
                <div class="font-medium break-all">{{ pago.pf_payment_method_transaction_id }}</div>
              </div>
            </div>
          </div>

          <div v-else-if="esEfectivo" class="mt-6 border-t pt-4">
            <h3 class="text-sm font-semibold text-muted-foreground">Detalles pago en efectivo</h3>
            <p class="text-sm text-muted-foreground">
              Pago registrado como efectivo sin información adicional de pasarela.
            </p>
          </div>

          <div class="mt-6 flex gap-3">
            <Button
              type="button"
              variant="outline"
              :style="{ color: 'var(--color-text)' }"
              @click="descargarPdf"
            >
              Descargar PDF
            </Button>
          </div>

        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
