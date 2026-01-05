<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Search, Layers, Trash2, Plus, Pencil } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import GenericQuickAdd from '@/components/QuickAdd/GenericQuickAdd.vue';
import { route } from 'ziggy-js';
import { ref } from 'vue';

const props = defineProps<{
    models: Array<{
        id: number;
        nombre: string;
        marca: {
            id: number;
            nombre: string;
        };
        tipo_activo?: {
            id: number;
            nombre: string;
        };
    }>;
}>();

const deleteModel = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este modelo?')) {
        router.delete(route('models.destroy', id));
    }
};

const onModelAdded = () => {
    router.reload(); // Simple reload to refresh list
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Inicio', href: route('dashboard') }, { title: 'Configuración', href: '#' }, { title: 'Modelos', href: '#' }]">
        <Head title="Gestión de Modelos" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Modelos</h1>
                    <p class="text-muted-foreground">Catálogo de modelos por marca.</p>
                </div>
                <!-- Create Button -->
                <Button as-child class="bg-primary text-primary-foreground">
                    <Link :href="route('models.create')">
                        <Plus class="mr-2 h-4 w-4" /> Nuevo Modelo
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader class="pb-3">
                    <CardTitle>Listado de Modelos</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-muted/40 font-medium">
                                <tr>
                                    <th class="p-4">Marca</th>
                                    <th class="p-4">Modelo</th>
                                    <th class="p-4">Tipo Sugerido</th>
                                    <th class="p-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="models.length === 0">
                                    <td colspan="4" class="p-8 text-center text-muted-foreground">
                                        No hay modelos registrados.
                                    </td>
                                </tr>
                                <tr v-for="model in models" :key="model.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4">{{ model.marca?.nombre || '-' }}</td>
                                    <td class="p-4 font-bold">{{ model.nombre }}</td>
                                    <td class="p-4 text-xs text-muted-foreground">
                                        {{ model.tipo_activo?.nombre || '-' }}
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button variant="ghost" size="icon" as-child class="text-muted-foreground hover:text-primary" title="Editar">
                                                <Link :href="route('models.edit', model.id)">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" @click="deleteModel(model.id)" class="text-destructive hover:bg-destructive/10" title="Eliminar">
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
