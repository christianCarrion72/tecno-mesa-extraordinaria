<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps<{ pago: any }>();

const today = new Date();
const pad = (n: number) => String(n).padStart(2, '0');
const localDate = `${today.getFullYear()}-${pad(today.getMonth() + 1)}-${pad(today.getDate())}`;

const form = useForm({
    descripcion: '',
    estado: 'emitida',
    fechaemision: localDate,
    montototal: props.pago.monto ?? 0,
    nroautorizacion: '',
    numerofactura: '',
});

const submit = () => {
    form.post(route('facturas.store', props.pago.id));
};
</script>

<template>
    <Head :title="`Emitir Factura del Pago #${pago.id}`" />

    <AppLayout>
        <div class="p-4 max-w-3xl">
            <Card>
                <CardHeader>
                    <CardTitle>Emitir Factura</CardTitle>
                    <CardDescription>Plan #{{ pago.plan_pago_id }} · Pago #{{ pago.id }}</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <Label>Descripción</Label>
                            <TextInput type="text" v-model="form.descripcion" />
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <div>
                            <Label>Estado</Label>
                            <TextInput type="text" v-model="form.estado" />
                            <InputError :message="form.errors.estado" />
                        </div>

                        <div>
                            <Label>Fecha de Emisión</Label>
                            <TextInput type="date" v-model="form.fechaemision" />
                            <InputError :message="form.errors.fechaemision" />
                        </div>

                        <div>
                            <Label>Monto Total</Label>
                            <TextInput
                                type="number"
                                step="0.01"
                                v-model="form.montototal"
                                readonly
                            />
                            <InputError :message="form.errors.montototal" />
                        </div>

                        <div>
                            <Label>Nro. Autorización</Label>
                            <TextInput type="text" v-model="form.nroautorizacion" />
                            <InputError :message="form.errors.nroautorizacion" />
                        </div>

                        <div>
                            <Label>Número de Factura</Label>
                            <TextInput type="text" v-model="form.numerofactura" />
                            <InputError :message="form.errors.numerofactura" />
                        </div>

                        <div class="md:col-span-2 flex gap-4">
                            <Button
                                :style="{ backgroundColor: 'var(--color-primary)', color: 'var(--color-base)' }"
                                :disabled="form.processing"
                                type="submit"
                            >
                                Emitir
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
