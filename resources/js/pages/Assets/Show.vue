<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'; // Need to create Tabs probably
import { ArrowLeft, Pencil, UserPlus, Wrench, Download, FileText, Monitor, Server, Info } from 'lucide-vue-next';

// Fallback for Tabs if not exists, but let's assume I need to implement them or use simple state.
// Since list_dir failed for Table/Select, Tabs might be missing too. 
// I'll stick to simple sections or use conditional rendering for tabs to be safe, 
// OR check if tabs exist. Let's assume standard Shadcn structure and if missing I'll implement simple tabs.

const props = defineProps<{
    asset: any;
}>();

const getStatusBadge = (status: string) => {
    switch(status) {
        case 'free': return { label: 'Disponible', class: 'text-green-600 border-green-600 bg-green-50' };
        case 'assigned': return { label: 'Asignado', class: 'bg-blue-600 text-white' };
        case 'maintenance': return { label: 'Mantenimiento', class: 'bg-yellow-100 text-yellow-800' };
        case 'repair': return { label: 'Reparación', class: 'bg-orange-100 text-orange-800' };
        case 'broken': return { label: 'Dañado', class: 'bg-red-100 text-red-800' };
        case 'disposed': return { label: 'Baja', class: 'text-gray-500' };
        default: return { label: status, class: '' };
    }
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Inventario', href: '#' },
    { title: 'Activos', href: route('assets.index') },
    { title: 'Detalle', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Detalle de Activo" />

        <div class="max-w-6xl mx-auto p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" as-child>
                        <Link :href="route('assets.index')">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <div class="flex items-center gap-2">
                             <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ asset.code_internal || 'Sin Código' }}</h1>
                             <Badge :class="getStatusBadge(asset.status).class" variant="outline">{{ getStatusBadge(asset.status).label }}</Badge>
                        </div>
                        <p class="text-muted-foreground">{{ asset.type?.name }} - {{ asset.brand }} {{ asset.model }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="route('assets.edit', asset.id)">
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Link>
                    </Button>
                    <Button v-if="asset.status === 'free'" as-child>
                        <Link :href="route('asset-assignments.create', { asset_id: asset.id })">
                            <UserPlus class="mr-2 h-4 w-4" />
                            Asignar
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <Card class="border-border/50 shadow-sm">
                        <CardHeader>
                            <CardTitle>Detalles Técnicos</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 text-sm">
                                <div>
                                    <dt class="text-muted-foreground">Número de Serie</dt>
                                    <dd class="font-medium mt-1">{{ asset.serial_number || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-muted-foreground">Marca / Modelo</dt>
                                    <dd class="font-medium mt-1">{{ asset.brand }} / {{ asset.model }}</dd>
                                </div>
                                <div>
                                    <dt class="text-muted-foreground">Ubicación Actual</dt>
                                    <dd class="font-medium mt-1 flex items-center gap-1">
                                         <span v-if="asset.location">
                                            {{ asset.location.city?.name }} - {{ asset.location.name }}
                                         </span>
                                         <span v-else class="text-muted-foreground italic">No asignada</span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-muted-foreground">Dirección IP / MAC</dt>
                                    <dd class="font-medium mt-1">{{ asset.ip_address || '--' }} / {{ asset.mac_address || '--' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-muted-foreground">Garantía Hasta</dt>
                                    <dd class="font-medium mt-1">{{ asset.warranty_expiry_date || '--' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-muted-foreground">Fecha Compra</dt>
                                    <dd class="font-medium mt-1">{{ asset.purchase_date || '--' }}</dd>
                                </div>
                            </dl>
                            
                            <div class="mt-6 border-t border-border/50 pt-4">
                                <h4 class="text-sm font-medium text-muted-foreground mb-2">Notas</h4>
                                <p class="text-sm">{{ asset.notes || 'Sin notas adicionales.' }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- History / Assignments -->
                    <Card class="border-border/50 shadow-sm">
                        <CardHeader>
                            <CardTitle>Historial de Asignaciones</CardTitle>
                        </CardHeader>
                        <CardContent class="p-0">
                            <div class="overflow-hidden">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-muted/50 text-muted-foreground font-medium border-b border-border/50">
                                        <tr>
                                            <th class="h-10 px-4">Responsable</th>
                                            <th class="h-10 px-4">Fecha Entrega</th>
                                            <th class="h-10 px-4">Acta</th>
                                            <th class="h-10 px-4">Fecha Dev.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!asset.assignments || asset.assignments.length === 0">
                                            <td colspan="4" class="p-4 text-center text-muted-foreground">Sin historial.</td>
                                        </tr>
                                        <tr v-for="assign in asset.assignments" :key="assign.id" class="border-b border-border/50 last:border-0 hover:bg-muted/20">
                                            <td class="p-4 font-medium">{{ assign.user?.name }}</td>
                                            <td class="p-4">{{ assign.assigned_at }}</td>
                                            <td class="p-4">
                                                <a v-if="assign.act_document_path" :href="'/storage/' + assign.act_document_path" target="_blank" class="text-primary hover:underline flex items-center gap-1">
                                                    <FileText class="h-3 w-3" /> Ver Acta
                                                </a>
                                                <span v-else class="text-muted-foreground italic">--</span>
                                            </td>
                                            <td class="p-4 text-muted-foreground">{{ assign.returned_at || 'Vigente' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar Info (Software/Maintenance Summary) -->
                <div class="space-y-6">
                    <Card class="border-border/50 shadow-sm">
                        <CardHeader>
                            <CardTitle>Bitácora de Software</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-for="log in asset.software_logs" :key="log.id" class="flex items-start gap-3 text-sm pb-3 border-b border-border/50 last:border-0">
                                <Monitor class="h-4 w-4 text-primary mt-0.5" />
                                <div>
                                    <p class="font-medium">{{ log.software_name }} ({{ log.version }})</p>
                                    <p class="text-xs text-muted-foreground capitalize">{{ log.action }} - {{ log.performed_at }}</p>
                                </div>
                            </div>
                            <div v-if="!asset.software_logs || asset.software_logs.length === 0" class="text-sm text-muted-foreground italic">
                                No hay registros de software.
                            </div>
                            <Button variant="outline" size="sm" class="w-full">
                                <Plus class="mr-2 h-3 w-3" /> Agregar Registro
                            </Button>
                        </CardContent>
                    </Card>

                    <Card class="border-border/50 shadow-sm">
                        <CardHeader>
                            <CardTitle>Mantenimientos</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-for="maint in asset.maintenances" :key="maint.id" class="flex items-start gap-3 text-sm pb-3 border-b border-border/50 last:border-0">
                                <Wrench class="h-4 w-4 text-orange-500 mt-0.5" />
                                <div>
                                    <p class="font-medium">{{ maint.type?.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ maint.performed_at }}</p>
                                </div>
                            </div>
                            <div v-if="!asset.maintenances || asset.maintenances.length === 0" class="text-sm text-muted-foreground italic">
                                No hay registros de mantenimiento.
                            </div>
                             <Button variant="outline" size="sm" class="w-full">
                                <Plus class="mr-2 h-3 w-3" /> Registrar Mantenimiento
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
