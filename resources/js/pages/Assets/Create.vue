<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import axios from 'axios';

// UI Components
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Switch } from '@/components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

// Icons
import { 
    Save, X, Plus, Upload, Trash2, 
    Monitor, Tag, Layers, MapPin, 
    User, Briefcase, FileText, 
    CheckCircle2, AlertTriangle, Info,
    Activity, ShieldAlert, ArrowLeft} from 'lucide-vue-next';

// Quick Add
import GenericQuickAdd from '@/components/QuickAdd/GenericQuickAdd.vue';
import BranchQuickAdd from '@/components/QuickAdd/BranchQuickAdd.vue';


const props = defineProps<{
    tipos_activo: Array<any>;
    marcas: Array<any>;
    estados_activo: Array<any>;
    niveles_criticidad: Array<any>;
    propietarios: Array<any>;
    ubicaciones: Array<any>;
    puntos_red: Array<any>;
    branch_types: Array<any>;
    cities: Array<any>;
}>();

// Dynamic Models Logic (Defined early to avoid hoisting issues)
const availableModels = ref<Array<any>>([]);
const isLoadingModels = ref(false);

// Mutable lists for local updates from QuickAdd
const localTipos = ref([...props.tipos_activo]);
const localMarcas = ref([...props.marcas]);
const localPropietarios = ref([...props.propietarios]);
const localUbicaciones = ref([...props.ubicaciones]);
const localEstados = ref([...props.estados_activo]);
const localCriticidad = ref([...props.niveles_criticidad]);
const localModelos = ref<Array<any>>([]);

const onOwnerAdded = (newItem: any) => {
    localPropietarios.value.push(newItem);
    form.propietario_id = newItem.id.toString();
};

const onTypeAdded = (newItem: any) => {
    localTipos.value.push(newItem);
    form.tipo_activo_id = newItem.id.toString();
};

const onBrandAdded = (newItem: any) => {
    localMarcas.value.push(newItem);
    form.marca_id = newItem.id.toString();
};

const onStatusAdded = (newStatus: any) => {
    localEstados.value.push(newStatus);
    form.estado_activo_id = newStatus.id.toString();
};

const onCriticalityAdded = (newCrit: any) => {
    localCriticidad.value.push(newCrit);
    form.criticidad_id = newCrit.id.toString();
};

const onLocationAdded = (data: any) => {
    // Expecting data to contain 'ubicacion' from BranchController
    if (data.ubicacion) {
        localUbicaciones.value.push(data.ubicacion);
        form.ubicacion_id = data.ubicacion.id.toString();
    }
}; // Replaces availableModels for uniformity if we want, or sync.
// Actually availableModels logic is specific to fetch. Let's keep availableModels but expose a way to inject.
// wait, existing availableModels is overwritten by fetchModels.
// To support quick add, we need to push to availableModels.

const onModelAdded = (newItem: any) => {
    // Manually push to availableModels
    availableModels.value.push(newItem);
    form.modelo_id = newItem.id.toString();
};

// Form Reactive State
const form = useForm({
    // General
    codigo_activo: '',
    numero_serie: '',
    tipo_activo_id: '',
    marca_id: '',
    modelo_id: '',
    estado_activo_id: '1', // Default to Activo (assuming ID 1)
    criticidad_id: '',
    propietario_id: '',
    
    // Technical (EAV)
    atributos: {} as Record<string, string>,
    
    // Network
    ip_address: '',
    mac_ethernet: '',
    mac_wifi: '',
    punto_red_id: '',
    
    // Location / Custody
    ubicacion_id: '',
    usuario_responsable_id: '', // Not yet passed from controller, keeping logic open
    
    // Finance
    fecha_adquisicion: '',
    valor_adquisicion: '',
    vida_util_anios: '',

});

// Cascading Location Logic
const selectedCityId = ref('');

const filteredUbicaciones = computed(() => {
    if (!selectedCityId.value) return [];
    return localUbicaciones.value.filter(u => u.ciudad_id?.toString() === selectedCityId.value);
});

// Watch for city change to reset location if needed (optional but good UX)
watch(selectedCityId, (newVal) => {
    // If current location doesn't match new city, clear it
    const currentLoc = localUbicaciones.value.find(u => u.id.toString() === form.ubicacion_id);
    if (currentLoc && currentLoc.ciudad_id?.toString() !== newVal) {
         form.ubicacion_id = '';
    }
});

// Auto-select city if location is selected (reverse logic for edit or quick add)
watch(() => form.ubicacion_id, (newId) => {
     const loc = localUbicaciones.value.find(u => u.id.toString() === newId);
     if (loc && loc.ciudad_id) {
         selectedCityId.value = loc.ciudad_id.toString();
     }
});

const fetchModels = async (marcaId: string) => {
    if (!marcaId) {
        availableModels.value = [];
        return;
    }
    isLoadingModels.value = true;
    try {
        const response = await axios.get(route('api.models', marcaId));
        availableModels.value = response.data;
    } catch (error) {
        console.error("Error fetching models:", error);
    } finally {
        isLoadingModels.value = false;
    }
};

watch(() => form.marca_id, (newId) => {
    form.modelo_id = ''; // Reset model on brand change
    fetchModels(newId);
});



// Dynamic Attribute Definitions Logic
const definitions = ref<Array<any>>([]);
const isLoadingDefinitions = ref(false);

const fetchDefinitions = async (typeId: string) => {
    if (!typeId) {
        definitions.value = [];
        return;
    }
    isLoadingDefinitions.value = true;
    try {
        const response = await axios.get(route('api.attributes', typeId));
        definitions.value = response.data;
        
        // Initialize attributes based on definitions
        // logic: preserve existing values if user switches back and forth?
        // simple logic: ensure keys exist
        definitions.value.forEach(def => {
             if (!(def.nombre in form.atributos)) {
                 form.atributos[def.nombre] = '';
             }
        });
        
    } catch (error) {
        console.error("Error fetching definitions:", error);
    } finally {
        isLoadingDefinitions.value = false;
    }
};

watch(() => form.tipo_activo_id, (newId) => {
    fetchDefinitions(newId);
});

// EAV Logic (Legacy / Extra)
const customAttributes = ref<Array<{ key: string, value: string }>>([]);

const addAttribute = () => {
    customAttributes.value.push({ key: '', value: '' });
};

const removeAttribute = (index: number) => {
    customAttributes.value.splice(index, 1);
};

// Sync customAttributes to form.atributos before submit
const prepareAttributes = () => {
    const attrs: Record<string, string> = {};
    customAttributes.value.forEach(item => {
        if (item.key) attrs[item.key] = item.value;
    });
    form.atributos = attrs;
};

// Auto-fill attributes based on Type (Creative/Functional touch)
// Auto-fill attributes based on Type (Creative/Functional touch)
watch(() => form.tipo_activo_id, (newId) => {
    const selectedType = localTipos.value.find(t => t.id.toString() === newId);
    if (!selectedType) return;
    
    // Simple heuristic for demo purposes
    const typeName = selectedType.nombre.toLowerCase(); // Ensure 'nombre' or 'name' compatibility?
    // AssetType might have 'name' from controller but 'nombre' from props? 
    // Props use 'nombre' (see index controller). New item from quick add uses 'name'.
    // Let's normalize name access: typeName = selectedType.nombre || selectedType.name;
    const name = (selectedType.nombre || selectedType.name || '').toLowerCase();
    
    // Clear current if empty? No, keep user data. Just add suggestions if missing.
    const suggestions = [];
    if (name.includes('laptop') || name.includes('pc') || name.includes('computadora')) {
        suggestions.push({ key: 'Procesador', value: '' });
        suggestions.push({ key: 'RAM (GB)', value: '' });
        suggestions.push({ key: 'Disco Duro', value: '' });
        suggestions.push({ key: 'Sistema Operativo', value: '' });
    } else if (name.includes('ups')) {
        suggestions.push({ key: 'Potencia (KVA)', value: '' });
        suggestions.push({ key: 'Baterías', value: '' });
    } else if (name.includes('impresora')) {
        suggestions.push({ key: 'Tipo', value: 'Láser/Tinta' });
        suggestions.push({ key: 'Contador Páginas', value: '0' });
    }

    // Append unique suggestions if NO definitions are found (Legacy Mode)
    if (definitions.value.length === 0) {
        suggestions.forEach(s => {
            if (!customAttributes.value.some(ca => ca.key === s.key)) {
                customAttributes.value.push(s);
            }
        });
    }
});

const submit = () => {
    prepareAttributes();
    form.post(route('assets.store'), {
        onSuccess: () => {
            // Toast handled by layout usually
        }
    });
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Inventario', href: '#' },
    { title: 'Activos', href: route('assets.index') },
    { title: 'Nuevo', href: route('assets.create') },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Nuevo Activo" />

        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" as-child class="shrink-0">
                        <Link :href="route('assets.index')">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Registro de Activo</h1>
                        <p class="text-sm text-muted-foreground">Alta de nuevo equipo en la CMDB.</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <Tabs default-value="general" class="w-full space-y-6">
                    
                    <!-- Tabs Navigation -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="overflow-x-auto w-full sm:w-auto pb-1">
                            <TabsList class="inline-flex min-w-full sm:min-w-0 bg-muted/20 p-1 h-auto">
                                <TabsTrigger value="general" class="flex items-center gap-2 px-4 py-2 text-xs sm:text-sm">
                                    <Box class="h-4 w-4" /> General
                                </TabsTrigger>
                                <TabsTrigger value="financial" class="flex items-center gap-2 px-4 py-2 text-xs sm:text-sm">
                                    <DollarSign class="h-4 w-4" /> Financiero
                                </TabsTrigger>
                            </TabsList>
                        </div>
                        
                        <div class="flex gap-2 w-full sm:w-auto">
                            <Button type="button" variant="ghost" as-child class="flex-1 sm:flex-none">
                                <Link :href="route('assets.index')">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="flex-1 sm:flex-none bg-primary text-primary-foreground shadow-lg hover:shadow-xl transition-all">
                                <Save class="mr-2 h-4 w-4" />
                                <span class="whitespace-nowrap">Guardar Activo</span>
                            </Button>
                        </div>
                    </div>

                    <!-- 1. GENERAL -->
                    <TabsContent value="general">
                        <Card class="border-t-4 border-t-blue-500 shadow-md">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2 text-blue-600">
                                    <FileText class="h-5 w-5" /> Identificación General
                                </CardTitle>
                                <CardDescription>Datos principales para identificar el activo en el inventario.</CardDescription>
                            </CardHeader>
                            <CardContent class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Type -->
                                <div class="space-y-2">
                                    <Label>Tipo de Activo <span class="text-red-500">*</span></Label>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <Select v-model="form.tipo_activo_id">
                                                <SelectTrigger class="bg-background">
                                                    <SelectValue placeholder="Seleccione Tipo" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="t in localTipos" :key="t.id" :value="t.id.toString()">
                                                        {{ t.nombre || t.name }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <GenericQuickAdd 
                                            title="Nuevo Tipo de Activo" 
                                            endpoint="asset-types.store"
                                            :icon="Monitor"
                                            @success="onTypeAdded"
                                        />
                                    </div>
                                    <p v-if="form.errors.tipo_activo_id" class="text-sm text-destructive">{{ form.errors.tipo_activo_id }}</p>
                                </div>

                                <!-- Brand -->
                                <div class="space-y-2">
                                    <Label>Marca relative <span class="text-red-500">*</span></Label>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <Select v-model="form.marca_id">
                                                <SelectTrigger class="bg-background">
                                                    <SelectValue placeholder="Seleccione Marca" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="m in localMarcas" :key="m.id" :value="m.id.toString()">
                                                        {{ m.nombre }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <GenericQuickAdd 
                                            title="Nueva Marca" 
                                            endpoint="brands.store"
                                            :icon="Tag"
                                            @success="onBrandAdded"
                                        />
                                    </div>
                                    <p v-if="form.errors.marca_id" class="text-sm text-destructive">{{ form.errors.marca_id }}</p>
                                </div>

                                <!-- Model (Dynamic) -->
                                <div class="space-y-2">
                                    <Label>Modelo <span class="text-red-500">*</span></Label>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <Select v-model="form.modelo_id" :disabled="!form.marca_id || isLoadingModels">
                                                <SelectTrigger class="bg-background">
                                                    <SelectValue :placeholder="isLoadingModels ? 'Cargando...' : 'Seleccione Modelo'" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="mod in availableModels" :key="mod.id" :value="mod.id.toString()">
                                                        {{ mod.nombre }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                         <GenericQuickAdd 
                                            title="Nuevo Modelo" 
                                            endpoint="models.store"
                                            :params="{ marca_id: form.marca_id }"
                                            :disabled="!form.marca_id"
                                            :icon="Layers"
                                            @success="onModelAdded"
                                        />
                                    </div>
                                    <p v-if="availableModels.length === 0 && form.marca_id" class="text-xs text-muted-foreground">
                                        No hay modelos para esta marca.
                                    </p>
                                    <p v-if="form.errors.modelo_id" class="text-sm text-destructive">{{ form.errors.modelo_id }}</p>
                                </div>

                                <!-- Serial -->
                                <div class="space-y-2">
                                    <Label>Número de Serie <span class="text-red-500">*</span></Label>
                                    <Input v-model="form.numero_serie" placeholder="S/N del fabricante" class="font-mono" />
                                    <p v-if="form.errors.numero_serie" class="text-sm text-destructive">{{ form.errors.numero_serie }}</p>
                                </div>

                                <!-- Code -->
                                <div class="space-y-2">
                                    <Label>Código Activo (Etiqueta) <span class="text-red-500">*</span></Label>
                                    <Input v-model="form.codigo_activo" placeholder="Ej. BFO-00123" class="font-mono font-bold text-blue-600" />
                                    <p v-if="form.errors.codigo_activo" class="text-sm text-destructive">{{ form.errors.codigo_activo }}</p>
                                </div>

                                <!-- Status -->
                                <div class="space-y-2">
                                    <Label>Estado Actual <span class="text-red-500">*</span></Label>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <Select v-model="form.estado_activo_id">
                                                <SelectTrigger class="bg-background">
                                                    <SelectValue placeholder="Estado" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="e in localEstados" :key="e.id" :value="e.id.toString()">
                                                        {{ e.nombre }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <GenericQuickAdd 
                                            title="Nuevo Estado" 
                                            endpoint="asset-status.store"
                                            :icon="Activity"
                                            @success="onStatusAdded"
                                        />
                                    </div>
                                </div>

                                <!-- Criticality -->
                                <div class="space-y-2">
                                    <Label>Criticidad <span class="text-red-500">*</span></Label>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <Select v-model="form.criticidad_id">
                                                <SelectTrigger class="bg-background">
                                                    <SelectValue placeholder="Nivel" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="c in localCriticidad" :key="c.id" :value="c.id.toString()">
                                                       <span :style="{ color: c.color }">{{ c.nombre }}</span>
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <GenericQuickAdd 
                                            title="Nueva Criticidad" 
                                            endpoint="criticality.store"
                                            :icon="ShieldAlert"
                                            enable-color
                                            @success="onCriticalityAdded"
                                        />
                                    </div>
                                </div>

                                <!-- Owner -->
                                <div class="space-y-2">
                                    <Label>Propietario Legal <span class="text-red-500">*</span></Label>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <Select v-model="form.propietario_id">
                                                <SelectTrigger class="bg-background">
                                                    <SelectValue placeholder="Propietario" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="p in localPropietarios" :key="p.id" :value="p.id.toString()">
                                                        {{ p.nombre }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <GenericQuickAdd 
                                            title="Nuevo Propietario" 
                                            endpoint="owners.store"
                                            :icon="Briefcase"
                                            @success="onOwnerAdded"
                                        />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>


                        <!-- Location Section (Moved Here) -->
                        <Card class="border-t-4 border-t-orange-500 shadow-md">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2 text-orange-600">
                                    <MapPin class="h-5 w-5" /> Ubicación y Asignación
                                </CardTitle>
                                <CardDescription>Datos principales para identificar el activo en el inventario.</CardDescription>
                            </CardHeader>
                            <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- City Select -->
                                <div class="space-y-2">
                                    <Label>Ciudad</Label>
                                    <Select v-model="selectedCityId">
                                        <SelectTrigger class="bg-background">
                                            <SelectValue placeholder="Seleccione Ciudad" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="c in cities" :key="c.id" :value="c.id.toString()">
                                                {{ c.nombre }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <!-- Branch Select (Filtered) -->
                                <div class="space-y-2">
                                    <Label>Sucursal / Ubicación <span class="text-red-500">*</span></Label>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <Select v-model="form.ubicacion_id" :disabled="!selectedCityId">
                                                <SelectTrigger class="bg-background">
                                                    <SelectValue placeholder="Seleccione Sucursal" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="u in filteredUbicaciones" :key="u.id" :value="u.id.toString()">
                                                        {{ u.nombre }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                         <BranchQuickAdd 
                                            :cities="cities"
                                            :types="branch_types"
                                            @success="onLocationAdded"
                                        />
                                    </div>
                                    <p v-if="!selectedCityId" class="text-xs text-muted-foreground">Seleccione una ciudad primero.</p>
                                    <p v-if="form.errors.ubicacion_id" class="text-sm text-destructive">{{ form.errors.ubicacion_id }}</p>
                                </div>
                                
                                <div class="space-y-2 md:col-span-2">
                                    <Label>Responsable (Custodio)</Label>
                                    <div class="p-4 border rounded bg-muted/20 text-center text-sm text-muted-foreground">
                                        Se asignará posteriormente mediante Acta de Entrega.
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <!-- (Technical Specs moved to separate form) -->

                    <!-- (Network Config moved to separate form) -->
                    


                    <!-- 5. FINANCIAL -->
                    <TabsContent value="financial">
                        <Card class="border-t-4 border-t-green-500 shadow-md">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2 text-green-600">
                                    <DollarSign class="h-5 w-5" /> Datos Financieros (NIIF)
                                </CardTitle>
                                <CardDescription>Valoración y depreciación del activo.</CardDescription>
                            </CardHeader>
                            <CardContent class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <Label>Fecha Adquisición</Label>
                                    <Input type="date" v-model="form.fecha_adquisicion" />
                                </div>
                                
                                <div class="space-y-2">
                                    <Label>Valor Adquisición (Bs)</Label>
                                    <Input type="number" step="0.01" v-model="form.valor_adquisicion" placeholder="0.00" />
                                </div>
                                
                                <div class="space-y-2">
                                    <Label>Vida Útil Estimada (Años)</Label>
                                    <Input type="number" v-model="form.vida_util_anios" placeholder="4" />
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                </Tabs>
            </form>
        </div>
    </AppLayout>
</template>
