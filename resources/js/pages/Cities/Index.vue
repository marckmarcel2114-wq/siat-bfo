<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { MapPin, Plus, Pencil, Trash2, Search, Building, Monitor, Landmark } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import Pagination from '@/components/Pagination.vue';
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps<{
    cities: {
        data: Array<{
            id: number;
            id: number;
            nombre: string;
            codigo: string;
            atms_count: number;
            [key: string]: any; // For dynamic type_X_count
        }>;
        links: any[];
    };
    branchTypes: Array<{
        id: number;
        nombre: string;
        color: string;
    }>;
    filters: {
        search: string;
    };
}>();

const search = ref(props.filters.search || '');

watch(search, debounce((value: string) => {
    router.get(route('cities.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const deleteCity = (id: number) => {
    if (confirm('¿Estás seguro de eliminar esta ciudad? Se eliminarán también sus sucursales.')) {
        router.delete(route('cities.destroy', id));
    }
};

const getIconForType = (name: string) => {
    const t = name.toLowerCase();
    if (t.includes('atm')) return Monitor;
    if (t.includes('agencia')) return Building;
    return Landmark;
};

const getTypeColorClass = (name: string) => {
    const t = name.toLowerCase();
    if (t.includes('sucursal')) return 'bg-sky-500/10 text-sky-600 border-sky-500/20 dark:text-sky-400';
    if (t.includes('agencia')) return 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20 dark:text-emerald-400';
    if (t.includes('externa')) return 'bg-orange-500/10 text-orange-600 border-orange-500/20 dark:text-orange-400';
    if (t.includes('paf')) return 'bg-rose-500/10 text-rose-600 border-rose-500/20 dark:text-rose-400';
    if (t.includes('central')) return 'bg-indigo-500/10 text-indigo-600 border-indigo-500/20 dark:text-indigo-400';
    return 'bg-slate-500/10 text-slate-600 border-slate-500/20 dark:text-slate-400';
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Ubicaciones', href: '#' },
    { title: 'Ciudades', href: route('cities.index') },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Gestión de Ciudades" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Ciudades y Estadísticas</h1>
                    <p class="text-muted-foreground">Desglose de presencia institucional por región.</p>
                </div>
                <Button as-child class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-sm">
                    <Link :href="route('cities.create')">
                        <Plus class="mr-2 h-4 w-4" />
                        Nueva Ciudad
                    </Link>
                </Button>
            </div>

            <Card class="border-border/50 shadow-sm">
                <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                        <CardTitle>Listado de Ciudades</CardTitle>
                        <div class="relative w-64 max-w-sm hidden md:block">
                            <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input v-model="search" placeholder="Buscar ciudad..." class="pl-9 h-9" />
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead class="bg-muted/40 text-muted-foreground font-medium border-y border-border/50">
                                <tr>
                                    <th class="h-10 px-4 align-middle font-semibold">Ciudad / Código</th>
                                    <th class="h-10 px-4 align-middle font-semibold text-center">Estadísticas de Puntos de Atención</th>
                                    <th class="h-10 px-4 align-middle text-right font-semibold pr-6">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="cities.data.length === 0">
                                    <td colspan="3" class="p-12 text-center text-muted-foreground">
                                        No se encontraron ciudades registradas.
                                    </td>
                                </tr>
                                <tr v-for="city in cities.data" :key="city.id" class="border-b border-border/50 last:border-0 hover:bg-muted/30 transition-colors group/row">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover/row:scale-110 transition-transform">
                                                <MapPin class="h-4 w-4" />
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-bold text-foreground">{{ city.nombre }}</span>
                                                <span class="text-[10px] font-mono text-muted-foreground uppercase opacity-70">{{ city.codigo }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="p-4">
                                        <div class="flex flex-col gap-1.5">
                                            <template v-for="type in branchTypes" :key="type.id">
                                                <div v-if="city['type_' + type.id + '_count'] > 0" class="flex items-center">
                                                    <Badge 
                                                        variant="outline" 
                                                        :class="`bg-${type.color}-500/10 text-${type.color}-600 border-${type.color}-500/20 dark:text-${type.color}-400`"
                                                        class="flex items-center gap-2 px-2 py-0.5 border shadow-none h-6 w-fit"
                                                    >
                                                        <span class="text-[11px] font-black border-r border-current pr-2 leading-tight">{{ city['type_' + type.id + '_count'] }}</span>
                                                        <span class="text-[10px] uppercase font-bold tracking-wider leading-tight">{{ type.nombre }}</span>
                                                    </Badge>
                                                </div>
                                            </template>
                                            
                                            <span v-if="!branchTypes.some(t => city['type_' + t.id + '_count'] > 0)" class="text-muted-foreground/30 italic text-xs">
                                                Sin puntos registrados
                                            </span>
                                        </div>
                                    </td>

                                    <td class="p-4 text-right pr-6">
                                        <div class="flex justify-end gap-1">
                                            <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-primary" as-child>
                                                <Link :href="route('cities.edit', city.id)">
                                                    <Pencil class="h-3.5 w-3.5" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-destructive" @click="deleteCity(city.id)">
                                                <Trash2 class="h-3.5 w-3.5" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-border/50">
                        <Pagination :links="cities.links" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
