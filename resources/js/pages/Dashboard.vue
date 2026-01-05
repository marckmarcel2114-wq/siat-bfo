<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Monitor, ShoppingCart, Wrench, Users, Activity, FileText, TrendingUp, AlertOctagon, MapPin } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

const props = defineProps<{
    stats: {
        totalAssets: number;
        totalValue: number;
        statusCounts: Record<string, number>;
        assignedCount: number;
        maintenanceCount: number;
    };
    charts: {
        assetsByCity: Array<{ name: string; value: number }>;
        assetsByType: Array<{ name: string; value: number }>;
    };
    recentAssignments: Array<any>;
}>();

const breadcrumbs = [
    { title: 'Inicio', href: '/dashboard' },
];

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB', maximumFractionDigits: 0 }).format(amount);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Panel Gerencial" />

        <div class="flex flex-col gap-6 p-4 md:p-6">
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Panel de Operaciones</h1>
                    <p class="text-muted-foreground">Visión general del estado del parque tecnológico.</p>
                </div>
                <Button variant="outline" as-child class="w-full sm:w-auto">
                    <Link :href="route('assets.index')">Ver Inventario Completo</Link>
                </Button>
            </div>

            <!-- Stats KPI Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Activos -->
                <Card class="border-t-4 border-t-blue-600 shadow-sm">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Activos</CardTitle>
                        <Monitor class="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalAssets }}</div>
                        <p class="text-xs text-muted-foreground">Valor Total: {{ formatCurrency(stats.totalValue) }}</p>
                    </CardContent>
                </Card>

                <!-- Asignados -->
                <Card class="border-t-4 border-t-green-600 shadow-sm">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">En Uso (Asignados)</CardTitle>
                        <Users class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.assignedCount }}</div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                            <div class="bg-green-600 h-1.5 rounded-full" :style="`width: ${(stats.assignedCount / stats.totalAssets) * 100}%`"></div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Mantenimiento -->
                <Card class="border-t-4 border-t-red-500 shadow-sm">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Mantenimiento</CardTitle>
                        <Wrench class="h-4 w-4 text-red-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.maintenanceCount }}</div>
                        <p class="text-xs text-muted-foreground text-red-600 font-medium">
                            Requieren atención inmediata
                        </p>
                    </CardContent>
                </Card>

                <!-- Disponibles/Stock (Calculated) -->
                <Card class="border-t-4 border-t-gray-500 shadow-sm">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">En Depósito / Vacantes</CardTitle>
                        <Box class="h-4 w-4 text-gray-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.totalAssets - stats.assignedCount - stats.maintenanceCount }}
                        </div>
                        <p class="text-xs text-muted-foreground">Disponibles para asignar</p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-7">
                
                <!-- Main Activity / Recent Feed -->
                <Card class="col-span-4 shadow-md">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Activity class="h-5 w-5 text-purple-600" /> Actividad Reciente
                        </CardTitle>
                        <CardDescription>Últimos movimientos de asignación registrados.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-6">
                            <div v-for="assign in recentAssignments" :key="assign.id" class="flex items-start gap-4">
                                <div class="mt-1 bg-blue-100 p-2 rounded-full">
                                    <FileText class="h-4 w-4 text-blue-600" />
                                </div>
                                <div class="flex-1 space-y-1">
                                    <p class="text-sm font-medium leading-none">
                                        Asignación de {{ assign.activo?.tipo_activo?.nombre }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Entregado a <span class="font-bold text-gray-700">{{ assign.usuario?.name }} {{ assign.usuario?.lastname }}</span>
                                    </p>
                                    <div class="flex items-center gap-2 pt-1">
                                        <Badge variant="outline" class="text-xs">
                                            {{ assign.activo?.codigo_activo }}
                                        </Badge>
                                        <span class="text-xs text-muted-foreground">
                                            {{ new Date(assign.fecha_asignacion).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </div>
                                <Button variant="ghost" size="sm" as-child>
                                    <Link :href="route('assets.show', assign.activo_id)">Ver</Link>
                                </Button>
                            </div>
                            
                            <div v-if="recentAssignments.length === 0" class="text-center py-8 text-muted-foreground">
                                No hay actividades recientes.
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- City Distribution Chart (Visual Bars) -->
                <Card class="col-span-3 shadow-md">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MapPin class="h-5 w-5 text-orange-600" /> Distribución Geográfica
                        </CardTitle>
                        <CardDescription>Total de activos por ciudad.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-5">
                             <div v-for="city in charts.assetsByCity" :key="city.name">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium">{{ city.name }}</span>
                                    <span class="text-sm font-bold">{{ city.value }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-gray-700 overflow-hidden">
                                    <div class="bg-orange-500 h-2.5 rounded-full" :style="{ width: Math.min((city.value / stats.totalAssets) * 100, 100) + '%' }"></div>
                                </div>
                             </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
            
            <!-- Type Distribution (Grid of mini cards) -->
             <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Inventario por Tipo</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div v-for="type in charts.assetsByType" :key="type.name" class="bg-white p-4 rounded-lg border hover:shadow-md transition-shadow">
                        <div class="text-sm font-medium text-gray-500 truncate" :title="type.name">{{ type.name }}</div>
                        <div class="text-2xl font-bold text-gray-800 mt-1">{{ type.value }}</div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
