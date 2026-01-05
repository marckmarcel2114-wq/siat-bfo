<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Activity, Trash2, Plus, Pencil } from 'lucide-vue-next';
import GenericQuickAdd from '@/components/QuickAdd/GenericQuickAdd.vue';
import { route } from 'ziggy-js';

defineProps<{
    statuses: Array<{
        id: number;
        nombre: string;
    }>;
}>();

const deleteStatus = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este estado?')) {
        router.delete(route('asset-status.destroy', id));
    }
};

const onStatusAdded = () => {
    router.reload();
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Inicio', href: route('dashboard') }, { title: 'Configuración', href: '#' }, { title: 'Estados', href: '#' }]">
        <Head title="Estados de Activo" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Estados de Activo</h1>
                    <p class="text-muted-foreground">Ciclo de vida de los activos.</p>
                </div>
                <Button as-child class="bg-primary text-primary-foreground">
                    <Link :href="route('asset-status.create')">
                        <Plus class="mr-2 h-4 w-4" /> Nuevo Estado
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader class="pb-3">
                    <CardTitle>Listado de Estados</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-muted/40 font-medium">
                                <tr>
                                    <th class="p-4">Nombre</th>
                                    <th class="p-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="status in statuses" :key="status.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4">{{ status.nombre }}</td>
                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button variant="ghost" size="icon" as-child class="text-muted-foreground hover:text-primary">
                                                <Link :href="route('asset-status.edit', status.id)">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" @click="deleteStatus(status.id)" class="text-destructive hover:bg-destructive/10">
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
