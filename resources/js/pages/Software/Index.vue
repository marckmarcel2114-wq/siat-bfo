<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Disc, Server, ShieldCheck, AlertTriangle, Search, Info, Monitor, MoreHorizontal, ChevronDown, ChevronRight, Briefcase } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
    softwareStats: Array<any>;
    orphanedLicenses: Array<any>;
}>();

const searchTerm = ref('');
const expandedRows = ref<Record<number, boolean>>({});

const toggleRow = (id: number) => {
    expandedRows.value[id] = !expandedRows.value[id];
};

const filteredSoftware = computed(() => {
    if (!searchTerm.value) return props.softwareStats;
    return props.softwareStats.filter(soft => 
        soft.nombre.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        soft.licenses.some((l: any) => l.tipo.toLowerCase().includes(searchTerm.value.toLowerCase()))
    );
});

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Software', href: '#' },
];

const getCoverageColor = (percent: number) => {
    if (percent === 100) return 'bg-emerald-500';
    if (percent > 80) return 'bg-blue-500';
    if (percent > 50) return 'bg-orange-500';
    return 'bg-red-500';
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Inventario de Software" />

        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-8">
            <!-- Hero Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-200">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                        <Disc class="h-8 w-8" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Gestión Unificada de Software</h1>
                        <p class="text-muted-foreground font-medium">Controle instalaciones técnicas y cobertura de licencias en un solo lugar.</p>
                    </div>
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <div class="relative flex-grow md:w-64">
                         <Search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                         <Input v-model="searchTerm" placeholder="Buscar software..." class="pl-9 h-11 bg-white border-slate-200 shadow-sm rounded-xl focus:ring-blue-500/20" />
                    </div>
                    <Button as-child variant="outline" class="h-11 px-4 bg-white hover:bg-slate-50 text-slate-700 border-slate-200 rounded-xl shadow-sm">
                        <Link :href="route('software-catalog.index')" title="Gestionar Nombres y Versiones">
                            <Briefcase class="mr-2 h-4 w-4" /> Catálogo
                        </Link>
                    </Button>
                    <Button as-child class="h-11 px-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-lg shadow-indigo-500/20">
                        <Link :href="route('software.create')">
                             <Plus class="mr-2 h-4 w-4" /> Agregar Contrato
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- UNIFIED TABLE -->
            <Card class="border-none shadow-lg rounded-2xl overflow-hidden bg-white">
                <Table>
                    <TableHeader class="bg-indigo-50/50">
                        <TableRow class="hover:bg-transparent border-indigo-100">
                            <TableHead class="w-[50px]"></TableHead>
                            <TableHead class="py-5 font-bold text-indigo-900 uppercase text-[11px] tracking-wider">Software (Catálogo)</TableHead>
                            <TableHead class="font-bold text-indigo-900 uppercase text-[11px] tracking-wider text-center">Instalaciones Totales</TableHead>
                            <TableHead class="font-bold text-indigo-900 uppercase text-[11px] tracking-wider text-center">Licencias / Cupos</TableHead>
                            <TableHead class="font-bold text-indigo-900 uppercase text-[11px] tracking-wider text-center">Cobertura</TableHead>
                            <TableHead class="w-[100px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-for="item in filteredSoftware" :key="item.id">
                            <!-- PARENT ROW: Software Item -->
                            <TableRow class="hover:bg-slate-50 cursor-pointer" @click="toggleRow(item.id)">
                                <TableCell>
                                    <Button variant="ghost" size="icon" class="h-6 w-6">
                                        <ChevronDown v-if="expandedRows[item.id]" class="h-4 w-4 text-slate-500" />
                                        <ChevronRight v-else class="h-4 w-4 text-slate-400" />
                                    </Button>
                                </TableCell>
                                <TableCell class="py-5">
                                    <div class="flex flex-col">
                                        <span class="text-base font-bold text-slate-800">{{ item.nombre }}</span>
                                        <span class="text-xs text-slate-400 font-medium">{{ item.fabricante }}</span>
                                    </div>
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge variant="secondary" class="font-mono text-sm px-3 py-1 bg-slate-100 text-slate-700">
                                        {{ item.installations_count }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-center">
                                     <div v-if="item.seats_total > 0" class="flex flex-col items-center">
                                        <span class="font-bold text-slate-700 text-sm">{{ item.seats_used }} / {{ item.seats_total }}</span>
                                        <span class="text-[10px] text-slate-400">Asientos Usados</span>
                                     </div>
                                     <span v-else class="text-xs text-slate-400 italic">No hay licencias vinculadas</span>
                                </TableCell>
                                <TableCell>
                                    <div class="w-32 mx-auto">
                                        <div class="flex justify-between text-[10px] mb-1 font-bold text-slate-600">
                                            <span>Uso</span>
                                            <span>{{ item.coverage_percent }}%</span>
                                        </div>
                                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full transition-all duration-500" 
                                                :class="getCoverageColor(item.coverage_percent)" 
                                                :style="`width: ${Math.min(item.coverage_percent, 100)}%`">
                                            </div>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button variant="ghost" size="sm" class="text-xs text-indigo-600 font-bold hover:bg-indigo-50">
                                        Detalles
                                    </Button>
                                </TableCell>
                            </TableRow>

                            <!-- CHILD ROW: Licenses Details -->
                            <TableRow v-if="expandedRows[item.id]" class="bg-indigo-50/20 border-b border-indigo-100">
                                <TableCell colspan="6" class="p-0">
                                    <div class="p-4 pl-16 grid gap-2">
                                        <h4 class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider mb-2">Contratos y Licencias Vinculadas</h4>
                                        <div v-if="item.licenses.length === 0" class="text-sm text-slate-400 italic px-4 py-2 border border-dashed rounded bg-white">
                                            No hay contratos registrados para este software (Instalaciones libres o sin regularizar).
                                        </div>
                                        <div v-for="lic in item.licenses" :key="lic.id" class="flex items-center justify-between bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                                            <div class="flex items-center gap-3">
                                                <Badge :class="lic.tipo === 'OEM' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-blue-100 text-blue-700 border-blue-200'">{{ lic.tipo }}</Badge>
                                                <span class="font-medium text-slate-700 text-sm">ID: {{ lic.id }} - {{ lic.scope }} Scope</span>
                                            </div>
                                            <div class="flex items-center gap-6">
                                                <div class="text-xs">
                                                    <span class="font-bold">{{ lic.seats_used }}</span> ocupados de 
                                                    <span class="font-bold">{{ lic.seats_total }}</span>
                                                </div>
                                                <Button size="icon" variant="ghost" as-child class="h-8 w-8 text-slate-400 hover:text-indigo-600">
                                                    <Link :href="route('software.show', lic.id)"><MoreHorizontal class="h-4 w-4" /></Link>
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </template>
                    </TableBody>
                </Table>
            </Card>

            <!-- Bottom Disclaimer -->
            <div class="flex items-center justify-center gap-2 text-[11px] text-muted-foreground">
                <ShieldCheck class="h-3 w-3" />
                <span>Vista unificada de inventario técnico y activos legales.</span>
            </div>
        </div>
    </AppLayout>
</template>
