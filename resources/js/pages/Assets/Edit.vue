<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue';
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

// Icons
import { 
    ArrowLeft, Save, Plus, Trash2, 
    Monitor, Server, Network, MapPin, DollarSign,
    Box, FileText, Wifi, ShieldCheck, AlertTriangle,
    Activity, ShieldAlert,
    Info,
    Tag, Layers, Briefcase,
    UserPlus
} from 'lucide-vue-next';

const props = defineProps<{
    asset: any;
    tipos_activo: Array<any>;
    marcas: Array<any>;
    modelos: Array<any>; // Initial models for the asset's brand
    estados_activo: Array<any>;
    niveles_criticidad: Array<any>;
    propietarios: Array<any>;
    ubicaciones: Array<any>;
    current_network?: any;
    branch_types: Array<any>;
    cities: Array<any>;
    current_network?: any;
    current_assignment?: any;
}>();

// Quick Add Import
import GenericQuickAdd from '@/components/QuickAdd/GenericQuickAdd.vue';
import BranchQuickAdd from '@/components/QuickAdd/BranchQuickAdd.vue';


// Local Mutable Lists
const localTipos = ref([...props.tipos_activo]);
const localMarcas = ref([...props.marcas]);
const localPropietarios = ref([...props.propietarios]);
const localUbicaciones = ref([...props.ubicaciones]);
const localEstados = ref([...props.estados_activo]);
const localCriticidad = ref([...props.niveles_criticidad]);
const localModelos = ref<Array<any>>([]);
const onTypeAdded = (newItem: any) => {
    localTipos.value.push(newItem);
    form.tipo_activo_id = newItem.id.toString();
};

const onBrandAdded = (newItem: any) => {
    localMarcas.value.push(newItem);
    form.marca_id = newItem.id.toString();
    availableModels.value = [];
};

const onModelAdded = (newItem: any) => {
    availableModels.value.push(newItem);
    form.modelo_id = newItem.id.toString();
};

const onOwnerAdded = (newItem: any) => {
    localPropietarios.value.push(newItem);
    form.propietario_id = newItem.id.toString();
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
    if (data.ubicacion) {
        localUbicaciones.value.push(data.ubicacion);
        form.ubicacion_id = data.ubicacion.id.toString();
    }
};

// Form Reactive State
const form = useForm({
    // General
    codigo_activo: props.asset.codigo_activo,
    numero_serie: props.asset.numero_serie,
    tipo_activo_id: props.asset.tipo_activo_id.toString(),
    marca_id: props.asset.modelo?.marca_id.toString() || '',
    modelo_id: props.asset.modelo_id.toString(),
    estado_activo_id: props.asset.estado_activo_id.toString(),
    criticidad_id: props.asset.criticidad_id.toString(),
    propietario_id: props.asset.propietario_id.toString(),
    
    // Technical (EAV)
    atributos: {} as Record<string, string>,
    
    // Network (Read-onlyish in Edit, or update if allowed)
    // For now we allow update but warn it wont create history unless separate flow, 
    // but Controller logic for Update was minimal. Let's populate for display/minor fix.
    // Ideally Network/Custody are separate actions.
    
    // Location / Custody
    ubicacion_id: props.asset.ubicacion_id.toString(),
    detalle_ubicacion: props.asset.detalle_ubicacion || '',
    
    // Finance
    fecha_adquisicion: props.asset.fecha_adquisicion,
    valor_adquisicion: props.asset.valor_adquisicion,
    vida_util_anios: props.asset.vida_util_anios,

});

// Cascading Location Logic
const selectedCityId = ref('');

// Initialize city from existing location
onMounted(() => {
    if (form.ubicacion_id) {
        const u = localUbicaciones.value.find(loc => loc.id.toString() === form.ubicacion_id);
        if (u && u.ciudad_id) {
            selectedCityId.value = u.ciudad_id.toString();
        }
    }
});

const filteredUbicaciones = computed(() => {
    if (!selectedCityId.value) return [];
    return localUbicaciones.value.filter(u => u.ciudad_id?.toString() === selectedCityId.value);
});

// Watch for city change to reset location if needed (logic specific for edit)
watch(selectedCityId, (newVal) => {
    const currentLoc = localUbicaciones.value.find(u => u.id.toString() === form.ubicacion_id);
    // Only clear if the current location does NOT belong to the new city
    if (currentLoc && currentLoc.ciudad_id?.toString() !== newVal) {
         form.ubicacion_id = '';
    }
});

// Dynamic Models Logic
const availableModels = ref<Array<any>>(props.modelos || []);
const isLoadingModels = ref(false);

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

watch(() => form.marca_id, (newId, oldId) => {
    if (newId !== oldId && oldId !== '') {
        form.modelo_id = ''; // Reset only if changed by user interactive
        fetchModels(newId);
    } else if (newId && availableModels.value.length === 0) {
        // Initial load or edge case
        fetchModels(newId);
    }
});

// EAV Logic
const customAttributes = ref<Array<{ key: string, value: string }>>([]);

// Dynamic Attribute Definitions
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
        
        // Post-process: Remove defined attributes from customAttributes list to avoid duplication
        definitions.value.forEach(def => {
             // 1. Ensure form.atributos has the value (it should from mount)
             if (!(def.nombre in form.atributos)) {
                 form.atributos[def.nombre] = ''; // Initialize if missing
             }
             
             // 2. Remove from customAttributes if present there
             const idx = customAttributes.value.findIndex(ca => ca.key === def.nombre);
             if (idx !== -1) {
                 // Update form.atributos just in case (though mount did it)
                 form.atributos[def.nombre] = customAttributes.value[idx].value;
                 // Remove from extra list
                 customAttributes.value.splice(idx, 1);
             }
        });
        
    } catch (error) {
        console.error("Error fetching definitions:", error);
    } finally {
        isLoadingDefinitions.value = false;
    }
};

onMounted(() => {
    // 1. Populate form.atributos dictionary from props
    if (props.asset.atributos) {
        const attrs: Record<string, string> = {};
        props.asset.atributos.forEach((a: any) => {
            if (a.definicion?.nombre) attrs[a.definicion.nombre] = a.valor;
        });
        form.atributos = attrs;
    }

    // 2. Populate customAttributes array (initial state: everything is custom until defs load)
    if (props.asset.atributos) {
        customAttributes.value = props.asset.atributos.map((a: any) => ({
            key: a.definicion?.nombre || '',
            value: a.valor
        }));
    }
    
    // 3. Fetch Definitions (which will filter customAttributes)
    if (form.tipo_activo_id) {
        fetchDefinitions(form.tipo_activo_id);
    }
});

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

const submit = () => {
    prepareAttributes();
    form.put(route('assets.update', props.asset.id), {
        onSuccess: () => {
            // Toast handled by layout usually
        }
    });
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Inventario', href: '#' },
    { title: 'Activos', href: route('assets.index') },
    { title: props.asset.codigo_activo, href: route('assets.show', props.asset.id) },
    { title: 'Editar', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Editar Activo" />

        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col gap-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3 sm:gap-4">
                        <Button variant="outline" size="icon" as-child class="shrink-0">
                            <Link :href="route('assets.show', asset.id)">
                                <ArrowLeft class="h-4 w-4" />
                            </Link>
                        </Button>
                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground truncate">Editar Activo: {{ asset.codigo_activo }}</h1>
                            <p class="text-sm sm:text-base text-muted-foreground truncate">Modificar información técnica o administrativa.</p>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <Tabs default-value="general" class="w-full space-y-6">
                    
                    <!-- Responsive Tabs Navigation -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="overflow-x-auto pb-1 -mx-4 px-4 sm:mx-0 sm:px-0 w-full lg:w-auto">
                            <TabsList class="inline-flex bg-muted/20 p-1 h-auto min-w-full sm:min-w-0">
                                <TabsTrigger value="general" class="flex items-center gap-2 px-3 py-2 text-xs sm:text-sm">
                                    <Box class="h-4 w-4" /> General
                                </TabsTrigger>
                                <TabsTrigger value="financial" class="flex items-center gap-2 px-3 py-2 text-xs sm:text-sm">
                                    <DollarSign class="h-4 w-4" /> Financiero
                                </TabsTrigger>
                            </TabsList>
                        </div>
                        
                        <div class="flex gap-2 w-full sm:w-auto">
                            <Button type="button" variant="ghost" as-child class="flex-1 sm:flex-none">
                                <Link :href="route('assets.show', asset.id)">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="bg-primary text-primary-foreground flex-1 sm:flex-none">
                                <Save class="mr-2 h-4 w-4" />
                                <span class="whitespace-nowrap">Actualizar</span>
                            </Button>
                        </div>
                    </div>

                    <!-- 1. GENERAL -->
                    <TabsContent value="general">
                        <Card class="border-t-4 border-t-amber-500 shadow-md">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2 text-amber-600">
                                    <FileText class="h-5 w-5" /> Identificación General
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Type -->
                                <div class="space-y-2">
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
                                    <MapPin class="h-5 w-5" /> Ubicación y Custodia
                                </CardTitle>
                                <CardDescription>Modificar ubicación física.</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div class="bg-yellow-50 text-yellow-800 p-4 rounded-md mb-6 flex items-start gap-3">
                                    <AlertTriangle class="h-5 w-5 mt-0.5" />
                                    <div>
                                        <p class="font-bold">Advertencia</p>
                                        <p class="text-sm">Cambiar la ubicación aquí actualizará el registro pero NO generará un Acta de Movimiento. Para traslados formales, use el módulo de Asignaciones.</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                        <Label>Sucursal / Agencia <span class="text-red-500">*</span></Label>
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

                                    <!-- Detalle Ubicacion (New) -->
                                    <div class="space-y-2 md:col-span-2">
                                        <Label>Detalle de Ubicación (Referencia)</Label>
                                        <Input v-model="form.detalle_ubicacion" placeholder="Ej. Piso 1, Oficina de Gerencia, etc." />
                                        <p class="text-xs text-muted-foreground">Especifique el lugar exacto dentro de la sucursal.</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <!-- (Technical Specs moved to separate form) -->



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

                <!-- Actions Footer -->
                <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t flex items-center justify-between md:static md:bg-transparent md:border-0 md:p-0 md:mt-8">
                    <div class="flex gap-2">
                        <Button type="button" variant="outline" as-child>
                            <Link :href="route('assets.show', asset.id)">Cancelar</Link>
                        </Button>

                    </div>
                    
                    <Button type="submit" :disabled="form.processing" class="bg-primary text-primary-foreground">
                        <Save class="mr-2 h-4 w-4" />
                        Guardar Cambios
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
