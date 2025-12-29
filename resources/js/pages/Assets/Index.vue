<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Monitor, Plus, Pencil, Trash2, Search, Filter, Eye } from 'lucide-vue-next';
import { watch, ref } from 'vue';
import { debounce } from 'lodash'; 

const props = defineProps<{
    assets: {
        data: Array<{
            id: number;
            code_internal: string;
            brand: string;
            model: string;
            serial_number: string;
            status: string;
            type: { id: number; name: string };
            location: { id: number; name: string; city: { name: string } } | null;
        }>;
        links: Array<any>;
    };
    types: Array<{ id: number; name: string }>;
    filters: {
        search?: string;
        status?: string;
        type?: string;
    };
}>();

// Filter Management
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const type = ref(props.filters.type || '');

// Debounce search to avoid rapid requests
const updateFilters = debounce(() => {
    router.get(route('assets.index'), { 
        search: search.value, 
        status: status.value,
        type: type.value 
    }, { preserveState: true, replace: true });
}, 500);

watch([search, status, type], () => {
    updateFilters();
});

const getStatusBadge = (status: string) => {
    switch(status) {
        case 'free': return { variant: 'outline', label: 'Disponible', class: 'text-green-600 border-green-600 bg-green-50' };
        case 'assigned': return { variant: 'default', label: 'Asignado', class: 'bg-blue-600 text-white hover:bg-blue-700' };
        case 'maintenance': return { variant: 'secondary', label: 'Mantenimiento', class: 'bg-yellow-100 text-yellow-800' };
        case 'repair': return { variant: 'destructive', label: 'Reparación', class: 'bg-orange-100 text-orange-800' };
        case 'broken': return { variant: 'destructive', label: 'Dañado', class: '' };
        case 'disposed': return { variant: 'outline', label: 'Baja', class: 'text-gray-500' };
        default: return { variant: 'outline', label: status, class: '' };
    }
};

const deleteAsset = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este activo?')) {
        router.delete(route('assets.destroy', id));
    }
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Inventario', href: '#' },
    { title: 'Activos', href: route('assets.index') },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Gestión de Activos" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Inventario de Activos</h1>
                    <p class="text-muted-foreground">Control total de hardware y equipos.</p>
                </div>
                <Button as-child class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-sm">
                    <Link :href="route('assets.create')">
                        <Plus class="mr-2 h-4 w-4" />
                        Nuevo Activo
                    </Link>
                </Button>
            </div>

            <!-- Filters -->
            <Card class="border-border/50 shadow-sm">
                <CardContent class="p-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="space-y-2 md:col-span-2">
                        <Label>Búsqueda</Label>
                        <div class="relative">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input v-model="search" placeholder="Código, Serie, Marca o Modelo..." class="pl-8" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label>Estado</Label>
                        <select 
                            v-model="status" 
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="">Todos</option>
                            <option value="free">Disponibles</option>
                            <option value="assigned">Asignados</option>
                            <option value="maintenance">Mantenimiento</option>
                            <option value="repair">Reparación</option>
                            <option value="broken">Dañado</option>
                            <option value="disposed">Baja</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <Label>Tipo</Label>
                        <select 
                             v-model="type"
                             class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="">Todos</option>
                            <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-border/50 shadow-sm">
                <CardHeader class="pb-3 border-b border-border/50 bg-muted/20">
                    <CardTitle>Listado de Equipos</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-muted/50 text-muted-foreground font-medium border-b border-border/50">
                                <tr>
                                    <th class="h-12 px-4 whitespace-nowrap">Código</th>
                                    <th class="h-12 px-4">Tipo / Modelo</th>
                                    <th class="h-12 px-4">Serie</th>
                                    <th class="h-12 px-4">Ubicación</th>
                                    <th class="h-12 px-4">Estado</th>
                                    <th class="h-12 px-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="assets.data.length === 0">
                                    <td colspan="6" class="p-8 text-center text-muted-foreground">
                                        No se encontraron activos que coincidan con la búsqueda.
                                    </td>
                                </tr>
                                <tr v-for="asset in assets.data" :key="asset.id" class="border-b border-border/50 last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="p-4 font-mono font-medium text-primary whitespace-nowrap">
                                        {{ asset.code_internal || 'S/N' }}
                                    </td>
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ asset.type?.name }}</span>
                                            <span class="text-xs text-muted-foreground">{{ asset.brand }} {{ asset.model }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-xs text-muted-foreground">
                                        {{ asset.serial_number }}
                                    </td>
                                    <td class="p-4">
                                        <div v-if="asset.location" class="flex flex-col text-xs">
                                            <span class="font-medium">{{ asset.location.name }}</span>
                                            <span class="text-muted-foreground">{{ asset.location.city?.name }}</span>
                                        </div>
                                        <span v-else class="text-xs text-muted-foreground italic">Sin ubicación</span>
                                    </td>
                                    <td class="p-4">
                                        <Badge :class="getStatusBadge(asset.status).class" variant="secondary" class="font-normal capitalize border-0">
                                            {{ getStatusBadge(asset.status).label }}
                                        </Badge>
                                    </td>
                                    <td class="p-4 text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-1">
                                            <Button variant="ghost" size="icon" as-child title="Ver Detalles">
                                                <Link :href="route('assets.show', asset.id)">
                                                    <Eye class="h-4 w-4 text-muted-foreground hover:text-primary" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" as-child title="Editar">
                                                <Link :href="route('assets.edit', asset.id)">
                                                    <Pencil class="h-4 w-4 text-muted-foreground hover:text-primary" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" @click="deleteAsset(asset.id)" title="Eliminar">
                                                <Trash2 class="h-4 w-4 text-muted-foreground hover:text-destructive" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination (Basic) -->
                    <div class="p-4 flex items-center justify-between border-t border-border/50" v-if="assets.links.length > 3">
                        <div class="flex gap-1">
                             <template v-for="(link, k) in assets.links" :key="k">
                                <Link 
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1 text-sm rounded-md transition-colors"
                                    :class="link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'"
                                    v-html="link.label"
                                />
                                <span v-else class="px-3 py-1 text-sm text-muted-foreground opacity-50" v-html="link.label"></span>
                             </template>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
