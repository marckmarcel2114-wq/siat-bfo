<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Monitor, Plus, Pencil, Trash2, Search, MapPin, Building, Download, FileSpreadsheet, FileText, ChevronDown, ChevronRight } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import Pagination from '@/components/Pagination.vue';

import { ref, watch, computed } from 'vue';
import { route } from 'ziggy-js';
import { debounce } from 'lodash';

const props = defineProps<{
    atms: {
        data: Array<{
            id: number;
            name: string;
            address: string;
            city: { name: string };
            parent?: { name: string }; // Agency parent
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
    router.get(route('atms.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Grouping and Collapsible Logic - Collapsed by default (track expanded)
const expandedGroups = ref<Set<string>>(new Set());

const groupedAtms = computed(() => {
    const groups: { name: string; items: typeof props.atms.data }[] = [];
    let currentCity = '';
    let currentGroup: any = null;

    props.atms.data.forEach(atm => {
        const cityName = atm.city?.name || 'Otras Ubicaciones';
        if (cityName !== currentCity) {
            currentCity = cityName;
            currentGroup = { name: cityName, items: [] };
            groups.push(currentGroup);
        }
        currentGroup.items.push(atm);
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

const deleteAtm = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este ATM?')) {
        router.delete(route('atms.destroy', id));
    }
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Ubicaciones', href: '#' },
    { title: 'ATMs', href: route('atms.index') },
];

const exportCSV = () => {
    const headers = ['ID', 'Nombre', 'Dirección', 'Ciudad', 'Agencia Base'];
    const rows = props.atms.data.map(a => [
        a.id,
        a.name,
        a.address || '',
        a.city?.name || '',
        a.parent?.name || ''
    ]);
    
    let csvContent = "data:text/csv;charset=utf-8," 
        + headers.join(",") + "\n"
        + rows.map(e => e.join(",")).join("\n");

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "atms.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Gestión de ATMs" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Cajeros Automáticos (ATMs)</h1>
                    <p class="text-muted-foreground">Administra la red de cajeros y su vinculación con agencias.</p>
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
                        <Link :href="route('atms.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Nuevo ATM
                        </Link>
                    </Button>
                </div>
            </div>

            <Card class="border-border/50 shadow-sm">
                <CardHeader class="pb-3 px-6">
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-lg">Listado de ATMs</CardTitle>
                        <div class="relative w-72 max-sm hidden md:block">
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
                                    <th class="h-10 px-4 align-middle font-semibold">Nombre / ID</th>
                                    <th class="h-10 px-4 align-middle font-semibold">Agencia Base (Vínculo)</th>
                                    <th class="h-10 px-4 align-middle text-right font-semibold pr-6">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="atms.data.length === 0">
                                    <td colspan="4" class="p-12 text-center text-muted-foreground">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <Monitor class="h-8 w-8 opacity-20" />
                                            <p>No se encontraron ATMs registrados.</p>
                                        </div>
                                    </td>
                                </tr>
                                <template v-for="group in groupedAtms" :key="group.name">
                                    <!-- City Header (Collapsible) -->
                                    <tr 
                                        class="bg-muted/10 border-b border-border/50 cursor-pointer hover:bg-muted/20 transition-colors"
                                        @click="toggleGroup(group.name)"
                                    >
                                        <td colspan="4" class="px-4 py-2.5 font-bold text-[11px] uppercase tracking-wider text-emerald-700/80">
                                            <div class="flex items-center justify-between w-full">
                                                <div class="flex items-center gap-2">
                                                    <component :is="isExpanded(group.name) ? ChevronDown : ChevronRight" class="h-4 w-4 text-muted-foreground" />
                                                    <MapPin class="h-3.5 w-3.5" />
                                                    {{ group.name }}
                                                    <Badge variant="outline" class="ml-2 bg-background border-emerald-500/20 text-emerald-700 px-2 font-bold">{{ group.items.length }}</Badge>
                                                </div>
                                                <span class="text-[9px] font-medium text-muted-foreground opacity-60 italic">
                                                    {{ isExpanded(group.name) ? 'CLICK PARA CONTRAER' : 'CLICK PARA EXPANDIR' }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- ATM Rows -->
                                    <template v-if="isExpanded(group.name)">
                                        <tr v-for="atm in group.items" :key="atm.id" class="border-b border-border/50 last:border-0 hover:bg-muted/30 transition-colors group/row">
                                            <td class="p-4 pl-10 border-l-2 border-emerald-500/20">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded-md bg-emerald-500/10 flex items-center justify-center text-emerald-600 group-hover/row:scale-110 transition-transform shrink-0">
                                                        <Monitor class="h-4 w-4" />
                                                    </div>
                                                    <div class="flex flex-col text-left">
                                                        <span class="font-medium text-foreground">{{ atm.name }}</span>
                                                        <span class="text-[10px] text-muted-foreground truncate max-w-[200px]" :title="atm.address">{{ atm.address || 'Sin dirección' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4">
                                                <div v-if="atm.parent" class="flex items-center gap-1.5 ">
                                                    <div class="h-5 w-5 rounded bg-blue-500/10 flex items-center justify-center text-blue-600">
                                                        <Building class="h-3 w-3" />
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-semibold text-foreground">{{ atm.parent.name }}</span>
                                                        <span class="text-[9px] text-muted-foreground uppercase font-medium">Vinculado</span>
                                                    </div>
                                                </div>
                                                <div v-else class="flex items-center gap-1.5 opacity-50">
                                                    <div class="h-5 w-5 rounded bg-slate-200 flex items-center justify-center text-slate-500">
                                                        <Monitor class="h-3 w-3" />
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-[10px] text-slate-500 font-medium italic">Sin vincular</span>
                                                        <span class="text-[9px] text-slate-400 uppercase font-medium truncate max-w-[80px]">Extra muro</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4 text-right pr-6">
                                                <div class="flex justify-end gap-1">
                                                    <Button variant="ghost" size="icon" class="h-8 w-8" as-child>
                                                        <Link :href="route('atms.edit', atm.id)">
                                                            <Pencil class="h-3.5 w-3.5 text-muted-foreground hover:text-primary transition-colors" />
                                                        </Link>
                                                    </Button>
                                                    <Button variant="ghost" size="icon" class="h-8 w-8 hover:bg-destructive/10" @click="deleteAtm(atm.id)">
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
                            Mostrando página {{ atms.current_page }} de {{ atms.last_page }}
                        </div>
                        <Pagination :links="atms.links" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
