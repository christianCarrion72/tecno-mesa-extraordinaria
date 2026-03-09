<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps<{
    token: string;
    monto: number;
    numerocuota: number;
    currency: string;
    stripePublicKey: string;
    clientSecret: string;
    confirmUrl: string;
}>();

const stripe = ref<any | null>(null);
const cardElement = ref<any | null>(null);
const loading = ref(false);
const error = ref<string>('');
const success = ref(false);

const csrfToken = (document.querySelector('meta[name=\"csrf-token\"]') as HTMLMetaElement)?.content || '';

const initStripe = () => {
    const anyWindow = window as any;
    if (!anyWindow.Stripe) {
        error.value = 'Stripe.js no está disponible.';
        return;
    }
    stripe.value = anyWindow.Stripe(props.stripePublicKey);
    const elements = stripe.value.elements();
    const card = elements.create('card');
    card.mount('#stripe-card-element');
    cardElement.value = card;
};

onMounted(() => {
    initStripe();
});

const pagar = async () => {
    if (!stripe.value || !cardElement.value) {
        error.value = 'Stripe no está listo.';
        return;
    }

    error.value = '';
    loading.value = true;

    try {
        const result = await stripe.value.confirmCardPayment(props.clientSecret, {
            payment_method: { card: cardElement.value },
        });

        if (result.error) {
            error.value = result.error.message || 'Error procesando la tarjeta.';
            return;
        }

        if (result.paymentIntent.status !== 'succeeded') {
            error.value = 'El pago no se completó (status: ' + result.paymentIntent.status + ').';
            return;
        }

        const res = await fetch(props.confirmUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                payment_intent_id: result.paymentIntent.id,
            }),
        });

        const data = await res.json();
        if (!res.ok || !data.success) {
            error.value = data.message || 'No se pudo registrar el pago.';
            return;
        }

        success.value = true;
        if (data.redirect) {
            window.location.href = data.redirect;
        }
    } catch (e) {
        console.error(e);
        error.value = 'Error inesperado al procesar el pago.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Head title="Pago con Tarjeta" />
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="w-full max-w-md bg-white rounded-lg shadow p-6 space-y-4">
            <h1 class="text-xl font-semibold text-center">Pago con Tarjeta</h1>
            <p class="text-sm text-center text-gray-600">
                Monto: {{ monto }} {{ currency }} · Cuota #{{ numerocuota }}
            </p>

            <div v-if="error" class="p-3 bg-red-50 border border-red-200 text-sm text-red-700 rounded">
                {{ error }}
            </div>

            <div v-if="success" class="p-3 bg-green-50 border border-green-200 text-sm text-green-700 rounded">
                Pago realizado con éxito.
            </div>

            <div id="stripe-card-element" class="px-3 py-2 border rounded-md bg-gray-50"></div>

            <button
                class="w-full mt-4 py-2 px-4 rounded bg-blue-600 text-white disabled:opacity-50"
                :disabled="loading || success"
                @click="pagar"
            >
                {{ loading ? 'Procesando...' : 'Pagar' }}
            </button>
        </div>
    </div>
</template>
