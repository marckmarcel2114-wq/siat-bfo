<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

// UI Components
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Switch } from '@/components/ui/switch';

// Icons
import { 
    ArrowLeft, Edit, UserPlus, CheckCircle, Download, Wrench,
    Monitor, Server, Network, MapPin, DollarSign, History, AlertOctagon,
    Box, FileText, Wifi, UserCheck, Disc, Trash2, Plus, Settings, Package, Layers, X, RefreshCw
} from 'lucide-vue-next';

const props = defineProps<{
    asset: any;
    availableLicenses: Array<any>;
}>();

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Inventario', href: '#' },
    { title: 'Activos', href: route('assets.index') },
    { title: props.asset.codigo_activo, href: '#' },
];

const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString();
};

const formatDateTime = (dateString: string) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
};

const formatCurrency = (amount: string) => {
    if (!amount) return 'N/A';
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(Number(amount));
};

// Local Mutable Asset State
const localAsset = ref(props.asset);
watch(() => props.asset, (newVal) => {
    localAsset.value = newVal;
}, { deep: true });

const refreshAssetData = async () => {
    try {
        // Direct fetch avoiding Inertia AND Cache
        const response = await axios.get(route('assets.show', localAsset.value.id) + '?t=' + new Date().getTime());
        if (response.data && response.data.asset) {
            localAsset.value = response.data.asset;
        }
    } catch (e) {
        console.error("Failed to refresh asset", e);
    }
};

const isAssigned = computed(() => localAsset.value.estado_activo?.nombre === 'Asignado');
const isInMaintenance = computed(() => {
    const status = localAsset.value.estado_activo?.nombre?.toLowerCase() || '';
    return status.includes('mantenimiento') || status.includes('reparación');
});

// Latest maintenance for finishing
const currentMaintenanceId = computed(() => {
    return localAsset.value.mantenimientos?.length > 0 ? localAsset.value.mantenimientos[0].id : null;
});

// --- Software Catalog Data ---
const softwareCatalog = ref<any[]>([]);
const selectedSoftwareId = ref<string>('');
const availableVersions = computed(() => {
    if (!selectedSoftwareId.value) return [];
    const soft = softwareCatalog.value.find(s => s.id == selectedSoftwareId.value);
    return soft ? soft.versions : [];
});

onMounted(async () => {
    try {
        const response = await axios.get(route('api.software-catalog'));
        softwareCatalog.value = response.data;
    } catch (e) {
        console.error("Failed to load software catalog", e);
    }
});

// --- Software Installation logic ---
// --- Software Installation logic ---
// --- Unified Installation Logic ---
const isInstallModalOpen = ref(false);
const installMode = ref<'existing' | 'new'>('existing');

// Unified Form
const softwareForm = useForm({
    // Existing Mode
    license_id: '',
    software_version_id: '',
    
    // New Mode Fields
    new_software_name: '',
    new_software_version: '',
    
    activo_id: props.asset.id,
    observaciones: ''
});

const openInstallModal = () => {
    softwareForm.reset();
    selectedSoftwareId.value = '';
    installMode.value = 'existing';
    isInstallModalOpen.value = true;
};

const closeInstallModal = () => {
    isInstallModalOpen.value = false;
    softwareForm.reset();
    selectedSoftwareId.value = '';
};

const submitInstall = async () => {
    if (installMode.value === 'new') {
        // Validation for new
        if (!softwareForm.new_software_name || !softwareForm.new_software_version) {
             alert('Debe ingresar el nombre y versión del software.');
             return;
        }
        
        try {
             // 1. Create Catalog Item
             const catRes = await axios.post(route('software-catalog.store'), {
                 nombre: softwareForm.new_software_name,
                 tipo: 'Software',
                 fabricante: 'Desconocido'
             });
             const newSoft = catRes.data;
             
             // 2. Create Version
             const verRes = await axios.post(route('software-catalog.versions.store', newSoft.id), {
                 version: softwareForm.new_software_version
             });
             const newVer = verRes.data;
             
             // 3. Set IDs for Installation
             softwareForm.software_version_id = newVer.id;
             
             // Refresh catalog silently in background for next time
             axios.get(route('api.software-catalog')).then(r => softwareCatalog.value = r.data);
             
        } catch (e: any) {
             console.error("Creation failed", e);
             alert('Error al crear el software: ' + (e.response?.data?.message || e.message));
             return; // Stop
        }
    }

    // Proceed with Installation (Standard)
    softwareForm.post(route('software.install'), {
        onSuccess: () => {
            closeInstallModal();
            alert('¡Software instalado correctamente!');
            refreshAssetData();
        },
        onError: (errors) => {
            console.error("Install failed", errors);
            alert('Error al instalar software. Revise los campos.');
        }
    });
};

const uninstallSoftware = (installId: number) => {
    if (confirm('¿Desinstalar software del equipo?')) {
        const form = useForm({});
        form.delete(route('software.uninstall', installId));
    }
};


// --- Software Log logic (Upgrades/History) ---
const showUpdateLogDialog = ref(false);
const logForm = useForm({
    software_name: 'Sistema Operativo',
    version: '',
    action: 'update',
    notes: ''
});

// Redundant close function removed (moved up)

const submitLog = () => {
    logForm.post(route('assets.software.log', props.asset.id), {
        onSuccess: () => {
            showUpdateLogDialog.value = false;
            logForm.reset('version', 'notes');
        }
    });
};

const getActionBadge = (action: string) => {
    switch (action) {
        case 'install': return { label: 'Instalación', class: 'bg-green-100 text-green-700' };
        case 'update': return { label: 'Actualización', class: 'bg-blue-100 text-blue-700' };
        case 'remove': return { label: 'Remoción', class: 'bg-red-100 text-red-700' };
        default: return { label: action, class: 'bg-gray-100 text-gray-700' };
    }
};

// --- Upgrade Software Logic ---
const showUpgradeInlineId = ref<number | null>(null);
const upgradeForm = useForm({
    id: 0,
    software_version_id: '',
    fecha_actualizacion: new Date().toISOString().split('T')[0],
    observaciones: ''
});
const currentUpgradeInstallation = ref<any>(null);
const upgradeSoftwareId = ref<string>('');

const upgradeVersions = computed(() => {
    // If we have a selected software ID (either from existing version or manual selection)
    if (upgradeSoftwareId.value) {
        const soft = softwareCatalog.value.find(s => s.id.toString() === upgradeSoftwareId.value.toString());
        return soft ? soft.versions : [];
    }
    return [];
});

const openUpgrade = (installation: any) => {
    // Toggle off if same
    if (showUpgradeInlineId.value === installation.id) {
        showUpgradeInlineId.value = null;
        return;
    }

    currentUpgradeInstallation.value = installation;
    upgradeForm.id = installation.id;
    upgradeForm.software_version_id = ''; 
    upgradeForm.observaciones = '';
    
    // If it already has a version, lock it to that software
    if (installation.software_version) {
        upgradeSoftwareId.value = installation.software_version.software_id.toString();
    } else {
        // Legacy: Let user choose software
        upgradeSoftwareId.value = '';
    }
    
    showUpgradeInlineId.value = installation.id;
};

const submitUpgrade = () => {
    upgradeForm.put(route('software.upgrade', upgradeForm.id), {
        onSuccess: () => {
            // Close form immediately
            showUpgradeInlineId.value = null;
            upgradeForm.reset();
            currentUpgradeInstallation.value = null;
            
            alert('¡Actualización registrada con éxito!');
            
            // Refresh data
            refreshAssetData();
            axios.get(route('api.software-catalog')).then(r => softwareCatalog.value = r.data);
        },
        onError: (errors: any) => {
            console.error("Upgrade failed", errors);
            const msg = Object.values(errors).join('\n');
            alert('Error al actualizar: ' + (msg || 'Verifique los datos e intente nuevamente.'));
        }
    });
};

// --- New Catalog Item Modal ---
// Old separate modals logic removed for cleanup
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Detalle: ${asset.codigo_activo}`" />

        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col gap-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3 sm:gap-4">
                        <Button variant="outline" size="icon" as-child class="shrink-0">
                            <Link :href="route('assets.index')">
                                <ArrowLeft class="h-4 w-4" />
                            </Link>
                        </Button>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground truncate">{{ asset.codigo_activo }}</h1>
                                <div class="flex gap-1.5 shrink-0">
                                    <Badge variant="outline" class="whitespace-nowrap">{{ asset.tipo_activo?.nombre }}</Badge>
                                    <Badge :variant="isAssigned ? 'default' : (isInMaintenance ? 'destructive' : 'secondary')" class="whitespace-nowrap">
                                        {{ asset.estado_activo?.nombre }}
                                    </Badge>
                                </div>
                            </div>
                            <p class="text-sm sm:text-base text-muted-foreground truncate">
                                {{ asset.modelo?.marca?.nombre }} {{ asset.modelo?.nombre }}
                                <span class="hidden sm:inline"> - S/N: {{ asset.numero_serie }}</span>
                                <span class="sm:hidden block">S/N: {{ asset.numero_serie }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                        <Button v-if="isAssigned" as-child class="bg-orange-600 hover:bg-orange-700 flex-1 sm:flex-none text-white">
                            <Link :href="route('assignments.return', asset.id)">
                                <CheckCircle class="mr-2 h-4 w-4" /> <span class="whitespace-nowrap">Devolver</span>
                            </Link>
                        </Button>
                        
                        <!-- Maintenance Button: Smart toggle -->
                        <Button v-if="isInMaintenance && currentMaintenanceId" as-child class="bg-green-600 hover:bg-green-700 flex-1 sm:flex-none text-white shadow-lg shadow-green-500/20 transition-all active:scale-95">
                            <Link :href="route('maintenances.finish', currentMaintenanceId)">
                                <CheckCircle class="mr-2 h-4 w-4" /> <span class="whitespace-nowrap">Finalizar</span>
                            </Link>
                        </Button>
                        <Button v-else-if="!isInMaintenance" as-child class="bg-red-600 hover:bg-red-700 flex-1 sm:flex-none text-white shadow-lg shadow-red-500/20 transition-all active:scale-95">
                            <Link :href="route('maintenances.create', { asset_id: asset.id })">
                                <Wrench class="mr-2 h-4 w-4" /> <span class="whitespace-nowrap">Mant.</span>
                            </Link>
                        </Button>
                        
                        <Button variant="outline" as-child class="flex-1 sm:flex-none">
                            <Link :href="route('assets.edit', asset.id)">
                                <Edit class="mr-2 h-4 w-4" /> <span class="whitespace-nowrap">Editar</span>
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>

            <Tabs default-value="general" class="w-full space-y-6">
                <TabsList class="bg-muted/20 p-1 h-auto flex flex-wrap sm:flex-nowrap">
                    <TabsTrigger value="general" class="flex-1 sm:flex-none gap-2 px-4"><Box class="h-4 w-4" /> General</TabsTrigger>
                    <TabsTrigger value="technical" class="flex-1 sm:flex-none gap-2 px-4"><Monitor class="h-4 w-4" /> Técnico</TabsTrigger>
                    <TabsTrigger value="network" class="flex-1 sm:flex-none gap-2 px-4"><Network class="h-4 w-4" /> Red</TabsTrigger>
                    <TabsTrigger value="software" class="flex-1 sm:flex-none gap-2 px-4"><Disc class="h-4 w-4" /> Software</TabsTrigger>
                    <TabsTrigger value="location" class="flex-1 sm:flex-none gap-2 px-4"><MapPin class="h-4 w-4" /> Ubicación</TabsTrigger>
                    <TabsTrigger value="history" class="flex-1 sm:flex-none gap-2 px-4"><History class="h-4 w-4" /> Historial</TabsTrigger>
                    <TabsTrigger value="maintenance" class="flex-1 sm:flex-none gap-2 px-4 text-red-600"><Wrench class="h-4 w-4" /> Mant.</TabsTrigger>
                </TabsList>

                <!-- 1. GENERAL -->
                <TabsContent value="general">
                    <Card class="border-t-4 border-t-amber-500 shadow-md">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-amber-600">
                                <FileText class="h-5 w-5" /> Información General
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div><h4 class="text-sm font-medium text-muted-foreground">Marca</h4><p class="text-lg font-bold">{{ asset.modelo?.marca?.nombre || 'N/A' }}</p></div>
                            <div><h4 class="text-sm font-medium text-muted-foreground">Modelo</h4><p class="text-lg font-bold">{{ asset.modelo?.nombre || 'N/A' }}</p></div>
                            <div><h4 class="text-sm font-medium text-muted-foreground">S/N</h4><p class="text-lg font-mono font-bold">{{ asset.numero_serie || 'N/A' }}</p></div>
                            <div><h4 class="text-sm font-medium text-muted-foreground">Criticidad</h4><p class="text-lg font-bold">{{ asset.nivel_criticidad?.nombre || 'N/A' }}</p></div>
                            <div><h4 class="text-sm font-medium text-muted-foreground">Propietario</h4><p class="text-lg font-bold">{{ asset.propietario?.nombre || 'N/A' }}</p></div>
                            <div><h4 class="text-sm font-medium text-muted-foreground">Adquisición</h4><p class="text-lg font-bold">{{ formatDate(asset.fecha_adquisicion) }} ({{ formatCurrency(asset.valor_adquisicion) }})</p></div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- 2. TECHNICAL -->
                <TabsContent value="technical">
                    <Card class="border-t-4 border-t-purple-500 shadow-md">
                        <CardHeader class="flex flex-row items-center justify-between pb-4">
                            <CardTitle class="flex items-center gap-2 text-purple-600"><Monitor class="h-5 w-5" /> Hardware EAV</CardTitle>
                            <Button size="sm" variant="secondary" as-child><Link :href="route('assets.specs.edit', asset.id)"><Wrench class="mr-2 h-4 w-4" /> Gestionar Hardware</Link></Button>
                        </CardHeader>
                        <CardContent class="space-y-6">
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex justify-between p-3 border rounded-lg bg-slate-50">
                                    <span class="font-medium text-muted-foreground">MAC Ethernet</span>
                                    <span class="font-bold font-mono">{{ asset.mac_ethernet || 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between p-3 border rounded-lg bg-slate-50">
                                    <span class="font-medium text-muted-foreground">MAC Wi-Fi</span>
                                    <span class="font-bold font-mono">{{ asset.mac_wifi || 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="attr in asset.atributos.filter(a => a.definicion?.category !== 'software')" :key="attr.id" class="flex justify-between p-3 border rounded-lg hover:border-purple-200 transition-colors">
                                    <span class="font-medium text-muted-foreground">{{ attr.definicion?.nombre }}</span>
                                    <span class="font-bold">{{ attr.valor }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- 3. NETWORK -->
                <TabsContent value="network">
                    <Card class="border-t-4 border-t-cyan-500 shadow-md">
                        <CardHeader class="flex flex-row items-center justify-between border-b pb-4">
                            <CardTitle class="flex items-center gap-2 text-cyan-600"><Network class="h-5 w-5" /> Configuración IP</CardTitle>
                            <Button size="sm" variant="secondary" as-child><Link :href="route('assets.network.edit', asset.id)"><Wrench class="mr-2 h-4 w-4" /> Gestionar Red</Link></Button>
                        </CardHeader>
                        <CardContent class="p-6">
                             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div><h4 class="text-[10px] uppercase font-bold text-muted-foreground">IPv4</h4><p class="text-lg font-bold font-mono text-slate-800">{{ asset.network_assignment?.ip_address || 'Estática / No asig.' }}</p></div>
                                <div><h4 class="text-[10px] uppercase font-bold text-muted-foreground">Switch</h4><p class="text-lg font-bold text-slate-800">{{ asset.network_assignment?.punto_red?.switch || 'N/A' }}</p></div>
                                <div><h4 class="text-[10px] uppercase font-bold text-muted-foreground">Punto / Roseta</h4><p class="text-lg font-bold text-slate-800">{{ asset.network_assignment?.punto_red?.roseta || 'N/A' }}</p></div>
                                <div><h4 class="text-[10px] uppercase font-bold text-muted-foreground">Patch Panel</h4><p class="text-lg font-bold text-slate-800">{{ asset.network_assignment?.punto_red?.patch_panel || 'N/A' }}</p></div>
                             </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- 4. SOFTWARE -->
                <TabsContent value="software" class="space-y-6">
                    <!-- Base Software Versions (Legacy) -->
                    <Card v-if="asset.atributos.some(a => a.definicion?.category === 'software')" class="border-none shadow-md overflow-hidden bg-white">
                        <CardHeader class="bg-slate-50/50 border-b pb-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-slate-200 flex items-center justify-center text-slate-500 shadow-sm">
                                        <Disc class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <CardTitle class="text-lg font-bold text-slate-600">Versiones Base (Legado)</CardTitle>
                                        <CardDescription>Atributos legados de software.</CardDescription>
                                    </div>
                                </div>
                                <div>
                                     <Button size="sm" variant="secondary" as-child><Link :href="route('assets.software.edit', asset.id)"><Settings class="mr-2 h-4 w-4" /> Editar</Link></Button>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="attr in localAsset.atributos.filter(a => a.definicion?.category === 'software')" :key="attr.id" class="flex justify-between p-3 border rounded-lg hover:border-blue-200 transition-colors bg-slate-50">
                                    <span class="font-medium text-muted-foreground">{{ attr.definicion?.nombre }}</span>
                                    <span class="font-bold text-slate-800">{{ attr.valor }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Licensed Software / Software Inventory -->
                    <Card class="border-none shadow-md overflow-hidden bg-white">
                        <CardHeader class="bg-slate-50/50 border-b pb-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/10">
                                        <Package class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <CardTitle class="text-lg font-bold">Inventario de Software</CardTitle>
                                        <CardDescription>Aplicaciones instaladas desde el catálogo y licencias activas.</CardDescription>
                                    </div>
                                </div>
                                <Button size="sm" @click="openInstallModal" class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-500/20 shadow-lg transition-all active:scale-95">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Instalar Software
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent class="p-0">
                            <!-- Software Table -->
                            <Table>
                                <TableHeader class="bg-slate-50/30">
                                    <TableRow>
                                        <TableHead class="text-[10px] uppercase font-bold">Software / Versión</TableHead>
                                        <TableHead class="text-[10px] uppercase font-bold">Licencia</TableHead>
                                        <TableHead class="text-[10px] uppercase font-bold">Fecha / Registrador</TableHead>
                                        <TableHead class="text-right text-[10px] uppercase font-bold pr-6">Acciones</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <template v-for="inst in localAsset.software_installations" :key="inst.id">
                                        <TableRow class="group hover:bg-slate-50/50 transition-colors" :class="showUpgradeInlineId === inst.id ? 'bg-blue-50/30' : ''">
                                            <TableCell class="py-5">
                                                <!-- Logic to Display either Catalog Name or License Name -->
                                                <template v-if="inst.software_version">
                                                    <p class="font-bold text-slate-900 leading-tight">{{ inst.software_version?.software?.nombre || 'Software sin nombre' }}</p>
                                                    <p class="text-xs text-emerald-600 font-mono mt-1">{{ inst.software_version?.version }} (Catalogado)</p>
                                                </template>
                                                <template v-else>
                                                    <p class="font-bold text-slate-900 leading-tight">{{ inst.license?.nombre || 'Registro Legado / Manual' }}</p>
                                                    <p class="text-xs text-amber-600 font-mono mt-1">Sin Catalogación / Licencia</p>
                                                </template>
                                                <p class="text-[10px] text-slate-500 mt-1" v-if="inst.observaciones">{{ inst.observaciones }}</p>
                                            </TableCell>
                                            <TableCell>
                                                <Badge v-if="inst.license" variant="outline" class="font-bold text-[10px] bg-emerald-50 text-emerald-700 border-emerald-200">
                                                    {{ inst.license?.tipo }}
                                                </Badge>
                                                <span v-else class="text-xs text-slate-400 italic">Sin Licencia / Gratuito</span>
                                            </TableCell>
                                            <TableCell>
                                                <p class="text-sm font-medium">{{ formatDate(inst.fecha_instalacion) }}</p>
                                                <p class="text-[10px] text-slate-400 font-bold flex items-center gap-1 mt-0.5"><UserCheck class="h-3 w-3" /> {{ inst.registrador?.name }}</p>
                                            </TableCell>
                                            <TableCell class="text-right pr-6">
                                                <div class="flex items-center justify-end gap-1">
                                                    <Button variant="ghost" size="icon" @click="openUpgrade(inst)" 
                                                        class="rounded-full h-8 w-8 transition-all active:scale-95" 
                                                        :class="showUpgradeInlineId === inst.id ? 'bg-blue-600 text-white hover:bg-blue-700' : 'text-blue-400 hover:text-blue-600 hover:bg-blue-50'"
                                                        title="Actualizar Versión">
                                                        <RefreshCw class="h-4 w-4" :class="showUpgradeInlineId === inst.id ? 'animate-spin-slow' : ''" />
                                                    </Button>
                                                    <Button variant="ghost" size="icon" @click="uninstallSoftware(inst.id)" class="text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-full h-8 w-8 transition-colors" title="Desinstalar">
                                                        <Trash2 class="h-4 w-4" />
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>

                                        <!-- INLINE UPGRADE FORM -->
                                        <TableRow v-if="showUpgradeInlineId === inst.id" class="bg-blue-50/50 border-x border-blue-100 animate-in fade-in slide-in-from-top-1">
                                            <TableCell colspan="4" class="p-0">
                                                <div class="p-6 space-y-4">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center gap-3">
                                                            <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-md"><RefreshCw class="h-4 w-4" /></div>
                                                            <h4 class="font-bold text-slate-800">Registrar Actualización de Versión</h4>
                                                        </div>
                                                        <Badge variant="outline" class="bg-blue-50 text-blue-700 border-blue-200">
                                                            Versión Actual: {{ inst.software_version?.version || 'Legado' }}
                                                        </Badge>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                                                        <!-- Software Selection / Display -->
                                                        <div class="space-y-1.5 md:col-span-1">
                                                            <Label class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Software</Label>
                                                            <div v-if="!inst.software_version">
                                                                <Select v-model="upgradeSoftwareId">
                                                                    <SelectTrigger class="h-10 bg-white border-slate-200"><SelectValue placeholder="Vincular a..." /></SelectTrigger>
                                                                    <SelectContent class="z-[100] max-h-[200px]">
                                                                        <SelectItem v-for="soft in softwareCatalog" :key="soft.id" :value="soft.id.toString()">{{ soft.nombre }}</SelectItem>
                                                                    </SelectContent>
                                                                </Select>
                                                                <p class="text-[9px] text-amber-600 mt-1 font-medium">Requiere vinculación con catálogo</p>
                                                            </div>
                                                            <div v-else class="h-10 flex items-center px-3 bg-slate-50 border border-slate-100 rounded-md text-sm font-bold text-slate-700">
                                                                {{ inst.software_version?.software?.nombre }}
                                                            </div>
                                                        </div>

                                                        <!-- New Version -->
                                                        <div class="space-y-1.5 md:col-span-1">
                                                            <Label class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Nueva Versión</Label>
                                                            <div class="flex gap-1.5">
                                                                <div class="flex-1">
                                                                    <Select v-model="upgradeForm.software_version_id" :disabled="!upgradeSoftwareId">
                                                                        <SelectTrigger class="h-10 bg-white border-slate-200"><SelectValue placeholder="Seleccione..." /></SelectTrigger>
                                                                        <SelectContent class="z-[100] max-h-[200px]">
                                                                            <SelectItem v-for="ver in upgradeVersions" :key="ver.id" :value="ver.id.toString()" :disabled="ver.id === inst.software_version_id">
                                                                                {{ ver.version }} {{ ver.id === inst.software_version_id ? '(Actual)' : '' }}
                                                                            </SelectItem>
                                                                        </SelectContent>
                                                                    </Select>
                                                                </div>
                                                                <Button variant="outline" size="icon" @click="isVersionModalOpen = true" :disabled="!upgradeSoftwareId" class="h-10 w-10 shrink-0 border-dashed border-slate-300 text-slate-500 hover:text-blue-600 hover:border-blue-200" title="Nueva Versión">
                                                                    <Plus class="h-4 w-4" />
                                                                </Button>
                                                            </div>
                                                            <p v-if="upgradeForm.errors.software_version_id" class="text-[10px] text-red-500 font-bold mt-1 text-right">{{ upgradeForm.errors.software_version_id }}</p>
                                                        </div>

                                                        <!-- Date -->
                                                        <div class="space-y-1.5">
                                                            <Label class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Fecha de Cambio</Label>
                                                            <Input type="date" v-model="upgradeForm.fecha_actualizacion" class="h-10 bg-white border-slate-200" />
                                                        </div>

                                                        <!-- Notes -->
                                                        <div class="md:col-span-2 space-y-1.5">
                                                            <Label class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Notas de la Actualización</Label>
                                                            <Input v-model="upgradeForm.observaciones" placeholder="Motivo, ticket, etc..." class="h-10 bg-white border-slate-200" />
                                                        </div>

                                                        <!-- Actions -->
                                                        <div class="flex gap-2">
                                                            <Button type="button" @click="submitUpgrade" :disabled="upgradeForm.processing || !upgradeForm.software_version_id" class="flex-1 bg-blue-600 hover:bg-blue-700 h-10 shadow-md">Actualizar</Button>
                                                            <Button type="button" variant="outline" @click="showUpgradeInlineId = null" class="h-10 text-slate-500 hover:bg-slate-50 px-4">Cerrar</Button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                    <TableRow v-if="localAsset.software_installations.length === 0">
                                        <TableCell colspan="4" class="py-8 text-center text-slate-400 italic text-sm">Sin software registrado.</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>

                </TabsContent>

                <!-- 5. LOCATION -->
                <TabsContent value="location">

                    <Card class="border-t-4 border-t-orange-500 shadow-md">
                        <CardHeader><CardTitle class="flex items-center gap-2 text-orange-600"><MapPin class="h-5 w-5" /> Ubicación & Custodia</CardTitle></CardHeader>
                        <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <div><h4 class="text-xs uppercase font-bold text-muted-foreground">Sede / Oficina</h4><p class="text-xl font-bold text-slate-800">{{ asset.ubicacion?.nombre }}</p></div>
                                <div><h4 class="text-xs uppercase font-bold text-muted-foreground">Ubicación Detalle</h4><p class="text-base text-slate-600">{{ asset.detalle_ubicacion || 'Edificio Principal' }}</p></div>
                            </div>
                            <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100 flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center text-white"><UserCheck class="h-6 w-6" /></div>
                                <div><h4 class="text-xs uppercase font-bold text-blue-600/60">Custodio Actual</h4><p class="text-xl font-black text-blue-900">{{ asset.assignments?.[0]?.usuario?.name || 'EN DEPÓSITO' }} {{ asset.assignments?.[0]?.usuario?.last_name || '' }}</p></div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- 6. HISTORY -->
                <TabsContent value="history">
                     <Card class="border-t-4 border-t-slate-500 shadow-md">
                        <CardHeader><CardTitle class="flex items-center gap-2 text-slate-600"><History class="h-5 w-5" /> Historial de Asignaciones</CardTitle></CardHeader>
                        <CardContent class="p-0">
                            <Table>
                                <TableHeader class="bg-slate-50/50">
                                    <TableRow>
                                        <TableHead class="font-bold">Usuario</TableHead>
                                        <TableHead class="font-bold">Asignación</TableHead>
                                        <TableHead class="font-bold">Devolución</TableHead>
                                        <TableHead class="text-right font-bold pr-6">Actas</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="assign in asset.assignments" :key="assign.id">
                                        <TableCell class="font-bold text-slate-800">{{ assign.usuario?.name }} {{ assign.usuario?.last_name }}</TableCell>
                                        <TableCell>{{ formatDate(assign.fecha_asignacion) }} <Badge v-if="assign.es_actual" class="ml-2 bg-blue-100 text-blue-700 hover:bg-blue-100 border-none">Actual</Badge></TableCell>
                                        <TableCell>{{ assign.fecha_devolucion ? formatDate(assign.fecha_devolucion) : '---' }}</TableCell>
                                        <TableCell class="text-right pr-6 space-x-2">
                                            <Button v-if="assign.acta_entrega_path" variant="ghost" size="sm" as-child class="text-blue-600 h-8 font-bold"><a :href="route('assignments.download', { id: assign.id, type: 'entrega' })">Entrega</a></Button>
                                            <Button v-if="assign.acta_devolucion_path" variant="ghost" size="sm" as-child class="text-orange-600 h-8 font-bold"><a :href="route('assignments.download', { id: assign.id, type: 'devolucion' })">Devolución</a></Button>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- 7. MAINTENANCE -->
                <TabsContent value="maintenance">
                    <Card class="border-t-4 border-t-red-500 shadow-md">
                        <CardHeader class="flex flex-row items-center justify-between">
                            <div class="flex items-center gap-2 text-red-600">
                                <AlertOctagon class="h-5 w-5" />
                                <CardTitle>Control de Reparaciones</CardTitle>
                            </div>
                            <div class="flex gap-2">
                                <Button v-if="isInMaintenance && currentMaintenanceId" as-child class="bg-green-600 hover:bg-green-700 text-white shadow-md active:scale-95 transition-all">
                                    <Link :href="route('maintenances.finish', currentMaintenanceId)">
                                        <CheckCircle class="mr-2 h-4 w-4" /> Finalizar Mantenimiento
                                    </Link>
                                </Button>
                                <Button v-if="!isInMaintenance" as-child class="bg-red-600 hover:bg-red-700 text-white shadow-md active:scale-95 transition-all">
                                    <Link :href="route('maintenances.create', { asset_id: asset.id })">
                                        <Plus class="mr-2 h-4 w-4" /> Iniciar Mantenimiento
                                    </Link>
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent class="p-0">
                            <Table>
                                <TableHeader class="bg-red-50/50"><TableRow><TableHead>Inicio</TableHead><TableHead>Proveedor</TableHead><TableHead>Problema / Trabajo</TableHead><TableHead class="text-right pr-6">Inversión (Bs)</TableHead></TableRow></TableHeader>
                                <TableBody>
                                    <TableRow v-for="maint in asset.mantenimientos" :key="maint.id">
                                        <TableCell class="font-medium">{{ formatDate(maint.fecha_inicio) }}</TableCell>
                                        <TableCell class="font-bold">{{ maint.proveedor?.nombre_empresa || 'Interno' }}</TableCell>
                                        <TableCell class="text-slate-600">{{ maint.hoja_trabajo }}</TableCell>
                                        <TableCell class="text-right pr-6 font-mono font-bold">{{ formatCurrency(maint.costo_total) }}</TableCell>
                                    </TableRow>
                                    <TableRow v-if="!asset.mantenimientos?.length">
                                        <TableCell colspan="4" class="py-12 text-center text-slate-400 italic">No se han registrado mantenimientos para este activo.</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </div>

        <!-- DIALOGS -->

        <!-- Manual Update Log Dialog -->
        <Dialog v-model:open="showUpdateLogDialog">
            <DialogContent class="sm:max-w-[450px] rounded-2xl">
                <DialogHeader>
                    <div class="h-12 w-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 mb-4"><History class="h-6 w-6" /></div>
                    <DialogTitle class="text-xl font-bold">Registrar Evento de Cambio</DialogTitle>
                    <DialogDescription>Use esto para registrar actualizaciones de Windows o cambios significativos de software.</DialogDescription>
                </DialogHeader>
                <div class="grid gap-6 py-4">
                    <div class="space-y-2">
                        <Label class="text-xs font-bold uppercase text-slate-500">Nombre del Software / Componente</Label>
                        <Input v-model="logForm.software_name" placeholder="Ej: Windows 11 Pro, Office 2024" class="h-11" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label class="text-xs font-bold uppercase text-slate-500">Nueva Versión</Label>
                            <Input v-model="logForm.version" placeholder="Ej: 24H2" class="h-11" />
                        </div>
                        <div class="space-y-2">
                            <Label class="text-xs font-bold uppercase text-slate-500">Efecto</Label>
                            <Select v-model="logForm.action">
                                <SelectTrigger class="h-11"><SelectValue /></SelectTrigger>
                                <SelectContent class="z-[100] max-h-[200px]">
                                    <SelectItem value="update">Actualización (Upgrade)</SelectItem>
                                    <SelectItem value="install">Instalación Limpia</SelectItem>
                                    <SelectItem value="remove">Desinstalación</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label class="text-xs font-bold uppercase text-slate-500">Notas Adicionales / Justificación</Label>
                        <Textarea v-model="logForm.notes" placeholder="Ej: Upgrade realizado via ISO para corregir bugs de red..." class="h-24 resize-none" />
                    </div>
                </div>
                <DialogFooter class="gap-2">
                    <Button variant="outline" @click="showUpdateLogDialog = false" class="h-11 px-6">Cancelar</Button>
                    <Button @click="submitLog" :disabled="logForm.processing" class="h-11 px-6 bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/20">Guardar en Historial</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- New Catalog Software Modal -->
        <Dialog v-model:open="isCatalogModalOpen">
            <DialogContent class="sm:max-w-[450px] rounded-2xl p-0 overflow-hidden">
                <DialogHeader class="p-6 bg-slate-50 border-b">
                    <DialogTitle class="text-xl font-bold flex items-center gap-2">
                        <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600"><Plus class="h-4 w-4" /></div>
                        Nuevo Software en Catálogo
                    </DialogTitle>
                    <DialogDescription>Añada un nuevo título al catálogo maestro de software.</DialogDescription>
                </DialogHeader>
                <div class="p-6 space-y-4">
                    <div class="space-y-1.5">
                        <Label class="text-[10px] uppercase font-bold text-slate-500">Nombre del Software <span class="text-red-500">*</span></Label>
                        <Input v-model="catalogForm.nombre" placeholder="Ej: Adobe Photoshop" class="h-11 shadow-sm" />
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-[10px] uppercase font-bold text-slate-500">Fabricante</Label>
                        <Input v-model="catalogForm.fabricante" placeholder="Ej: Adobe Systems Inc." class="h-11 shadow-sm" />
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-[10px] uppercase font-bold text-slate-500">Breve Descripción</Label>
                        <Textarea v-model="catalogForm.descripcion" placeholder="Opcional..." class="h-20 resize-none shadow-sm" />
                    </div>
                </div>
                <DialogFooter class="p-6 bg-slate-50 border-t gap-2">
                    <Button variant="ghost" @click="isCatalogModalOpen = false">Cancelar</Button>
                    <Button @click="submitCatalogItem" :disabled="!catalogForm.nombre || catalogForm.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8">Guardar Software</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>



        <!-- New Version Modal -->
        <Dialog v-model:open="isVersionModalOpen">
            <DialogContent class="sm:max-w-[400px] rounded-2xl p-0 overflow-hidden">
                <DialogHeader class="p-6 bg-slate-50 border-b">
                    <DialogTitle class="text-xl font-bold flex items-center gap-2">
                        <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600"><Plus class="h-4 w-4" /></div>
                        Nueva Versión
                    </DialogTitle>
                    <DialogDescription>Añada una nueva versión para el software seleccionado.</DialogDescription>
                </DialogHeader>
                <div class="p-6 space-y-4">
                    <div class="space-y-1.5">
                        <Label class="text-[10px] uppercase font-bold text-slate-500">Número de Versión <span class="text-red-500">*</span></Label>
                        <Input v-model="versionForm.version" placeholder="Ej: 2024, 2.5.1, Professional" class="h-11 shadow-sm" />
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-[10px] uppercase font-bold text-slate-500">Descripción Corta</Label>
                        <Input v-model="versionForm.descripcion" placeholder="Opcional..." class="h-11 shadow-sm" />
                    </div>
                </div>
                <DialogFooter class="p-6 bg-slate-50 border-t gap-2">
                    <Button variant="ghost" @click="isVersionModalOpen = false">Cancelar</Button>
                    <Button @click="submitVersion" :disabled="!versionForm.version || versionForm.processing" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 shadow-md">Guardar Versión</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
        <!-- NEW INSTALLATION MODAL -->
        <Dialog :open="isInstallModalOpen" @update:open="val => !val && closeInstallModal()">
            <DialogContent class="sm:max-w-[800px] max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600"><Plus class="h-5 w-5" /></div>
                        <div>
                            <DialogTitle class="text-xl font-bold">Nueva Instalación de Software</DialogTitle>
                            <DialogDescription>
                                Gestione el software instalado en este activo.
                            </DialogDescription>
                        </div>
                    </div>
                    <!-- TABS -->
                    <div class="flex p-1 bg-slate-100 rounded-lg mt-4 w-fit">
                        <button @click="installMode = 'existing'" class="px-4 py-1.5 text-sm font-bold rounded-md transition-all" :class="installMode === 'existing' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'">Seleccionar Existente</button>
                        <button @click="installMode = 'new'" class="px-4 py-1.5 text-sm font-bold rounded-md transition-all" :class="installMode === 'new' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'">Registrar Nuevo</button>
                    </div>
                </DialogHeader>

                <div class="grid gap-6 py-6" v-if="installMode === 'existing'">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <!-- Software -->
                        <div class="space-y-1.5 md:col-span-1">
                            <Label class="text-sm font-bold text-slate-700">Software (Catálogo)</Label>
                             <Select v-model="selectedSoftwareId" @update:modelValue="() => { softwareForm.software_version_id = ''; }">
                                <SelectTrigger class="h-11 bg-white border-slate-200 shadow-sm"><SelectValue placeholder="Buscar software..." /></SelectTrigger>
                                <SelectContent class="z-[100] max-h-[300px]">
                                    <SelectItem v-for="soft in softwareCatalog" :key="soft.id" :value="soft.id.toString()">{{ soft.nombre }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Version -->
                        <div class="space-y-1.5 md:col-span-1">
                            <Label class="text-sm font-bold text-slate-700">Versión</Label>
                            <Select v-model="softwareForm.software_version_id" :disabled="!selectedSoftwareId">
                                <SelectTrigger class="h-11 bg-white border-slate-200 shadow-sm"><SelectValue placeholder="Seleccione versión..." /></SelectTrigger>
                                <SelectContent class="z-[100] max-h-[300px]">
                                    <SelectItem v-for="ver in availableVersions" :key="ver.id" :value="ver.id.toString()">{{ ver.version }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 py-6" v-if="installMode === 'new'">
                    <div class="p-4 bg-emerald-50 rounded-lg border border-emerald-100 mb-2">
                        <p class="text-sm text-emerald-800 font-medium">Registrando nuevo software en catálogo.</p>
                    </div>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div class="space-y-1.5 md:col-span-1">
                            <Label class="text-sm font-bold text-slate-700">Nombre del Software</Label>
                            <Input v-model="softwareForm.new_software_name" placeholder="Ej: Adobe Photoshop 2024" class="h-11 shadow-sm" />
                        </div>
                        <div class="space-y-1.5 md:col-span-1">
                             <Label class="text-sm font-bold text-slate-700">Versión Inicial</Label>
                            <Input v-model="softwareForm.new_software_version" placeholder="Ej: 25.0.0" class="h-11 shadow-sm" />
                        </div>
                    </div>
                </div>
                
                <!-- Common Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-100">
                     <!-- License -->
                    <div class="space-y-1.5 md:col-span-1">
                        <Label class="text-sm font-bold text-slate-700">Vincular Licencia</Label>
                        <Select v-model="softwareForm.license_id">
                            <SelectTrigger class="h-11 bg-white border-slate-200 shadow-sm"><SelectValue placeholder="Opcional (Gratuito)" /></SelectTrigger>
                            <SelectContent class="z-[100] max-h-[300px]">
                                <SelectItem value="">Sin Licencia (Gratis)</SelectItem>
                                <SelectItem v-for="lic in availableLicenses" :key="lic.id" :value="lic.id.toString()">{{ lic.nombre }}</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Observations (Full width) -->
                    <div class="space-y-1.5 md:col-span-1">
                        <Label class="text-sm font-bold text-slate-700">Notas / Ticket</Label>
                        <Input v-model="softwareForm.observaciones" placeholder="Ej: Solicitado en Ticket #1234..." class="h-11 bg-white border-slate-200 shadow-sm" />
                    </div>
                </div>

                <DialogFooter class="gap-2 sm:gap-0 mt-6">
                    <Button variant="ghost" @click="closeInstallModal" class="h-11">Cancelar</Button>
                    <Button type="button" @click="submitInstall" :disabled="softwareForm.processing" class="bg-emerald-600 hover:bg-emerald-700 text-white h-11 px-8 shadow-md">
                        {{ installMode === 'new' ? 'Guardar e Instalar' : 'Instalar Ahora' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>
