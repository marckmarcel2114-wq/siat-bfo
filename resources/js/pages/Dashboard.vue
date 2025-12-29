<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Monitor, ShoppingCart, Wrench, Users, Activity, FileText } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

const props = defineProps<{
    stats: {
        totalAssets: number;
        statusCounts: Record<string, number>;
        pendingProcurements: number;
        activeMaintenances: number;
    };
    charts: {
        assetsByCity: Array<{ name: string; value: number }>;
    };
    recentAssignments: Array<any>;
    myRequests: Array<any>;
}>();

const breadcrumbs = [
    {
        title: 'Inicio',
        href: '/dashboard',
    },
];

const getStatusColor = (status: string) => {
    switch(status) {
        case 'free': return 'text-green-600 bg-green-50';
        case 'assigned': return 'text-blue-600 bg-blue-50';
        case 'maintenance': return 'text-yellow-600 bg-yellow-50'; 
        case 'broken': return 'text-red-600 bg-red-50';
        default: return 'text-gray-600 bg-gray-50';
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Panel Principal" />

        <div class="flex flex-col gap-6 p-4 md:p-6">
            <!-- Welcome Header -->
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">Bienvenido al SIAT</h1>
                <p class="text-muted-foreground">Resumen global del estado de inventarios.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card class="border-border/50 shadow-sm">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Activos</CardTitle>
                        <Monitor class="h-4 w-4 text-primary" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalAssets }}</div>
                        <p class="text-xs text-muted-foreground">Equipos registrados</p>
                    </CardContent>
                </Card>
                <Card class="border-border/50 shadow-sm">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Asignados</CardTitle>
                        <Users class="h-4 w-4 text-blue-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.statusCounts.assigned || 0 }}</div>
                        <p class="text-xs text-muted-foreground">En custodia de personal</p>
                    </CardContent>
                </Card>
                 <Card class="border-border/50 shadow-sm">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Solicitudes Pendientes</CardTitle>
                        <ShoppingCart class="h-4 w-4 text-orange-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.pendingProcurements }}</div>
                        <p class="text-xs text-muted-foreground">Requieren autorización</p>
                    </CardContent>
                </Card>
                 <Card class="border-border/50 shadow-sm">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Mantenimiento</CardTitle>
                        <Wrench class="h-4 w-4 text-yellow-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.statusCounts.maintenance || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Equipos en revisión</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Area with 2 Columns -->
            <div class="grid gap-6 md:grid-cols-7">
                
                <!-- Recent Assignments (List) -->
                <Card class="col-span-4 border-border/50 shadow-sm">
                    <CardHeader>
                        <CardTitle>Últimas Asignaciones</CardTitle>
                        <CardDescription>Movimientos recientes de inventario.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="assign in recentAssignments" :key="assign.id" class="flex items-center justify-between border-b border-border/50 pb-4 last:border-0 last:pb-0">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10">
                                        <Activity class="h-4 w-4 text-primary" />
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium leading-none">{{ assign.asset?.type?.name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ assign.user?.name }}</p>
                                    </div>
                                </div>
                                <div class="text-right text-xs text-muted-foreground">
                                    {{ new Date(assign.assigned_at).toLocaleDateString() }}
                                </div>
                            </div>
                             <div v-if="recentAssignments.length === 0" class="text-center py-4 text-muted-foreground text-sm">
                                No hay asignaciones recientes.
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Assets by City (Quick List) -->
                <Card class="col-span-3 border-border/50 shadow-sm">
                    <CardHeader>
                        <CardTitle>Activos por Ciudad</CardTitle>
                        <CardDescription>Distribución geográfica.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                             <div v-for="city in charts.assetsByCity" :key="city.name" class="flex items-center justify-between">
                                <span class="text-sm font-medium">{{ city.name }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-24 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full bg-primary" :style="{ width: Math.min((city.value / stats.totalAssets) * 100, 100) + '%' }"></div>
                                    </div>
                                    <span class="text-xs font-mono w-8 text-right">{{ city.value }}</span>
                                </div>
                             </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
