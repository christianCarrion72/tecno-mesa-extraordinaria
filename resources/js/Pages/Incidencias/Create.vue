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

                        <!-- Descripción -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Descripción</Label>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <input 
                                    type="text" 
                                    v-model="form.descripcion" 
                                    placeholder="Detalle de la incidencia"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    :class="{ 'border-red-500': form.errors.descripcion }"
                                />
                            </div>
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <!-- Fecha -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Fecha</Label>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <input 
                                    type="date" 
                                    v-model="form.fecha"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    :class="{ 'border-red-500': form.errors.fecha }"
                                />
                            </div>
                            <InputError :message="form.errors.fecha" />
                        </div>

                        <!-- Estado -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-700">Estado</Label>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <select 
                                    v-model="form.estado" 
                                    class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white transition-colors"
                                >
                                    <option value="registrada">Registrada</option>
                                    <option value="en revisión">En Revisión</option>
                                    <option value="resuelta">Resuelta</option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-4 pt-2">
                            <Button 
                                :disabled="form.processing" 
                                :style="{
                                    backgroundColor: form.processing ? 'var(--color-muted)' : 'var(--color-primary)',
                                    color: form.processing ? 'var(--color-text)' : 'white',
                                    cursor: form.processing ? 'not-allowed' : 'pointer',
                                }"
                                type="submit"
                                class="px-6 py-2.5 rounded-lg font-medium transition-all hover:opacity-90"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Registrar
                            </Button>

                            <Button 
                                type="button" 
                                variant="outline"
                                class="px-6 py-2.5 rounded-lg font-medium"
                                @click="$inertia.visit(route('orden-trabajos.show', orden.id))"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancelar
                            </Button>
                        </div>

                    </form>
                </CardContent>
            </Card>

        </div>
    </AppLayout>
</template>
