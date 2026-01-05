<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Search, Tag, Pencil, Trash2, Plus } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import GenericQuickAdd from '@/components/QuickAdd/GenericQuickAdd.vue';
import { route } from 'ziggy-js';
import { ref } from 'vue';

const props = defineProps<{
    brands: Array<{
        id: number;
        nombre: string;
        modelos_count: number;
    }>;
}>();

const deleteBrand = (id: number) => {
    if (confirm('¿Estás seguro de eliminar esta marca?')) {
        router.delete(route('brands.destroy', id));
    }
};

const onBrandAdded = () => {
    router.reload();
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Inicio', href: route('dashboard') }, { title: 'Configuración', href: '#' }, { title: 'Marcas', href: '#' }]">
        <Head title="Gestión de Marcas" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Marcas</h1>
                    <p class="text-muted-foreground">Administración del catálogo de marcas.</p>
                </div>
                <Button as-child class="bg-primary text-primary-foreground">
                    <Link :href="route('brands.create')">
                        <Plus class="mr-2 h-4 w-4" /> Nueva Marca
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader class="pb-3">
                    <CardTitle>Listado de Marcas</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-muted/40 font-medium">
                                <tr>
                                    <th class="p-4">Nombre</th>
                                    <th class="p-4 text-center">Modelos Asociados</th>
                                    <th class="p-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="brands.length === 0">
                                    <td colspan="3" class="p-8 text-center text-muted-foreground">
                                        No hay marcas registradas.
                                    </td>
                                </tr>
                                <tr v-for="brand in brands" :key="brand.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4 font-medium">{{ brand.nombre }}</td>
                                    <td class="p-4 text-center">
                                        <div class="inline-flex items-center justify-center bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                            {{ brand.modelos_count }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button variant="ghost" size="icon" as-child class="text-muted-foreground hover:text-primary">
                                                <Link :href="route('brands.edit', brand.id)">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" @click="deleteBrand(brand.id)" class="text-destructive hover:bg-destructive/10">
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
