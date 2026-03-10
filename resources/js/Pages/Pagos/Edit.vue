<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Combobox from '@/Components/ui/Combobox.vue';

const props = defineProps<{ pago: any; metodos: string[] }>();

const form = useForm({
    estado: props.pago.estado ?? 'pendiente',
    fechapago: props.pago.fechapago ?? '',
    metodopago: props.pago.metodopago ?? props.metodos[0] ?? 'efectivo',
    monto: props.pago.monto != null ? String(props.pago.monto) : '0',
    numerocuota: props.pago.numerocuota != null ? String(props.pago.numerocuota) : '1',
    referencia: props.pago.referencia ?? '',
});

const submit = () => {
    form.put(route('plan-pagos.pagos.update', props.pago.id));
};
</script>

<template>
    <Head :title="`Editar Pago #${pago.id}`" />

    <AppLayout>
        <div class="p-4 max-w-3xl">
            <Card>
                <CardHeader>
                    <CardTitle>Editar Pago #{{ pago.id }}</CardTitle>
                    <CardDescription>Plan #{{ pago.plan_pago_id }} · Orden #{{ pago.plan_pago?.orden_trabajo_id }}</CardDescription>
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
                            <Combobox v-model="form.metodopago" :items="metodos.map(m => ({ id: m, label: m }))" />
                            <InputError :message="form.errors.metodopago" />
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

                        <div class="md:col-span-2 flex gap-4">
                            <Button
                                :disabled="form.processing"
                                type="submit"
                                :style="{ backgroundColor: 'var(--color-primary)', color: 'var(--color-base)' }"
                            >
                                Guardar Cambios
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(route('plan-pagos.show', pago.plan_pago_id))"
                            >
                                Cancelar
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
