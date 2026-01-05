<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Search, Tag, Pencil, Trash2, Plus, Users } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';

const props = defineProps<{
    owners: Object;
    filters: Object;
}>();

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        route('owners.index'),
        { search: value },
        { preserveState: true, replace: true }
    );
});

const deleteOwner = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este propietario?')) {
        router.delete(route('owners.destroy', id));
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Inicio', href: route('dashboard') }, { title: 'Configuración', href: '#' }, { title: 'Propietarios', href: '#' }]">
        <Head title="Gestión de Propietarios" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Propietarios</h1>
                    <p class="text-muted-foreground">Gestión de propietarios de activos (Bancos, Proveedores, etc.).</p>
                </div>
                <Button as-child class="bg-primary text-primary-foreground">
                    <Link :href="route('owners.create')">
                        <Plus class="mr-2 h-4 w-4" /> Nuevo Propietario
                    </Link>
                </Button>
            </div>

            <!-- Filters -->
            <div class="flex items-center space-x-2">
                <div class="relative w-full max-w-sm">
                    <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input v-model="search" placeholder="Buscar propietario..." class="pl-8" />
                </div>
            </div>

            <Card>
                <CardHeader class="pb-3">
                    <CardTitle>Listado de Propietarios</CardTitle>
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
                                <tr v-if="owners.data.length === 0">
                                    <td colspan="2" class="p-8 text-center text-muted-foreground">
                                        No hay propietarios registrados.
                                    </td>
                                </tr>
                                <tr v-for="owner in owners.data" :key="owner.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4 font-medium flex items-center gap-3">
                                        <div class="bg-primary/10 p-2 rounded-full text-primary">
                                            <Users class="h-4 w-4" />
                                        </div>
                                        {{ owner.nombre }}
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button variant="ghost" size="icon" as-child class="text-muted-foreground hover:text-primary">
                                                <Link :href="route('owners.edit', owner.id)">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" @click="deleteOwner(owner.id)" class="text-destructive hover:bg-destructive/10">
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div v-if="owners.links && owners.links.length > 3" class="p-4 border-t">
                         <div class="flex gap-1 justify-center">
                            <template v-for="(link, i) in owners.links" :key="i">
                                <Link v-if="link.url" :href="link.url" v-html="link.label" 
                                    class="px-3 py-1 border rounded text-xs" 
                                    :class="{'bg-primary text-white': link.active, 'hover:bg-muted': !link.active}" />
                                <span v-else v-html="link.label" class="px-3 py-1 border rounded text-xs text-muted-foreground opacity-50"></span>
                            </template>
                         </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
