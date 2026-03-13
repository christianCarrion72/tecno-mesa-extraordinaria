<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { computed } from 'vue';

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
            <h3 class="text-sm font-semibold text-muted-foreground">Detalles Pago Fácil</h3>
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
