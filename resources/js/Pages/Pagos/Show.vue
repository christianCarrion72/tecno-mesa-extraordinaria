<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

const props = defineProps<{ pago: any }>();

const descargarPdf = () => {
  window.print();
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
            <div v-if="pago.pf_transaction_id">
              <div class="text-sm text-muted-foreground">Stripe PaymentIntent ID</div>
              <div class="font-medium">{{ pago.pf_transaction_id }}</div>
            </div>
            <div v-if="pago.pf_payment_method_transaction_id">
              <div class="text-sm text-muted-foreground">Stripe PaymentMethod ID</div>
              <div class="font-medium">{{ pago.pf_payment_method_transaction_id }}</div>
            </div>
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
