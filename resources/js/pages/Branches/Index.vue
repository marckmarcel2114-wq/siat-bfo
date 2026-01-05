<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Building, Plus, Pencil, Trash2, Search, MapPin, Phone, Download, FileSpreadsheet, FileText, ChevronDown, ChevronRight, Filter, Activity, TrendingUp } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger, DropdownMenuCheckboxItem, DropdownMenuSeparator, DropdownMenuLabel } from '@/components/ui/dropdown-menu';
import { cn } from '@/lib/utils';
import Pagination from '@/components/Pagination.vue';

import { route } from 'ziggy-js';
import { debounce } from 'lodash';
import { ref, watch, computed } from 'vue';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
const props = defineProps<{
    branches: {
        data: Array<{
            id: number;
            codigo_ubicacion: string;
            nombre: string;
            direccion: string;
            telefonos: string;
            ciudad: { nombre: string };
            tipo: { nombre: string; color: string };
            padre: { nombre: string; tipo: { nombre: string; color: string } } | null;
        }>;
        links: any[];
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
    };
    branchTypes: Array<{ id: number; nombre: string; branches_count: number }>;
    cities: Array<{ id: number; nombre: string; branches_count: number }>;
    filters: {
        search: string;
        type: string;
        city: string;
    };
}>();

// Grouping and Collapsible Logic - Collapsed by default (track expanded)
// Stats Calculation
// Stats Calculation
const totalLocations = computed(() => {
    return props.branches.total; 
});

const activeTypes = computed(() => {
    return props.branchTypes.filter(t => t.branches_count > 0);
});

const expandedGroups = ref<Set<string>>(new Set());
const search = ref(props.filters.search || '');
const type = ref<string[]>(props.filters.type ? props.filters.type.split(',') : []);
const city = ref<string[]>(props.filters.city ? props.filters.city.split(',') : []);

watch([search, type, city], debounce(([searchValue, typeValue, cityValue]: [string, string[], string[]]) => {
    router.get(route('branches.index'), { 
        search: searchValue,
        type: typeValue.length > 0 ? typeValue.join(',') : null,
        city: cityValue.length > 0 ? cityValue.join(',') : null,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));


const toggleType = (id: string) => {
    if (type.value.includes(id)) {
        type.value = type.value.filter(t => t !== id);
    } else {
        type.value = [...type.value, id];
    }
};

const toggleCity = (id: string) => {
    if (city.value.includes(id)) {
        city.value = city.value.filter(c => c !== id);
    } else {
        city.value = [...city.value, id];
    }
};



const getDynamicBadgeColor = (colorName: string | undefined) => {
    if (!colorName) return 'bg-slate-500/10 text-slate-700 border-slate-500/20';
    const color = colorName.toLowerCase();
    
    const colors: Record<string, string> = {
        'blue': 'bg-blue-500/10 text-blue-700 border-blue-500/20',
        'indigo': 'bg-indigo-500/10 text-indigo-700 border-indigo-500/20',
        'emerald': 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20',
        'amber': 'bg-amber-500/10 text-amber-700 border-amber-500/20',
        'orange': 'bg-orange-500/10 text-orange-700 border-orange-500/20',
        'rose': 'bg-rose-500/10 text-rose-700 border-rose-500/20',
        'sky': 'bg-sky-500/10 text-sky-700 border-sky-500/20',
        'red': 'bg-red-500/10 text-red-700 border-red-500/20',
        'green': 'bg-green-500/10 text-green-700 border-green-500/20',
        'yellow': 'bg-yellow-500/10 text-yellow-700 border-yellow-500/20',
        'purple': 'bg-purple-500/10 text-purple-700 border-purple-500/20',
        'pink': 'bg-pink-500/10 text-pink-700 border-pink-500/20',
        'gray': 'bg-gray-500/10 text-gray-700 border-gray-500/20',
    };

    return colors[color] || 'bg-slate-500/10 text-slate-700 border-slate-500/20';
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
    const headers = ['#', 'Código', 'Nombre', 'Ciudad', 'Tipo', 'Dirección', 'Teléfonos'];
    const rows = props.branches.data.map((b, index) => [
        (props.branches.current_page - 1) * props.branches.per_page + index + 1,
        `"${b.codigo_ubicacion || ''}"`, // Wrap in quotes to safely handle potential commas
        `"${b.nombre}"`,
        `"${b.ciudad?.nombre || ''}"`,
        `"${b.tipo?.nombre || ''}"`,
        `"${b.direccion || ''}"`,
        `"${b.telefonos || ''}"`
    ]);
    
    const csvContent = headers.join(",") + "\n"
        + rows.map(e => e.join(",")).join("\n");

    // Use Blob for better encoding handling (Explicit BOM + UTF-8)
    const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", "sucursales.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
};

const exportPDF = () => {
    const doc = new jsPDF();
    const headers = [['#', 'Código', 'Nombre', 'Ciudad', 'Tipo', 'Dirección', 'Teléfonos']];
    const data = props.branches.data.map((b, index) => [
        (props.branches.current_page - 1) * props.branches.per_page + index + 1,
        b.codigo_ubicacion || '-',
        b.nombre,
        b.ciudad?.nombre || '',
        b.tipo?.nombre || '',
        b.direccion || '',
        b.telefonos || ''
    ]);

    doc.text("Reporte de Sucursales y Agencias", 14, 15);
    doc.setFontSize(10);
    doc.text(`Generado: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}`, 14, 22);

    autoTable(doc, {
        head: headers,
        body: data,
        startY: 25,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [41, 128, 185] },
    });

    doc.save("sucursales.pdf");
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
                            <DropdownMenuItem @click="exportPDF">
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

            <Card class="border-border/50 shadow-sm mb-6">
                <CardHeader class="pb-2">
                    <CardTitle class="text-base font-medium text-muted-foreground flex items-center gap-2">
                        <Activity class="h-4 w-4 text-primary" />
                        Resumen
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg border bg-card p-4 shadow-none flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Building class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Puntos</p>
                                <p class="text-2xl font-bold">{{ totalLocations }}</p>
                            </div>
                        </div>

                        <div class="rounded-lg border bg-card p-4 shadow-none col-span-3">
                            <p class="text-sm font-medium text-muted-foreground mb-3">Distribución por Tipo</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div v-for="type in activeTypes" :key="type.id" class="flex items-center justify-between p-2 rounded-md bg-muted/30 border border-border/50">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2.5 w-2.5 rounded-full" :class="getDynamicBadgeColor(type.color).split(' ')[0].replace('/10', '')"></div>
                                        <span class="text-sm font-medium">{{ type.nombre }}</span>
                                    </div>
                                    <Badge variant="secondary" class="font-mono text-xs font-bold">{{ type.branches_count }}</Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-border/50 shadow-sm">
                <CardHeader class="pb-3 px-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <CardTitle class="text-lg">Listado de Sucursales</CardTitle>
                        <div class="relative w-full md:w-72">
                            <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input v-model="search" placeholder="Buscar por nombre, código o ciudad..." class="pl-9 h-9 w-full" />
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead class="bg-muted/40 text-muted-foreground font-medium border-y border-border/50">
                                <tr>
                                    <th class="h-10 px-4 align-middle font-semibold w-[50px]">#</th>
                                    <th class="h-10 px-4 align-middle font-semibold w-[100px]">Código</th>
                                    <th class="h-10 px-4 align-middle font-semibold">Sucursal</th>
                                    <th class="h-10 px-4 align-middle font-semibold">
                                        <div class="flex items-center gap-2">
                                            Ciudad
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <Button variant="ghost" size="sm" class="h-8 w-8 p-0 hover:bg-muted relative">
                                                        <Filter class="h-4 w-4" :class="city.length > 0 ? 'text-primary fill-primary/20' : 'text-muted-foreground'" />
                                                        <span v-if="city.length > 0" class="absolute -top-1 -right-1 flex h-3 w-3 items-center justify-center rounded-full bg-primary text-[8px] text-primary-foreground font-bold">
                                                            {{ city.length }}
                                                        </span>
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="start" class="w-56 max-h-[300px] overflow-y-auto">
                                                    <DropdownMenuLabel class="flex items-center justify-between text-xs">
                                                        Filtrar por Ciudad
                                                        <span v-if="city.length > 0" class="text-[10px] text-muted-foreground cursor-pointer hover:text-primary" @click.stop="city = []">
                                                            Limpiar
                                                        </span>
                                                    </DropdownMenuLabel>
                                                    <DropdownMenuSeparator />
                                                    <DropdownMenuCheckboxItem v-for="c in cities" :key="c.id" :checked="city.includes(c.id.toString())" @select.prevent="toggleCity(c.id.toString())">
                                                        <span class="flex items-center justify-between w-full">
                                                            {{ c.nombre }}
                                                            <span class="text-[10px] text-muted-foreground ml-2">({{ c.branches_count }})</span>
                                                        </span>
                                                    </DropdownMenuCheckboxItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </div>
                                    </th>
                                    <th class="h-10 px-4 align-middle font-semibold">
                                        <div class="flex items-center gap-2">
                                            Tipo
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <Button variant="ghost" size="sm" class="h-8 w-8 p-0 hover:bg-muted relative">
                                                        <Filter class="h-4 w-4" :class="type.length > 0 ? 'text-primary fill-primary/20' : 'text-muted-foreground'" />
                                                        <span v-if="type.length > 0" class="absolute -top-1 -right-1 flex h-3 w-3 items-center justify-center rounded-full bg-primary text-[8px] text-primary-foreground font-bold">
                                                            {{ type.length }}
                                                        </span>
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="start" class="w-56">
                                                    <DropdownMenuLabel class="flex items-center justify-between text-xs">
                                                        Filtrar por Tipo
                                                        <span v-if="type.length > 0" class="text-[10px] text-muted-foreground cursor-pointer hover:text-primary" @click.stop="type = []">
                                                            Limpiar
                                                        </span>
                                                    </DropdownMenuLabel>
                                                    <DropdownMenuSeparator />
                                                    <DropdownMenuCheckboxItem v-for="t in branchTypes" :key="t.id" :checked="type.includes(t.id.toString())" @select.prevent="toggleType(t.id.toString())">
                                                        <span class="flex items-center justify-between w-full">
                                                            {{ t.nombre }}
                                                            <span class="text-[10px] text-muted-foreground ml-2">({{ t.branches_count }})</span>
                                                        </span>
                                                    </DropdownMenuCheckboxItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </div>
                                    </th>
                                    <th class="h-10 px-4 align-middle font-semibold">Teléfonos</th>
                                    <th class="h-10 px-4 align-middle text-right font-semibold pr-6">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="branches.data.length === 0">
                                    <td colspan="7" class="p-12 text-center text-muted-foreground">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <Building class="h-8 w-8 opacity-20" />
                                            <p>No se encontraron sucursales registradas.</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-for="(branch, index) in branches.data" :key="branch.id" class="border-b border-border/50 last:border-0 hover:bg-muted/30 transition-colors group/row">
                                    <td class="p-4 font-mono text-xs text-muted-foreground">
                                        {{ (branches.current_page - 1) * branches.per_page + index + 1 }}
                                    </td>
                                    <td class="p-4 font-mono text-xs text-muted-foreground">
                                        <span v-if="branch.codigo_ubicacion" class="bg-muted px-1.5 py-0.5 rounded">{{ branch.codigo_ubicacion }}</span>
                                        <span v-else class="opacity-50">-</span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-md bg-primary/10 flex items-center justify-center text-primary group-hover/row:scale-110 transition-transform flex-shrink-0">
                                                <Building class="h-4 w-4" />
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-medium text-foreground">{{ branch.nombre }}</span>
                                                <span class="text-[10px] text-muted-foreground truncate max-w-[200px]" :title="branch.direccion">{{ branch.direccion }}</span>
                                                <div v-if="branch.padre" class="flex items-center gap-1.5 mt-1">
                                                    <span class="text-[9px] text-muted-foreground">Vinculado a:</span>
                                                    <span class="text-[9px] font-medium text-foreground">{{ branch.padre.nombre }}</span>
                                                    <Badge variant="outline" :class="getDynamicBadgeColor(branch.padre.tipo?.color)" class="text-[8px] px-1 py-0 h-3.5">
                                                        {{ branch.padre.tipo?.nombre }}
                                                    </Badge>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-sm text-foreground">
                                        <div class="flex items-center gap-2">
                                            <MapPin class="h-3.5 w-3.5 text-muted-foreground" />
                                            {{ branch.ciudad?.nombre }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <Badge variant="outline" :class="getDynamicBadgeColor(branch.tipo?.color)" class="font-semibold text-[10px] px-2 py-0.5 rounded-md">
                                            {{ branch.tipo?.nombre }}
                                        </Badge>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-1.5 text-muted-foreground">
                                            <Phone class="h-3.5 w-3.5 opacity-70" />
                                            <span class="text-xs">{{ branch.telefonos || 'N/A' }}</span>
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
