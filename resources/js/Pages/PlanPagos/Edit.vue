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

const props = defineProps<{ plan: any }>();

const estados = ['pendiente', 'en proceso', 'terminado'];

const form = useForm({
    estado: props.plan.estado ?? 'pendiente',
    fechainicio: props.plan.fechainicio ?? '',
    fechafin: props.plan.fechafin ?? '',
    montoporcuota: props.plan.montoporcuota != null ? String(props.plan.montoporcuota) : '',
    numerocuotas: props.plan.numerocuotas != null ? String(props.plan.numerocuotas) : '1',
    observacion: props.plan.observacion ?? '',
});

const submit = () => {
    form.put(route('plan-pagos.update', props.plan.id));
};

const montoTotalOrden = props.plan?.orden_trabajo?.total ?? props.plan.montototal;
</script>

<template>
    <Head title="Editar Plan de Pagos" />

    <AppLayout>
        <div class="p-4 max-w-3xl">
            <Card>
                <CardHeader>
                    <CardTitle>Editar Plan #{{ plan.id }}</CardTitle>
                    <CardDescription>Actualice los datos del plan</CardDescription>
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
                            <TextInput type="number" step="0.01" v-model="form.montoporcuota" />
                            <InputError :message="form.errors.montoporcuota" />
                        </div>

                        <div>
                            <Label>Número de Cuotas</Label>
                            <TextInput type="number" min="1" v-model="form.numerocuotas" />
                            <InputError :message="form.errors.numerocuotas" />
                        </div>

                        <div class="md:col-span-2">
                            <Label>Monto Total (de la Orden)</Label>
                            <div class="border rounded-md px-3 py-2 text-sm text-muted-foreground bg-gray-50">
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
                            <Button :disabled="form.processing">Guardar Cambios</Button>
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(route('plan-pagos.index'))"
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
