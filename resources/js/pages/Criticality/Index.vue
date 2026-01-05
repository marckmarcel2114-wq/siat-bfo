<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ShieldAlert, Trash2, Plus, Pencil } from 'lucide-vue-next';
import GenericQuickAdd from '@/components/QuickAdd/GenericQuickAdd.vue';
import { route } from 'ziggy-js';

defineProps<{
    criticalities: Array<{
        id: number;
        nombre: string;
        color: string;
        nivel_numerico: number;
    }>;
}>();

const deleteCrit = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este nivel?')) {
        router.delete(route('criticality.destroy', id));
    }
};

const onCritAdded = () => {
    router.reload();
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Inicio', href: route('dashboard') }, { title: 'Configuración', href: '#' }, { title: 'Criticidad', href: '#' }]">
        <Head title="Niveles de Criticidad" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Niveles de Criticidad</h1>
                    <p class="text-muted-foreground">Clasificación de riesgo e importancia.</p>
                </div>
                <GenericQuickAdd 
                    title="Nuevo Nivel" 
                    endpoint="criticality.store"
                    :icon="ShieldAlert"
                    enable-color
                    @success="onCritAdded"
                >
                    <Button class="bg-primary text-primary-foreground">
                        <Plus class="mr-2 h-4 w-4" /> Nuevo Nivel
                    </Button>
                </GenericQuickAdd>
            </div>

            <Card>
                <CardHeader class="pb-3">
                    <CardTitle>Listado de Niveles</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-muted/40 font-medium">
                                <tr>
                                    <th class="p-4">Nivel</th>
                                    <th class="p-4">Nombre</th>
                                    <th class="p-4">Color</th>
                                    <th class="p-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="crit in criticalities" :key="crit.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4 font-mono">{{ crit.nivel_numerico }}</td>
                                    <td class="p-4 font-bold" :style="{ color: crit.color }">{{ crit.nombre }}</td>
                                    <td class="p-4">
                                        <div class="w-6 h-6 rounded-full border shadow-sm" :style="{ backgroundColor: crit.color }"></div>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button variant="ghost" size="icon" as-child class="text-muted-foreground hover:text-primary">
                                                <Link :href="route('criticality.edit', crit.id)">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" @click="deleteCrit(crit.id)" class="text-destructive hover:bg-destructive/10">
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
