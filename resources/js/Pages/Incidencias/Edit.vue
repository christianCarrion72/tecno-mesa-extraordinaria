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
    incidencia: Object,
});

const form = useForm({
    descripcion: props.incidencia.descripcion,
    fecha: props.incidencia.fecha,
    estado: props.incidencia.estado,
});

const submit = () => {
    form.put(route('incidencias.update', props.incidencia.id), {
        onStart: () => console.debug('[Incidencia] update start', { id: props.incidencia?.id }),
        onSuccess: () => console.debug('[Incidencia] updated'),
        onError: (errors) => console.warn('[Incidencia] update errors', errors),
        onFinish: () => console.debug('[Incidencia] update finished'),
    });
};
</script>

<template>
    <Head title="Editar Incidencia" />

    <AppLayout>
        <div class="p-4 max-w-3xl">

            <Card>
                <CardHeader>
                    <CardTitle>Editar Incidencia</CardTitle>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-5">

                        <div>
                            <Label>Descripción</Label>
                            <TextInput type="text" v-model="form.descripcion" />
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
                                Actualizar
                            </Button>

                            <Button type="button" variant="outline"
                                @click="$inertia.visit(route('orden-trabajos.show', props.incidencia.orden_trabajo_id))">
                                Cancelar
                            </Button>
                        </div>

                    </form>
                </CardContent>
            </Card>

        </div>
    </AppLayout>
</template>
