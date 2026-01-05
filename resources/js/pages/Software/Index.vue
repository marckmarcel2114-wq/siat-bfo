<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Disc, Server, ShieldCheck, AlertTriangle, Search, Info, Monitor, MoreHorizontal } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
    licenses: Array<any>;
}>();

const searchTerm = ref('');

const filteredLicenses = computed(() => {
    if (!searchTerm.value) return props.licenses;
    return props.licenses.filter(lic => 
        lic.nombre.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        lic.tipo.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Software', href: '#' },
];

const getUsageColor = (used: number, total: number) => {
    if (total === 0) return 'bg-blue-500';
    const percentage = (used / total) * 100;
    if (percentage >= 100) return 'bg-red-500';
    if (percentage > 85) return 'bg-orange-500';
    return 'bg-blue-500';
};

const getStatusBadge = (lic: any) => {
    if (lic.fecha_expiracion && new Date(lic.fecha_expiracion) < new Date()) {
        return { label: 'Expirado', variant: 'destructive' as const };
    }
    if (lic.seats_total > 0 && lic.seats_used >= lic.seats_total) {
        return { label: 'Agotado', variant: 'outline' as const };
    }
    return { label: 'Activo', variant: 'default' as const };
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Inventario de Software" />

        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-8">
            <!-- Hero Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-200">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                        <Disc class="h-8 w-8" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Catálogo de Software</h1>
                        <p class="text-muted-foreground font-medium">Controle sus licencias, claves y asignaciones de aplicaciones.</p>
                    </div>
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <div class="relative flex-grow md:w-64">
                        <Search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input v-model="searchTerm" placeholder="Buscar software..." class="pl-9 h-11 bg-white border-slate-200 shadow-sm rounded-xl focus:ring-blue-500/20" />
                    </div>
                    <Button as-child class="h-11 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg shadow-blue-500/20">
                        <Link :href="route('software.create')">
                            <Plus class="mr-2 h-4 w-4" /> Nueva Licencia
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Dashboard Stats Brief -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <Card class="border-none shadow-sm bg-white overflow-hidden group">
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Total Aplicaciones</p>
                                <h3 class="text-3xl font-black text-slate-900 mt-1">{{ licenses.length }}</h3>
                            </div>
                            <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                                <ShieldCheck class="h-6 w-6" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
                <Card class="border-none shadow-sm bg-white overflow-hidden group">
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Instalaciones Activas</p>
                                <h3 class="text-3xl font-black text-slate-900 mt-1">{{ licenses.reduce((acc, l) => acc + l.seats_used, 0) }}</h3>
                            </div>
                            <div class="h-12 w-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 group-hover:scale-110 transition-transform">
                                <Monitor class="h-6 w-6" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
                <Card class="border-none shadow-sm bg-white overflow-hidden group">
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Críticas / Por Vencer</p>
                                <h3 class="text-3xl font-black text-slate-900 mt-1">{{ licenses.filter(l => l.seats_used >= l.seats_total && l.seats_total > 0).length }}</h3>
                            </div>
                            <div class="h-12 w-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
                                <AlertTriangle class="h-6 w-6" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Inventory Table -->
            <Card class="border-none shadow-lg rounded-2xl overflow-hidden bg-white">
                <Table>
                    <TableHeader class="bg-slate-50/50">
                        <TableRow class="hover:bg-transparent border-slate-100">
                            <TableHead class="py-5 font-bold text-slate-500 uppercase text-[10px]">Software / Key</TableHead>
                            <TableHead class="font-bold text-slate-500 uppercase text-[10px]">Tipo</TableHead>
                            <TableHead class="font-bold text-slate-500 uppercase text-[10px]">Utilización de Asientos</TableHead>
                            <TableHead class="font-bold text-slate-500 uppercase text-[10px]">Fecha Expiración</TableHead>
                            <TableHead class="font-bold text-slate-500 uppercase text-[10px] text-right">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="lic in filteredLicenses" :key="lic.id" class="group hover:bg-slate-50/50 border-slate-50 transition-colors">
                            <TableCell class="py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                        <Disc class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <Link :href="route('software.show', lic.id)" class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors block leading-tight">
                                            {{ lic.nombre }}
                                        </Link>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <span class="text-[11px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded leading-none" v-if="lic.key">
                                                {{ lic.key.substring(0, 10) }}...
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-medium" v-if="lic.proveedor">
                                                • {{ lic.proveedor?.nombre }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge variant="outline" class="font-semibold text-xs border-slate-200 text-slate-600">
                                    {{ lic.tipo }}
                                </Badge>
                                <Badge v-if="getStatusBadge(lic).label !== 'Activo'" :variant="getStatusBadge(lic).variant" class="ml-2 scale-90">
                                    {{ getStatusBadge(lic).label }}
                                </Badge>
                            </TableCell>
                            <TableCell class="w-[240px]">
                                <div class="flex justify-between text-[11px] mb-1.5 px-0.5">
                                    <span class="font-bold text-slate-700">{{ lic.seats_used }} / {{ lic.seats_total }} <span class="text-slate-400 font-normal">equipos</span></span>
                                    <span class="font-bold text-blue-600">{{ Math.round((lic.seats_used / (lic.seats_total || 1)) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-700 ease-out" 
                                        :class="getUsageColor(lic.seats_used, lic.seats_total)"
                                        :style="`width: ${Math.min((lic.seats_used / (lic.seats_total || 1)) * 100, 100)}%`">
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div v-if="lic.fecha_expiracion" class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-slate-400" />
                                    <span class="text-sm font-medium" :class="new Date(lic.fecha_expiracion) < new Date() ? 'text-red-500 font-bold' : 'text-slate-600'">
                                        {{ new Date(lic.fecha_expiracion).toLocaleDateString() }}
                                    </span>
                                </div>
                                <div v-else class="flex items-center gap-2 italic text-slate-400 text-xs">
                                    <Info class="h-3.5 w-3.5" /> Perpetua
                                </div>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-1">
                                    <Button variant="ghost" size="icon" as-child class="rounded-full h-9 w-9 text-slate-400 hover:text-blue-600 hover:bg-blue-50">
                                        <Link :href="route('software.show', lic.id)">
                                            <MoreHorizontal class="h-5 w-5" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="icon" as-child class="rounded-full h-9 w-9 text-slate-400 hover:text-blue-600 hover:bg-blue-50">
                                        <Link :href="route('software.edit', lic.id)">
                                            <MoreHorizontal class="h-5 w-5 rotate-90" />
                                        </Link>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                         <TableRow v-if="filteredLicenses.length === 0">
                            <TableCell colspan="5" class="h-64 text-center">
                                <div class="flex flex-col items-center justify-center gap-4 opacity-40">
                                    <Disc class="h-16 w-16" />
                                    <p class="text-lg font-medium">No se encontraron licencias registradas.</p>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </Card>

            <!-- Bottom Disclaimer -->
            <div class="flex items-center justify-center gap-2 text-[11px] text-muted-foreground">
                <ShieldCheck class="h-3 w-3" />
                <span>Base de datos normalizada para trazabilidad de licencias corporativas e individuales.</span>
            </div>
        </div>
    </AppLayout>
</template>
