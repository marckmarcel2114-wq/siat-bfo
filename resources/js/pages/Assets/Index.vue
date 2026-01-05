<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { debounce } from 'lodash';

// UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { 
    Search, Plus,  Edit, Eye, Trash2, UserPlus, Download, 
    FileSpreadsheet, FileText, ChevronDown, ChevronRight, MapPin, Building,
    FilterX, Settings2, Check
} from 'lucide-vue-next';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger, DropdownMenuSeparator, DropdownMenuCheckboxItem, DropdownMenuLabel } from '@/components/ui/dropdown-menu';
// Removed Popover import as it is not available

interface Asset {
    id: number;
    codigo_activo: string;
    numero_serie: string;
    tipo_activo?: { nombre: string };
    modelo?: { nombre: string; marca?: { nombre: string } };
    ubicacion?: { 
        id: number;
        nombre: string;
        ciudad?: { id: number; nombre: string };
        tipo?: { id: number; nombre: string };
    };
    detalle_ubicacion?: string;
    estado_activo?: { nombre: string };
    propietario?: { nombre: string };
    nivel_criticidad?: { nombre: string; color: string };
    atributos?: Array<{ valor: string; definicion?: { nombre: string } }>;
}

const props = defineProps<{
    assets: {
        data: Asset[];
        links: any[];
        meta: any;
        total: number;
        from: number;
        to: number;
    };
    tipos: any[];
    estados: any[];
    cities: any[];
    ubicaciones: any[]; 
    procesadores: string[];
    generaciones: string[];
    ram_capacidades: string[];
    ram_tipos: string[];
    disco_capacidades: string[];
    disco_tipos: string[];
    filters: any;
    propietarios: any[];
}>();

// --- Filter State ---
// We keep filter state but move UI to headers
const search = ref(props.filters.search || '');
const cityFilter = ref(props.filters.city_id?.toString() || 'all'); 
const locationFilter = ref(props.filters.ubicacion_id?.toString() || 'all');
const typeFilter = ref(props.filters.tipo_activo_id?.toString() || 'all');
const statusFilter = ref(props.filters.estado_activo_id?.toString() || 'all');
// Owner filter removed per user request

// EAV Filters (Grouped in a "Tech Specs" popover to save space)
const specsFilters = ref({
    procesador: props.filters.procesador || 'all',
    generacion: props.filters.generacion || 'all',
    ram_capacidad: props.filters.ram_capacidad || 'all',
    ram_tipo: props.filters.ram_tipo || 'all',
    disco_capacidad: props.filters.disco_capacidad || 'all',
    disco_tipo: props.filters.disco_tipo || 'all',
});

// --- Grouping Logic (Client-side view of the current page) ---
// Note: real grouping across pages requires backend changes, but we enforce sort in backend so pagination "flows" correctly.
const expandedCities = ref<Set<string>>(new Set()); // Start collapsed for "smart" feel? Or expanded? User requested "starts by...", usually implies seen. Let's auto-expand all initially or just track toggles.
// Actually, for a table view, expanding all by default is better usability.
const toggleCity = (cityName: string) => {
    if (expandedCities.value.has(cityName)) expandedCities.value.delete(cityName);
    else expandedCities.value.add(cityName);
};

// Initial Expansion: Expand all cities present on current page
// We will use a watcher or onMounted, but simply checking if it has it is enough.
// Let's invert: valid means COLLAPSED. So empty set = all expanded.
// --- Dynamic Columns ---
// Note: Código and Marca/Modelo are permanent columns per user's "vista general" request.
const columns = ref([
    { id: 'tipo', label: 'Tipo', visible: true },
    { id: 'specs', label: 'Especificaciones', visible: true },
    { id: 'ubicacion', label: 'Detalle Ubic.', visible: true },
    { id: 'estado', label: 'Estado', visible: true },
    { id: 'propietario', label: 'Propietario', visible: true },
    { id: 'criticidad', label: 'Criticidad', visible: true },
]);

const visibleColumnCount = computed(() => {
    return columns.value.filter(c => c.visible).length + 4; // 2 toggles/actions + 2 permanent (Código, Modelo)
});

const isColumnVisible = (id: string) => columns.value.find(c => c.id === id)?.visible;

const toggleColumn = (id: string) => {
    const col = columns.value.find(c => c.id === id);
    if (col) col.visible = !col.visible;
};

const collapsedGroups = ref<Set<string>>(new Set());

const isExpanded = (key: string) => !collapsedGroups.value.has(key);
const toggleGroup = (key: string) => {
    if (collapsedGroups.value.has(key)) collapsedGroups.value.delete(key);
    else collapsedGroups.value.add(key);
};

// Computed Grouped Data
// Structure: City -> Location -> Assets
const groupedData = computed(() => {
    const groups: Record<string, Record<string, Asset[]>> = {};
    
    props.assets.data.forEach(asset => {
        const city = asset.ubicacion?.ciudad?.nombre || 'Sin Ciudad';
        const location = asset.ubicacion?.nombre || 'Sin Ubicación';
        
        if (!groups[city]) groups[city] = {};
        if (!groups[city][location]) groups[city][location] = [];
        
        groups[city][location].push(asset);
    });
    
    // Sort keys just in case, though backend should have delivered them sorted
    return groups;
});

// --- Computed Filters Helper ---
const filteredLocations = computed(() => {
    if (!cityFilter.value || cityFilter.value === 'all') return [];
    return props.ubicaciones.filter(u => u.ciudad_id?.toString() === cityFilter.value);
});


// --- Search Trigger ---
const triggerSearch = debounce(() => {
    const params = {
        search: search.value,
        city_id: cityFilter.value === 'all' ? null : cityFilter.value,
        ubicacion_id: locationFilter.value === 'all' ? null : locationFilter.value,
        tipo_activo_id: typeFilter.value === 'all' ? null : typeFilter.value,
        estado_activo_id: statusFilter.value === 'all' ? null : statusFilter.value,
        
        // Spread specs
        procesador: specsFilters.value.procesador === 'all' ? null : specsFilters.value.procesador,
        generacion: specsFilters.value.generacion === 'all' ? null : specsFilters.value.generacion,
        ram_capacidad: specsFilters.value.ram_capacidad === 'all' ? null : specsFilters.value.ram_capacidad,
        ram_tipo: specsFilters.value.ram_tipo === 'all' ? null : specsFilters.value.ram_tipo,
        disco_capacidad: specsFilters.value.disco_capacidad === 'all' ? null : specsFilters.value.disco_capacidad,
        disco_tipo: specsFilters.value.disco_tipo === 'all' ? null : specsFilters.value.disco_tipo,
    };

    router.get(route('assets.index'), params, { 
        preserveState: true, 
        preserveScroll: true, 
        replace: true 
    });
}, 300);

// Deep watch on all filter refs
watch([search, cityFilter, locationFilter, typeFilter, statusFilter, specsFilters], triggerSearch, { deep: true });

const clearFilters = () => {
    search.value = '';
    cityFilter.value = 'all';
    locationFilter.value = 'all';
    typeFilter.value = 'all';
    statusFilter.value = 'all';
    specsFilters.value = {
        procesador: 'all', generacion: 'all', ram_capacidad: 'all', 
        ram_tipo: 'all', disco_capacidad: 'all', disco_tipo: 'all'
    };
    triggerSearch();
};

const hasActiveFilters = computed(() => {
    return search.value || cityFilter.value !== 'all' || typeFilter.value !== 'all' || statusFilter.value !== 'all' ||
           Object.values(specsFilters.value).some(v => v !== 'all');
});

// --- Helpers ---
const getStatusClasses = (status?: string) => {
    switch (status?.toLowerCase()) {
        case 'activo': return 'bg-green-100 text-green-800 border-green-200';
        case 'disponible': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'asignado': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'en mantenimiento': return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'baja': return 'bg-red-100 text-red-800 border-red-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};

const getAttributeValue = (asset: Asset, attrName: string) => {
    return asset.atributos?.find(a => a.definicion?.nombre.toLowerCase() === attrName.toLowerCase())?.valor || '-';
};

const handleExport = (format: 'csv' | 'pdf') => {
    // Logic remains same (uses current filter state)
    // ... (Simplified for brevity implies reusing current URL params mainly)
    const currentParams = new URLSearchParams(window.location.search);
    window.location.href = route('assets.export', { format: format }) + '?' + currentParams.toString();
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Activos', href: route('assets.index') },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Inventario de Activos" />

        <div class="max-w-[98%] mx-auto px-2 py-4">
            
            <!-- Header Title Only - Filters Moved to Table -->
            <div class="flex flex-col gap-4 mb-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Inventario General</h1>
                         <Badge class="bg-blue-100 text-blue-700 hover:bg-blue-200 border-blue-200 font-bold px-2 py-1 shadow-sm">
                             GLOBAL: {{ assets.total }} REGISTROS
                         </Badge>
                         <Button v-if="hasActiveFilters" variant="ghost" size="sm" @click="clearFilters" class="h-6 text-red-500 hover:text-red-700 hover:bg-red-50 text-xs gap-1 px-2">
                            <FilterX class="h-3 w-3" /> Limpiar
                         </Button>
                    </div>
                    
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <!-- Global Search -->
                        <div class="relative flex-1 sm:w-64">
                             <Search class="absolute left-2 top-2 h-3.5 w-3.5 text-gray-400" />
                             <Input 
                                v-model="search"
                                placeholder="Buscar código, serie, marca..." 
                                class="h-8 text-xs pl-8 bg-white w-full"
                            />
                        </div>

                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="outline" size="sm" class="h-8 gap-2 shadow-sm shrink-0">
                                    <Settings2 class="h-3.5 w-3.5" />
                                    <span class="hidden sm:inline">Columnas</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-56">
                                <DropdownMenuLabel>Personalizar Vista</DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem 
                                    v-for="col in columns" 
                                    :key="col.id"
                                    @select.prevent="toggleColumn(col.id)"
                                    class="flex items-center justify-between cursor-pointer"
                                >
                                    <span>{{ col.label }}</span>
                                    <Check v-if="col.visible" class="h-4 w-4 text-blue-600" />
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="outline" size="sm" class="h-8 gap-2 shadow-sm shrink-0">
                                    <Download class="h-3.5 w-3.5" />
                                    <span class="hidden sm:inline">Exportar</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem @click="handleExport('pdf')" class="cursor-pointer">PDF</DropdownMenuItem>
                                <DropdownMenuItem @click="handleExport('csv')" class="cursor-pointer">Excel (CSV)</DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
    
                        <Button as-child size="sm" class="h-8 bg-blue-700 hover:bg-blue-800 text-white shadow-sm gap-1 shrink-0">
                            <Link :href="route('assets.create')">
                                <Plus class="h-3.5 w-3.5" /> Nuevo
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>

             <!-- Smart Table -->
             <div class="bg-white rounded-md shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[calc(100vh-180px)]">
                <div class="overflow-auto flex-1">
                    <table class="w-full text-left text-xs border-collapse relative">
                        <!-- Sticky Header with Integrated Filters -->
                        <thead class="bg-gray-50 sticky top-0 z-20 shadow-sm">
                            <tr>
                                <!-- Columns Definition -->
                                <th class="p-2 w-10 border-b border-gray-200 bg-gray-50"></th> <!-- Group Toggle Space -->
                                
                                <th class="p-2 border-b border-gray-200 bg-gray-50 min-w-[100px]">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-gray-700">CÓDIGO</span>
                                    </div>
                                </th>

                                <th v-if="isColumnVisible('tipo')" class="p-2 border-b border-gray-200 bg-gray-50 min-w-[80px]">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-gray-700">TIPO</span>
                                        <select v-model="typeFilter" class="h-6 text-[10px] p-0 px-1 border-gray-300 rounded focus:ring-1 focus:ring-blue-500 bg-white">
                                            <option value="all">Todos</option>
                                            <option v-for="t in tipos" :key="t.id" :value="t.id.toString()">{{ t.nombre }}</option>
                                        </select>
                                    </div>
                                </th>

                                <th class="p-2 border-b border-gray-200 bg-gray-50">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-gray-700">MARCA / MODELO / S.N.</span>
                                    </div>
                                </th>

                                <th v-if="isColumnVisible('specs')" class="p-2 border-b border-gray-200 bg-gray-50 min-w-[140px]">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex justify-between items-center">
                                            <span class="font-semibold text-gray-700">ESPECIFICACIONES</span>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <span class="cursor-pointer text-blue-600 hover:text-blue-800" title="Filtrar Specs">
                                                        <FilterX v-if="Object.values(specsFilters).some(v => v !== 'all')" class="h-3 w-3" />
                                                        <ChevronDown v-else class="h-3 w-3" />
                                                    </span>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent class="w-80 p-3" align="start">
                                                    <div class="grid grid-cols-2 gap-3" @click.stop>
                                                        <div class="space-y-1">
                                                            <label class="text-[10px] font-bold text-gray-500">PROCESADOR</label>
                                                            <select v-model="specsFilters.procesador" class="w-full text-xs border-gray-300 rounded h-7">
                                                                <option value="all">Todos</option>
                                                                <option v-for="p in procesadores" :value="p">{{ p }}</option>
                                                            </select>
                                                        </div>
                                                        <div class="space-y-1">
                                                            <label class="text-[10px] font-bold text-gray-500">GENERACIÓN</label>
                                                            <select v-model="specsFilters.generacion" class="w-full text-xs border-gray-300 rounded h-7">
                                                                <option value="all">Todas</option>
                                                                <option v-for="g in generaciones" :value="g">{{ g }}</option>
                                                            </select>
                                                        </div>
                                                        <div class="space-y-1">
                                                            <label class="text-[10px] font-bold text-gray-500">RAM</label>
                                                            <select v-model="specsFilters.ram_capacidad" class="w-full text-xs border-gray-300 rounded h-7">
                                                                <option value="all">Todas</option>
                                                                <option v-for="r in ram_capacidades" :value="r">{{ r }}</option>
                                                            </select>
                                                        </div>
                                                         <div class="space-y-1">
                                                            <label class="text-[10px] font-bold text-gray-500">DISCO</label>
                                                            <select v-model="specsFilters.disco_capacidad" class="w-full text-xs border-gray-300 rounded h-7">
                                                                <option value="all">Todas</option>
                                                                <option v-for="d in disco_capacidades" :value="d">{{ d }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </div>
                                    </div>
                                </th>

                                <th v-if="isColumnVisible('ubicacion')" class="p-2 border-b border-gray-200 bg-gray-50">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-gray-700">UBICACIÓN</span>
                                        <div class="flex gap-1" v-if="cityFilter === 'all'">
                                            <select v-model="cityFilter" class="h-6 text-[10px] p-0 px-1 border-gray-300 rounded focus:ring-1 focus:ring-blue-500 bg-white max-w-[100px]">
                                                <option value="all">Todas Ciudades</option>
                                                <option v-for="c in cities" :key="c.id" :value="c.id.toString()">{{ c.nombre }}</option>
                                            </select>
                                        </div>
                                         <div class="flex gap-1" v-else>
                                            <span class="text-[10px] font-bold text-blue-700 self-center uppercase">{{ cities.find(c => c.id.toString() === cityFilter)?.nombre }}</span>
                                             <button @click="cityFilter='all'" class="text-red-500 hover:text-red-700" title="Quitar filtro de ciudad">
                                                <FilterX class="h-3 w-3" />
                                            </button>
                                        </div>
                                    </div>
                                </th>

                                <th v-if="isColumnVisible('estado')" class="p-2 border-b border-gray-200 bg-gray-50 min-w-[80px]">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-gray-700">ESTADO</span>
                                        <select v-model="statusFilter" class="h-6 text-[10px] p-0 px-1 border-gray-300 rounded focus:ring-1 focus:ring-blue-500 bg-white">
                                            <option value="all">Todos</option>
                                            <option v-for="e in estados" :key="e.id" :value="e.id.toString()">{{ e.nombre }}</option>
                                        </select>
                                    </div>
                                </th>

                                <th v-if="isColumnVisible('propietario')" class="p-2 border-b border-gray-200 bg-gray-50 min-w-[100px]">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-gray-700 uppercase">Propietario</span>
                                    </div>
                                </th>

                                <th v-if="isColumnVisible('criticidad')" class="p-2 border-b border-gray-200 bg-gray-50 min-w-[100px]">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-gray-700 uppercase">Criticidad</span>
                                    </div>
                                </th>
                                
                                <th class="p-2 border-b border-gray-200 bg-gray-50 text-right"></th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                             <template v-if="assets.data.length === 0">
                                 <tr>
                                    <td :colspan="visibleColumnCount" class="p-8 text-center text-gray-500">
                                        No se encontraron resultados
                                    </td>
                                </tr>
                             </template>
                             
                             <!-- Group Loop: City -->
                             <template v-for="(locations, cityName) in groupedData" :key="cityName">
                                 <!-- City Header Row -->
                                 <tr class="bg-blue-50/50 hover:bg-blue-50 cursor-pointer select-none" @click="toggleGroup(cityName)">
                                     <td class="p-2 border-b border-blue-100 text-center">
                                         <component :is="isExpanded(cityName) ? ChevronDown : ChevronRight" class="h-3.5 w-3.5 text-blue-500" />
                                     </td>
                                      <td :colspan="visibleColumnCount - 1" class="p-2 border-b border-blue-100">
                                         <div class="flex items-center gap-2">
                                             <MapPin class="h-3.5 w-3.5 text-blue-600" />
                                             <span class="font-bold text-blue-800 uppercase text-xs tracking-wider">{{ cityName }}</span>
                                             <div class="ml-auto">
                                                 <Badge class="bg-blue-600 text-white hover:bg-blue-700 font-bold px-2 py-0.5 text-[10px] shadow-sm">
                                                     CANTIDAD: {{ Object.values(locations).reduce((acc, curr) => acc + curr.length, 0) }}
                                                 </Badge>
                                             </div>
                                         </div>
                                     </td>
                                 </tr>

                                 <!-- Location Loop -->
                                 <template v-if="isExpanded(cityName)">
                                     <template v-for="(groupAssets, locationName) in locations" :key="cityName + locationName">
                                         <!-- Location Sub-Header Row -->
                                         <tr class="bg-gray-50/80 hover:bg-gray-100 cursor-pointer select-none" @click="toggleGroup(cityName + locationName)">
                                             <td class="p-2 border-b border-gray-200"></td> <!-- Indent -->
                                             <td :colspan="visibleColumnCount - 1" class="p-2 border-b border-gray-200 pl-4">
                                                 <div class="flex items-center gap-2">
                                                     <component :is="isExpanded(cityName + locationName) ? ChevronDown : ChevronRight" class="h-3 w-3 text-gray-400" />
                                                     <Building class="h-3.5 w-3.5 text-gray-500" />
                                                     <div class="flex items-center gap-1.5">
                                                         <span class="font-semibold text-gray-700 text-xs">{{ locationName }}</span>
                                                         <Badge variant="outline" class="text-[9px] h-3.5 px-1 bg-white border-blue-200 text-blue-600 font-normal uppercase">
                                                             {{ groupAssets[0]?.ubicacion?.tipo?.nombre || '-' }}
                                                         </Badge>
                                                     </div>
                                                     <div class="ml-auto">
                                                         <Badge variant="outline" class="font-bold text-gray-700 border-gray-300 bg-gray-100/50 px-2 text-[10px]">
                                                             TOTAL: {{ groupAssets.length }}
                                                         </Badge>
                                                     </div>
                                                 </div>
                                             </td>
                                         </tr>

                                         <!-- Assets Rows -->
                                         <template v-if="isExpanded(cityName + locationName)">
                                             <tr v-for="asset in groupAssets" :key="asset.id" class="group hover:bg-blue-50/30 transition-colors border-b border-gray-100 last:border-0 relative">
                                                 <td class="p-2"></td>
                                                  <td class="p-2 pl-8 font-mono font-medium text-blue-600">
                                                      {{ asset.codigo_activo }}
                                                  </td>
                                                  <td v-if="isColumnVisible('tipo')" class="p-2">
                                                      <Badge variant="outline" class="text-[10px] font-normal uppercase bg-white border-gray-200 text-gray-600">
                                                          {{ asset.tipo_activo?.nombre }}
                                                      </Badge>
                                                  </td>
                                                  <td class="p-2">
                                                      <div class="flex flex-col">
                                                          <span class="font-semibold text-gray-800 text-[11px]">{{ asset.modelo?.marca?.nombre }} {{ asset.modelo?.nombre }}</span>
                                                          <span class="text-[10px] text-gray-400 font-mono">SN: {{ asset.numero_serie }}</span>
                                                      </div>
                                                  </td>
                                                  <td v-if="isColumnVisible('specs')" class="p-2">
                                                      <div class="flex flex-col gap-0.5">
                                                          <span class="text-[11px] leading-tight">{{ getAttributeValue(asset, 'Procesador') }}</span>
                                                          <div class="flex gap-2 text-[10px] text-gray-500">
                                                              <span>{{ getAttributeValue(asset, 'Capacidad de Memoria') }}</span>
                                                              <span>{{ getAttributeValue(asset, 'Capacidad de Disco') }}</span>
                                                          </div>
                                                      </div>
                                                  </td>
                                                  <td v-if="isColumnVisible('ubicacion')" class="p-2 text-[10px] text-gray-500">
                                                      {{ asset.detalle_ubicacion || '-' }}
                                                  </td>
                                                  <td v-if="isColumnVisible('estado')" class="p-2">
                                                      <span class="px-2 py-0.5 rounded text-[10px] font-medium border" :class="getStatusClasses(asset.estado_activo?.nombre)">
                                                          {{ asset.estado_activo?.nombre }}
                                                      </span>
                                                  </td>
                                                  <td v-if="isColumnVisible('propietario')" class="p-2 text-[10px] text-gray-600">
                                                      {{ asset.propietario?.nombre || '-' }}
                                                  </td>
                                                  <td v-if="isColumnVisible('criticidad')" class="p-2">
                                                      <Badge v-if="asset.nivel_criticidad" variant="outline" class="text-[10px] font-bold" :style="{ borderColor: asset.nivel_criticidad.color, color: asset.nivel_criticidad.color }">
                                                          {{ asset.nivel_criticidad.nombre }}
                                                      </Badge>
                                                      <span v-else>-</span>
                                                  </td>
                                                 <td class="p-2 text-right">
                                                     <div class="flex justify-end gap-1">
                                                        <Button variant="ghost" size="icon" class="h-6 w-6 text-gray-400 hover:text-blue-600" as-child title="Ver">
                                                            <Link :href="route('assets.show', asset.id)"><Eye class="h-3.5 w-3.5" /></Link>
                                                        </Button>
                                                        <Button variant="ghost" size="icon" class="h-6 w-6 text-gray-400 hover:text-green-600" as-child title="Editar">
                                                            <Link :href="route('assets.edit', asset.id)"><Edit class="h-3.5 w-3.5" /></Link>
                                                        </Button>
                                                        <Button variant="ghost" size="icon" class="h-6 w-6 text-gray-400 hover:text-indigo-600" as-child title="Asignar">
                                                            <Link :href="route('assignments.create', { asset_id: asset.id })"><UserPlus class="h-3.5 w-3.5" /></Link>
                                                        </Button>
                                                     </div>
                                                 </td>
                                             </tr>
                                         </template>
                                     </template>
                                 </template>
                             </template>
                        </tbody>
                    </table>
                </div>

                <!-- Simple Footer -->
                 <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 flex items-center justify-between shadow-[0_-1px_2px_rgba(0,0,0,0.05)] z-30">
                     <div class="text-[10px] text-gray-500">
                         Página <span class="font-medium">{{ assets.meta?.current_page || 1 }}</span> de <span class="font-medium">{{ assets.meta?.last_page || 1 }}</span>
                     </div>
                     <div class="flex space-x-1">
                        <template v-for="(link, k) in assets.links" :key="k">
                             <Link 
                                v-if="link.url"
                                :href="link.url"
                                class="px-2 py-1 text-[10px] border rounded transition-colors min-w-[24px] text-center"
                                :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'"
                                v-html="link.label"
                             />
                        </template>
                     </div>
                </div>
             </div>

        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom scrollbar for table body if needed */
.overflow-auto::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.overflow-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.overflow-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
