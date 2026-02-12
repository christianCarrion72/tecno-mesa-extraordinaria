<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    orden: Object,
});

const form = useForm({
    descripcion: '',
    fecha: '',
    // default must match DB enum values
    estado: 'registrada',
});

const submit = () => {
    form.post(route('incidencias.store', { ordenTrabajo: props.orden.id }), {
        onStart: () => console.debug('[Incidencia] submit start', { orden: props.orden?.id }),
        onSuccess: () => console.debug('[Incidencia] created — redirecting back'),
        onError: (errors) => console.warn('[Incidencia] validation errors', errors),
        onFinish: () => console.debug('[Incidencia] submit finished'),
    });
};
</script>

<template>
    <Head title="Registrar Incidencia" />

    <AppLayout>
        <div class="p-4 max-w-3xl">

            <Card>
                <CardHeader>
                    <CardTitle>Registrar Incidencia</CardTitle>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-5">

                        <div>
                            <Label>Descripción</Label>
                            <TextInput type="text" v-model="form.descripcion" placeholder="Detalle de la incidencia" />
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <div>
                            <Label>Fecha</Label>
                            <TextInput type="date" v-model="form.fecha" />
                            <InputError :message="form.errors.fecha" />
                        </div>

                        <div>
                            <Label>Estado</Label>
                            <select v-model="form.estado" class="border rounded p-2">
                                <option value="registrada">Registrada</option>
                                <option value="en revisión">En Revisión</option>
                                <option value="resuelta">Resuelta</option>
                            </select>
                        </div>

                        <div class="flex gap-4">
                            <Button :disabled="form.processing" type="submit">
                                Registrar
                            </Button>

                            <Button type="button" variant="outline"
                                @click="$inertia.visit(route('orden-trabajos.show', orden.id))">
                                Cancelar
                            </Button>
                        </div>

                    </form>
                </CardContent>
            </Card>

        </div>
    </AppLayout>
</template>
