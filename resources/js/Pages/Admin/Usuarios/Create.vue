<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem, type Rol } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { onUnmounted, ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import TextInput from '@/Components/TextInput.vue';
import { Label } from '@/Components/ui/label';
import InputError from '@/Components/InputError.vue';

interface Props {
    roles: Rol[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
    },
    {
        title: 'Usuarios',
        href: route('admin.usuarios.index'),
    },
    {
        title: 'Nuevo Usuario',
        href: route('admin.usuarios.create'),
    },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    telefono: undefined,
    direccion: '',
    // tipo: '',
    rol_id: '' as string,
    foto: null as File | null,
});

const onFotoChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0] || null;
    form.foto = file;
};

const previewUrl = ref<string | null>(null);
let previousObjectUrl: string | null = null;

watch(
    () => form.foto,
    (file) => {
        if (previousObjectUrl) {
            URL.revokeObjectURL(previousObjectUrl);
            previousObjectUrl = null;
        }
        if (typeof window !== 'undefined' && file instanceof File) {
            previousObjectUrl = URL.createObjectURL(file);
            previewUrl.value = previousObjectUrl;
        } else {
            previewUrl.value = null;
        }
    },
);

onUnmounted(() => {
    if (previousObjectUrl) URL.revokeObjectURL(previousObjectUrl);
});

const submit = () => {
    form.post(route('admin.usuarios.store'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Nuevo Usuario" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Crear Nuevo Usuario</CardTitle>
                    <CardDescription>
                        Complete el formulario para registrar un nuevo usuario
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">Nombre</Label>
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Nombre completo"
                                    required
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="correo@ejemplo.com"
                                    required
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="foto">Foto (Opcional)</Label>
                            <input
                                id="foto"
                                type="file"
                                accept="image/*"
                                @change="onFotoChange"
                                :disabled="form.processing"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError :message="form.errors.foto" class="mt-2" />
                            <div v-if="previewUrl" class="mt-2">
                                <img :src="previewUrl!" alt="Preview" class="h-20 w-20 rounded-md object-cover border" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                                <Label for="password">Contraseña</Label>
                                <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                placeholder="Mínimo 8 caracteres"
                                required
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="telefono">Teléfono</Label>
                                <TextInput
                                    id="telefono"
                                    v-model="form.telefono"
                                    type="number"
                                    placeholder="+591 12345678"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.telefono" class="mt-2" />
                            </div>

                            <!-- <div class="grid gap-2">
                                <Label for="tipo">Tipo</Label>
                                <Input
                                    id="tipo"
                                    v-model="form.tipo"
                                    type="text"
                                    placeholder="Ej: Administrador, Vendedor, etc."
                                    required
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.tipo" class="mt-2" />
                            </div> -->
                        </div>

                        <div class="grid gap-2">
                            <Label for="direccion">Dirección</Label>
                            <textarea
                                id="direccion"
                                v-model="form.direccion"
                                placeholder="Dirección completa"
                                :disabled="form.processing"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError :message="form.errors.direccion" class="mt-2" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="rol_id">Rol</Label>
                            <select
                                id="rol_id"
                                v-model="form.rol_id"
                                required
                                :disabled="form.processing"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="" disabled>Seleccione un rol</option>
                                <option 
                                    v-for="rol in roles" 
                                    :key="rol.id" 
                                    :value="rol.id"
                                >
                                    {{ rol.nombre }}
                                </option>
                            </select>
                            <InputError :message="form.errors.rol_id" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Guardando...' : 'Guardar Usuario' }}
                            </Button>
                            
                            <Button 
                                type="button" 
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(route('admin.usuarios.index'))"
                            >
                                Cancelar
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-show="form.recentlySuccessful"
                                    class="text-sm text-muted-foreground"
                                >
                                    Guardado.
                                </p>
                            </Transition>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
