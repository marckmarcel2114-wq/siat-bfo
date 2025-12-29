<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ShoppingCart, Plus, Eye, FileText, CheckCircle, XCircle } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';

const props = defineProps<{
    procurements: {
        data: Array<{
            id: number;
            code: string; // Assuming we might have a generated code or just use ID
            requester: { name: string; email: string };
            city: { name: string };
            status: string;
            created_at: string;
            items: Array<{ name: string; quantity: number }>;
        }>;
        links: Array<any>;
    };
}>();

const getStatusBadge = (status: string) => {
    switch(status) {
        case 'pending': return { label: 'Pendiente', class: 'bg-yellow-100 text-yellow-800 border-yellow-200' };
        case 'authorized': return { label: 'Autorizado', class: 'bg-green-100 text-green-800 border-green-200' };
        case 'rejected': return { label: 'Rechazado', class: 'bg-red-100 text-red-800 border-red-200' };
        case 'purchased': return { label: 'Comprado', class: 'bg-blue-100 text-blue-800 border-blue-200' };
        default: return { label: status, class: 'bg-gray-100 text-gray-800' };
    }
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Adquisiciones', href: route('procurements.index') },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Solicitudes de Adquisición" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Adquisiciones</h1>
                    <p class="text-muted-foreground">Gestiona las solicitudes de compra de equipos y suministros.</p>
                </div>
                <Button as-child class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-sm">
                    <Link :href="route('procurements.create')">
                        <Plus class="mr-2 h-4 w-4" />
                        Nueva Solicitud
                    </Link>
                </Button>
            </div>

            <Card class="border-border/50 shadow-sm">
                <CardHeader class="pb-3 border-b border-border/50 bg-muted/20">
                    <CardTitle>Historial de Solicitudes</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-muted/50 text-muted-foreground font-medium border-b border-border/50">
                                <tr>
                                    <th class="h-12 px-4">Solicitante</th>
                                    <th class="h-12 px-4">Ciudad</th>
                                    <th class="h-12 px-4">Items</th>
                                    <th class="h-12 px-4">Fecha</th>
                                    <th class="h-12 px-4">Estado</th>
                                    <th class="h-12 px-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="procurements.data.length === 0">
                                    <td colspan="6" class="p-8 text-center text-muted-foreground">
                                        No hay solicitudes registradas.
                                    </td>
                                </tr>
                                <tr v-for="req in procurements.data" :key="req.id" class="border-b border-border/50 last:border-0 hover:bg-muted/30 transition-colors">
                                    <td class="p-4">
                                        <div class="font-medium">{{ req.requester.name }}</div>
                                        <div class="text-xs text-muted-foreground">{{ req.requester.email }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-1">
                                            <span>{{ req.city?.name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-xs max-w-xs truncate">
                                            <span v-for="(item, i) in req.items" :key="i" class="mr-1">
                                                {{ item.quantity }}x {{ item.name }}<span v-if="i < req.items.length - 1">,</span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-muted-foreground text-xs">
                                        {{ new Date(req.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="p-4">
                                        <Badge :class="getStatusBadge(req.status).class" variant="outline" class="font-normal capitalize border">
                                            {{ getStatusBadge(req.status).label }}
                                        </Badge>
                                    </td>
                                    <td class="p-4 text-right">
                                        <Button variant="ghost" size="sm" as-child>
                                            <Link :href="route('procurements.show', req.id)">
                                                <Eye class="h-4 w-4 mr-1" /> Ver Detalle
                                            </Link>
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                     <!-- Pagination -->
                    <div class="p-4 flex items-center justify-between border-t border-border/50" v-if="procurements.links.length > 3">
                         <div class="flex gap-1">
                             <template v-for="(link, k) in procurements.links" :key="k">
                                <Link 
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1 text-sm rounded-md transition-colors"
                                    :class="link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'"
                                    v-html="link.label"
                                />
                                <span v-else class="px-3 py-1 text-sm text-muted-foreground opacity-50" v-html="link.label"></span>
                             </template>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
