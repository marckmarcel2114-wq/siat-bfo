<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Users, Pencil, Trash2, Plus } from 'lucide-vue-next';
import { route } from 'ziggy-js';

const props = defineProps<{
    jobTitles: Array<{
        id: number;
        name: string;
        created_at: string;
    }>;
}>();

const deleteJobTitle = (id: number) => {
    if (confirm('¿Estás seguro de que quieres eliminar este cargo?')) {
        router.delete(route('job-titles.destroy', id));
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Inicio', href: route('dashboard') }, { title: 'Configuración', href: '#' }, { title: 'Cargos', href: '#' }]">
        <Head title="Gestión de Cargos" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Cargos</h1>
                    <p class="text-muted-foreground">Administración del catálogo de cargos y puestos laborales.</p>
                </div>
                <Button as-child class="bg-primary text-primary-foreground">
                    <Link :href="route('job-titles.create')">
                        <Plus class="mr-2 h-4 w-4" /> Nuevo Cargo
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader class="pb-3">
                    <CardTitle>Listado de Cargos</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-muted/40 font-medium">
                                <tr>
                                    <th class="p-4">Nombre del Cargo</th>
                                    <th class="p-4 text-center">Fecha de Creación</th>
                                    <th class="p-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="jobTitles.length === 0">
                                    <td colspan="3" class="p-8 text-center text-muted-foreground">
                                        No hay cargos registrados.
                                    </td>
                                </tr>
                                <tr v-for="job in jobTitles" :key="job.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4 font-medium">
                                        <div class="flex items-center gap-2">
                                            <div class="p-2 rounded-full bg-indigo-50 text-indigo-600">
                                                <Users class="w-4 h-4" />
                                            </div>
                                            {{ job.name }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-center text-muted-foreground">
                                        {{ new Date(job.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button variant="ghost" size="icon" as-child class="text-muted-foreground hover:text-primary">
                                                <Link :href="route('job-titles.edit', job.id)">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" @click="deleteJobTitle(job.id)" class="text-destructive hover:bg-destructive/10">
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
