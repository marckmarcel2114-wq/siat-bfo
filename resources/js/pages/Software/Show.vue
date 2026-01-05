<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import { 
    ArrowLeft, Disc, Calendar, Key, Users, Info, 
    Trash2, Monitor, ExternalLink, Plus, Search, CheckCircle2
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
    license: any;
    availableAssets: Array<any>;
}>();

const searchTerm = ref('');
const showInstallDialog = ref(false);

const filteredAssets = computed(() => {
    if (!searchTerm.value) return [];
    return props.availableAssets.filter(asset => 
        asset.codigo_activo.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        asset.numero_serie?.toLowerCase().includes(searchTerm.value.toLowerCase())
    ).slice(0, 5);
});

const form = useForm({
    license_id: props.license.id,
    activo_id: null as number | null,
    observaciones: ''
});

const selectAsset = (asset: any) => {
    form.activo_id = asset.id;
    searchTerm.value = asset.codigo_activo + ' - ' + (asset.numero_serie || 'S/N');
};

const submitInstall = () => {
    form.post(route('software.install'), {
        onSuccess: () => {
            form.reset();
            searchTerm.value = '';
            showInstallDialog.value = false;
        }
    });
};

const uninstall = (installId: number) => {
    if (confirm('¿Desinstalar software y liberar la licencia?')) {
        const delForm = useForm({});
        delForm.delete(route('software.uninstall', installId));
    }
};

const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString();
};

const getUsagePercentage = () => {
    if (props.license.seats_total === 0) return 0;
    return Math.min((props.license.seats_used / props.license.seats_total) * 100, 100);
};
</script>

<template>
    <AppLayout :breadcrumbs="[
        { title: 'Software', href: route('software.index') },
        { title: license.nombre, href: '#' }
    ]">
        <Head :title="`Detalle: ${license.nombre}`" />

        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" as-child class="rounded-full h-10 w-10">
                        <Link :href="route('software.index')">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ license.nombre }}</h1>
                            <Badge variant="secondary" class="bg-blue-50 text-blue-700 border-blue-100">
                                {{ license.tipo }}
                            </Badge>
                        </div>
                        <p class="text-muted-foreground flex items-center gap-1.5 mt-0.5">
                            <Info class="h-3.5 w-3.5" /> ID: #{{ license.id }} • {{ license.proveedor?.nombre || 'Sin proveedor' }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <Button variant="outline" as-child class="flex-1 sm:flex-none h-10 px-6">
                        <Link :href="route('software.edit', license.id)">Editar</Link>
                    </Button>
                    <Button class="bg-blue-600 hover:bg-blue-700 text-white flex-1 sm:flex-none h-10 px-6 shadow-md shadow-blue-500/20" @click="showInstallDialog = !showInstallDialog">
                        <Plus class="mr-2 h-4 w-4" /> Instalar en Equipo
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Info Cards -->
                <div class="lg:col-span-1 space-y-6">
                    <Card class="border-t-4 border-t-blue-500 shadow-sm overflow-hidden">
                        <CardHeader class="pb-2">
                            <CardTitle class="text-sm font-bold uppercase text-muted-foreground">Estado de Licencia</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-muted-foreground">Uso de Asientos</span>
                                    <span class="font-bold">{{ license.seats_used }} / {{ license.seats_total }}</span>
                                </div>
                                <div class="w-full bg-muted rounded-full h-2.5">
                                    <div 
                                        class="h-2.5 rounded-full transition-all duration-500" 
                                        :class="getUsagePercentage() >= 100 ? 'bg-red-500' : 'bg-blue-500'"
                                        :style="`width: ${getUsagePercentage()}%`"
                                    ></div>
                                </div>
                                <p class="text-[10px] text-muted-foreground text-right" v-if="license.seats_total > 0">
                                    {{ license.seats_total - license.seats_used }} asientos disponibles de {{ license.seats_total }} totales.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 pt-2">
                                <div class="flex items-center gap-3 p-3 bg-muted/30 rounded-lg border border-muted/50">
                                    <div class="bg-white p-2 rounded shadow-sm">
                                        <Key class="h-4 w-4 text-orange-500" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] uppercase font-bold text-muted-foreground">Clave de Producto</p>
                                        <p class="text-sm font-mono truncate">{{ license.key || 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-3 bg-muted/30 rounded-lg border border-muted/50">
                                    <div class="bg-white p-2 rounded shadow-sm">
                                        <Calendar class="h-4 w-4 text-blue-500" />
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-muted-foreground">Expiración</p>
                                        <p class="text-sm font-medium">{{ formatDate(license.fecha_expiracion) }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card v-if="license.observaciones" class="shadow-sm">
                        <CardHeader class="pb-2">
                            <CardTitle class="text-sm font-bold uppercase text-muted-foreground">Observaciones</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ license.observaciones }}</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right: Installations List & Assignment Dialog -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Assignment Quick Action (Dynamic) -->
                    <Card v-if="showInstallDialog" class="border-2 border-blue-500/50 bg-blue-50/10 shadow-lg animate-in fade-in slide-in-from-top-4">
                        <CardHeader class="pb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <CardTitle class="text-lg">Asignar Software</CardTitle>
                                    <CardDescription>Busque un equipo por código de activo o número de serie.</CardDescription>
                                </div>
                                <Button variant="ghost" size="sm" @click="showInstallDialog = false">Cerrar</Button>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="relative">
                                <Search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input 
                                    v-model="searchTerm" 
                                    placeholder="Equipos (Computadoras, Laptops...)" 
                                    class="pl-9 h-10 border-blue-200 focus:ring-blue-500/20"
                                />
                                <div v-if="filteredAssets.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-blue-100 rounded-md shadow-xl overflow-hidden">
                                    <div 
                                        v-for="asset in filteredAssets" 
                                        :key="asset.id"
                                        @click="selectAsset(asset)"
                                        class="p-3 hover:bg-blue-50 cursor-pointer flex justify-between items-center transition-colors border-b last:border-0"
                                    >
                                        <div>
                                            <p class="text-sm font-bold">{{ asset.codigo_activo }}</p>
                                            <p class="text-xs text-muted-foreground">S/N: {{ asset.numero_serie || 'N/A' }}</p>
                                        </div>
                                        <CheckCircle2 v-if="form.activo_id === asset.id" class="h-4 w-4 text-blue-600" />
                                        <Plus v-else class="h-4 w-4 text-muted-foreground opacity-30" />
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label class="text-xs font-bold uppercase text-muted-foreground">Nota de Instalación</Label>
                                <Textarea v-model="form.observaciones" placeholder="Ej: Instalación remota via VPN..." class="h-20 resize-none" />
                            </div>

                            <div class="flex justify-end pt-2">
                                <Button 
                                    class="bg-blue-600 hover:bg-blue-700 text-white w-full sm:w-auto" 
                                    :disabled="!form.activo_id || form.processing"
                                    @click="submitInstall"
                                >
                                    Confirmar Instalación
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Installations Table -->
                    <Card class="shadow-md overflow-hidden">
                        <CardHeader class="bg-muted/10 border-b">
                            <div class="flex items-center gap-2">
                                <Monitor class="h-5 w-5 text-muted-foreground" />
                                <CardTitle class="text-base">Equipos con esta aplicación</CardTitle>
                                <Badge variant="outline" class="ml-auto">{{ license.instalaciones?.length || 0 }} instalados</Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="p-0">
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-muted-foreground uppercase bg-muted/5 border-b">
                                        <tr>
                                            <th scope="col" class="px-6 py-4">Activo / Equipo</th>
                                            <th scope="col" class="px-6 py-4">Fecha Instalación</th>
                                            <th scope="col" class="px-6 py-4">Registrado por</th>
                                            <th scope="col" class="px-6 py-4 text-right">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        <tr v-for="inst in license.instalaciones" :key="inst.id" class="bg-white hover:bg-muted/5 transition-colors group">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                        <Monitor class="h-4 w-4" />
                                                    </div>
                                                    <div>
                                                        <Link :href="route('assets.show', inst.activo_id)" class="font-bold text-foreground hover:text-blue-600 flex items-center gap-1">
                                                            {{ inst.asset?.codigo_activo }} <ExternalLink class="h-3 w-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                                                        </Link>
                                                        <p class="text-xs text-muted-foreground">S/N: {{ inst.asset?.numero_serie || 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">{{ formatDate(inst.fecha_instalacion) }}</td>
                                            <td class="px-6 py-4 text-xs font-medium">{{ inst.registrador?.name || 'Sistema' }}</td>
                                            <td class="px-6 py-4 text-right">
                                                <Button variant="ghost" size="icon" @click="uninstall(inst.id)" class="text-muted-foreground hover:text-red-600 hover:bg-red-50 rounded-full h-8 w-8 transition-colors">
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </td>
                                        </tr>
                                        <tr v-if="!license.instalaciones || license.instalaciones.length === 0">
                                            <td colspan="4" class="px-6 py-12 text-center text-muted-foreground italic">
                                                No hay equipos registrados con esta licencia todavía.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
