<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Building, Plus, Pencil, Trash2, Search, MapPin, Phone, Download, FileSpreadsheet, FileText, ChevronDown, ChevronRight } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import Pagination from '@/components/Pagination.vue';

import { ref, watch, computed } from 'vue';
import { route } from 'ziggy-js';
import { debounce } from 'lodash';

const props = defineProps<{
    branches: {
        data: Array<{
            id: number;
            code: string;
            name: string;
            address: string;
            phones: string;
            city: { name: string };
            type: { name: string };
        }>;
        links: any[];
        current_page: number;
        last_page: number;
    };
    filters: {
        search: string;
    };
}>();

const search = ref(props.filters.search || '');

watch(search, debounce((value: string) => {
    router.get(route('branches.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Grouping and Collapsible Logic - Collapsed by default (track expanded)
const expandedGroups = ref<Set<string>>(new Set());

const groupedBranches = computed(() => {
    const groups: { name: string; items: typeof props.branches.data }[] = [];
    let currentCity = '';
    let currentGroup: any = null;

    props.branches.data.forEach(branch => {
        const cityName = branch.city?.name || 'Otras Ubicaciones';
        if (cityName !== currentCity) {
            currentCity = cityName;
            currentGroup = { name: cityName, items: [] };
            groups.push(currentGroup);
        }
        currentGroup.items.push(branch);
    });
    return groups;
});

const toggleGroup = (cityName: string) => {
    if (expandedGroups.value.has(cityName)) {
        expandedGroups.value.delete(cityName);
    } else {
        expandedGroups.value.add(cityName);
    }
};

const isExpanded = (cityName: string) => expandedGroups.value.has(cityName);

const getTypeColor = (type: string) => {
    const t = type.toLowerCase();
    if (t.includes('central')) return 'bg-indigo-500/10 text-indigo-700 border-indigo-500/20';
    if (t.includes('agencia')) return 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20';
    if (t.includes('externa')) return 'bg-orange-500/10 text-orange-700 border-orange-500/20';
    if (t.includes('paf')) return 'bg-rose-500/10 text-rose-700 border-rose-500/20';
    if (t.includes('sucursal')) return 'bg-sky-500/10 text-sky-700 border-sky-500/20';
    return 'bg-slate-500/10 text-slate-700 border-slate-500/20';
};

const deleteBranch = (id: number) => {
    if (confirm('¿Estás seguro de eliminar esta sucursal?')) {
        router.delete(route('branches.destroy', id));
    }
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Ubicaciones', href: '#' },
    { title: 'Sucursales', href: route('branches.index') },
];

const exportCSV = () => {
    const headers = ['ID', 'Código', 'Nombre', 'Ciudad', 'Tipo', 'Dirección', 'Teléfonos'];
    const rows = props.branches.data.map(b => [
        b.id,
        b.code || '',
        b.name,
        b.city?.name || '',
        b.type?.name || '',
        b.address || '',
        b.phones || ''
    ]);
    
    let csvContent = "data:text/csv;charset=utf-8," 
        + headers.join(",") + "\n"
        + rows.map(e => e.join(",")).join("\n");

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "sucursales.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Gestión de Sucursales" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Sucursales y Agencias</h1>
                    <p class="text-muted-foreground">Administra la red de puntos de atención física.</p>
                </div>
                <div class="flex gap-2 w-full md:w-auto">
                     <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline" class="shadow-sm">
                                <Download class="mr-2 h-4 w-4" />
                                Exportar
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-48">
                            <DropdownMenuItem @click="exportCSV">
                                <FileSpreadsheet class="mr-2 h-4 w-4 text-emerald-600" />
                                Excel (CSV)
                            </DropdownMenuItem>
                             <DropdownMenuItem @click="() => {}">
                                <FileText class="mr-2 h-4 w-4 text-rose-600" />
                                PDF
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <Button as-child class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-sm">
                        <Link :href="route('branches.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Nueva Sucursal
                        </Link>
                    </Button>
                </div>
            </div>

            <Card class="border-border/50 shadow-sm">
                <CardHeader class="pb-3 px-6">
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-lg">Listado de Sucursales</CardTitle>
                        <div class="relative w-72 max-w-sm hidden md:block">
                            <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input v-model="search" placeholder="Buscar por nombre, código o ciudad..." class="pl-9 h-9" />
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead class="bg-muted/40 text-muted-foreground font-medium border-y border-border/50">
                                <tr>
                                    <th class="h-10 px-4 align-middle font-semibold">Sucursal / Código</th>
                                    <th class="h-10 px-4 align-middle font-semibold">Tipo</th>
                                    <th class="h-10 px-4 align-middle font-semibold">Teléfonos</th>
                                    <th class="h-10 px-4 align-middle text-right font-semibold pr-6">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="branches.data.length === 0">
                                    <td colspan="4" class="p-12 text-center text-muted-foreground">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <Building class="h-8 w-8 opacity-20" />
                                            <p>No se encontraron sucursales registradas.</p>
                                        </div>
                                    </td>
                                </tr>
                                <template v-for="group in groupedBranches" :key="group.name">
                                    <!-- City Header (Collapsible) -->
                                    <tr 
                                        class="bg-muted/10 border-b border-border/50 cursor-pointer hover:bg-muted/20 transition-colors"
                                        @click="toggleGroup(group.name)"
                                    >
                                        <td colspan="4" class="px-4 py-2.5 font-bold text-[11px] uppercase tracking-wider text-primary/70">
                                            <div class="flex items-center justify-between w-full">
                                                <div class="flex items-center gap-2">
                                                    <component :is="isExpanded(group.name) ? ChevronDown : ChevronRight" class="h-4 w-4 text-muted-foreground" />
                                                    <MapPin class="h-3.5 w-3.5" />
                                                    {{ group.name }}
                                                    <Badge variant="outline" class="ml-2 bg-background border-primary/20 text-primary px-2 font-bold">{{ group.items.length }}</Badge>
                                                </div>
                                                <span class="text-[9px] font-medium text-muted-foreground opacity-60 italic">
                                                    {{ isExpanded(group.name) ? 'CLICK PARA CONTRAER' : 'CLICK PARA EXPANDIR' }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Branch Rows -->
                                    <template v-if="isExpanded(group.name)">
                                        <tr v-for="branch in group.items" :key="branch.id" class="border-b border-border/50 last:border-0 hover:bg-muted/30 transition-colors group/row">
                                            <td class="p-4 pl-10 border-l-2 border-primary/10">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded-md bg-primary/10 flex items-center justify-center text-primary group-hover/row:scale-110 transition-transform">
                                                        <Building class="h-4 w-4" />
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="font-medium text-foreground">{{ branch.name }}</span>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-[10px] bg-muted px-1.5 py-0.5 rounded font-mono text-muted-foreground" v-if="branch.code">
                                                                {{ branch.code }}
                                                            </span>
                                                            <span class="text-[10px] text-muted-foreground truncate max-w-[200px]" :title="branch.address">{{ branch.address }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4">
                                                <Badge variant="outline" :class="getTypeColor(branch.type?.name)" class="font-semibold text-[10px] px-2 py-0.5 rounded-md">
                                                    {{ branch.type?.name }}
                                                </Badge>
                                            </td>
                                            <td class="p-4">
                                                <div class="flex items-center gap-1.5 text-muted-foreground">
                                                    <Phone class="h-3.5 w-3.5 opacity-70" />
                                                    <span class="text-xs">{{ branch.phones || 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td class="p-4 text-right pr-6">
                                                <div class="flex justify-end gap-1">
                                                    <Button variant="ghost" size="icon" class="h-8 w-8" as-child>
                                                        <Link :href="route('branches.edit', branch.id)">
                                                            <Pencil class="h-3.5 w-3.5 text-muted-foreground hover:text-primary transition-colors" />
                                                        </Link>
                                                    </Button>
                                                    <Button variant="ghost" size="icon" class="h-8 w-8 hover:bg-destructive/10" @click="deleteBranch(branch.id)">
                                                        <Trash2 class="h-3.5 w-3.5 text-muted-foreground hover:text-destructive transition-colors" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Component -->
                    <div class="p-4 border-t border-border/50 flex items-center justify-between">
                         <div class="text-xs text-muted-foreground">
                            Mostrando página {{ branches.current_page }} de {{ branches.last_page }}
                        </div>
                        <Pagination :links="branches.links" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
