<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Combobox from '@/Components/ui/Combobox.vue';

const props = defineProps<{ orden: any }>();

const estados = ['pendiente', 'en proceso', 'terminado'];

const form = useForm({
    estado: 'pendiente',
    fechainicio: '',
    fechafin: '',
    montoporcuota: '',
    numerocuotas: '1',
    observacion: '',
});

const montoTotalOrden = props.orden?.total ?? 0;

const calcularMontoPorCuota = () => {
    const cuotas = Number(form.numerocuotas) || 1;
    const monto = Number(montoTotalOrden) || 0;
    form.montoporcuota = (monto / cuotas).toFixed(2);
};

calcularMontoPorCuota();

watch(
    () => form.numerocuotas,
    () => {
        calcularMontoPorCuota();
    }
);

const submit = () => {
    form.post(route('plan-pagos.store', props.orden.id));
};
</script>

<template>
    <Head title="Crear Plan de Pagos" />

    <AppLayout>
        <div class="p-4 max-w-3xl">
            <Card>
                <CardHeader>
                    <CardTitle>Plan de Pagos para Orden #{{ orden.id }}</CardTitle>
                    <CardDescription>Complete los datos del plan</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label>Estado</Label>
                            <Combobox v-model="form.estado" :items="estados.map(e => ({ id: e, label: e }))" />
                            <InputError :message="form.errors.estado" />
                        </div>

                        <div>
                            <Label>Fecha Inicio</Label>
                            <TextInput type="date" v-model="form.fechainicio" />
                            <InputError :message="form.errors.fechainicio" />
                        </div>

                        <div>
                            <Label>Fecha Fin</Label>
                            <TextInput type="date" v-model="form.fechafin" />
                            <InputError :message="form.errors.fechafin" />
                        </div>

                        <div>
                            <Label>Monto por Cuota</Label>
                            <TextInput
                                type="number"
                                step="0.01"
                                v-model="form.montoporcuota"
                                readonly
                            />
                            <InputError :message="form.errors.montoporcuota" />
                            <p class="text-xs text-muted-foreground">
                                Se calcula automáticamente según el total de la orden y el número de cuotas.
                            </p>
                        </div>

                        <div>
                            <Label>Número de Cuotas</Label>
                            <TextInput type="number" min="1" v-model="form.numerocuotas" />
                            <InputError :message="form.errors.numerocuotas" />
                        </div>

                        <div>
                            <Label>Monto Total (de la Orden)</Label>
                            <div class="border rounded-md px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800">
                                {{ montoTotalOrden }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Este valor se toma de la Orden de Trabajo y no se edita aquí.
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <Label>Observación</Label>
                            <TextInput type="text" v-model="form.observacion" />
                            <InputError :message="form.errors.observacion" />
                        </div>

                        <div class="md:col-span-2 flex gap-4">
                            <Button :disabled="form.processing" type="submit">Crear Plan</Button>
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(route('orden-trabajos.show', orden.id))"
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
