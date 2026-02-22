<script setup lang="ts">
declare function route(name: string, params?: any): string;
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem, type Usuario, type Rol } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import TextInput from '@/Components/TextInput.vue';
import { Label } from '@/Components/ui/label';
import InputError from '@/Components/InputError.vue';
import { computed, onUnmounted, ref, watch } from 'vue';

interface Props {
    usuario: Usuario;
    roles: Rol[];
}

const props = defineProps<Props>();

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
        title: 'Editar Usuario',
        href: route('admin.usuarios.edit', props.usuario.id),
    },
];

const form = useForm({
    name: props.usuario.name,
    email: props.usuario.email,
    password: '',
    telefono: props.usuario.telefono || '',
    direccion: props.usuario.direccion || '',
    // tipo: props.usuario.tipo,
    rol_id: props.usuario.rol_id.toString(),
    foto: null as File | null,
});

const onFotoChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0] || null;
    form.foto = file;
};

const currentFotoUrl = computed(() =>
    props.usuario.foto ? `/storage/${props.usuario.foto}` : null,
);

const newPreviewUrl = ref<string | null>(null);
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
            newPreviewUrl.value = previousObjectUrl;
        } else {
            newPreviewUrl.value = null;
        }
    },
);

onUnmounted(() => {
    if (previousObjectUrl) URL.revokeObjectURL(previousObjectUrl);
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
        rol_id: typeof data.rol_id === 'string' ? parseInt(data.rol_id) : data.rol_id,
    }));
    form.post(route('admin.usuarios.update', props.usuario.id), { forceFormData: true });
};
</script>

<template>
    <Head title="Editar Usuario" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Editar Usuario</CardTitle>
                    <CardDescription>
                        Modifique los datos del usuario "{{ usuario.name }}"
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
                            <div class="mt-2 flex items-center gap-4">
                                <div v-if="currentFotoUrl">
                                    <img :src="currentFotoUrl!" alt="Actual" class="h-20 w-20 rounded-md object-cover border" />
                                </div>
                                <div v-if="newPreviewUrl">
                                    <img :src="newPreviewUrl!" alt="Nueva" class="h-20 w-20 rounded-md object-cover border" />
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">Contraseña</Label>
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                placeholder="Dejar en blanco para mantener la actual"
                                :disabled="form.processing"
                            />
                            <p class="text-xs text-muted-foreground">Mínimo 8 caracteres si desea cambiarla</p>
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="telefono">Teléfono</Label>
                                <TextInput
                                    id="telefono"
                                    v-model="form.telefono"
                                    type="text"
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
                                :style="{
                                    backgroundColor: 'var(--color-primary)',
                                    color: 'white',
                                    ':hover': { backgroundColor: 'var(--color-primary)', opacity: '0.9' }
                                }"
                            >
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Usuario' }}
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
                                    Actualizado.
                                </p>
                            </Transition>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
