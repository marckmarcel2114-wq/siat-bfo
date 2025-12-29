<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Save, Plus } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { ref } from 'vue';
import AssetTypeQuickAdd from '@/Components/QuickAdd/AssetTypeQuickAdd.vue';
import BranchQuickAdd from '@/Components/QuickAdd/BranchQuickAdd.vue';

const props = defineProps<{
    types: Array<{ id: number; name: string }>;
    branches: Array<{ id: number; name: string; city: { name: string } }>;
    cities: Array<{ id: number; name: string }>;
    branchTypes: Array<{ id: number; name: string }>;
}>();

const localTypes = ref([...props.types]);
const localBranches = ref([...props.branches]);

const onTypeSuccess = (type: any) => {
    localTypes.value.push(type);
    form.asset_type_id = type.id.toString();
};

const onBranchSuccess = (branch: any) => {
    localBranches.value.push(branch);
    form.location_id = branch.id.toString();
};

const form = useForm({
    asset_type_id: '',
    brand: '',
    model: '',
    serial_number: '',
    code_internal: '',
    purchase_date: '',
    warranty_expiry_date: '',
    location_id: '',
    status: 'free',
    specs: {}, // TODO: Dynamic fields based on type? For now, simplistic.
    notes: '',
});

const submit = () => {
    form.post(route('assets.store'));
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

        <div class="max-w-4xl mx-auto p-4 md:p-6 space-y-6">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="route('assets.index')">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Registrar Activo</h1>
                    <p class="text-muted-foreground">Ingresa los detalles técnicos del nuevo equipo.</p>
                </div>
            </div>

            <Card class="border-border/50 shadow-sm">
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Información del Equipo</CardTitle>
                        <CardDescription>Los campos marcados con * son obligatorios.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="type">Tipo de Activo *</Label>
                                <div class="flex gap-2">
                                    <Select v-model="form.asset_type_id">
                                        <SelectTrigger id="type" class="h-10 bg-background flex-1">
                                            <SelectValue placeholder="Seleccione un tipo" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="type in localTypes" :key="type.id" :value="type.id.toString()">
                                                {{ type.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <AssetTypeQuickAdd @success="onTypeSuccess" />
                                </div>
                                <p v-if="form.errors.asset_type_id" class="text-sm text-destructive">{{ form.errors.asset_type_id }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="status">Estado Inicial *</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger id="status" class="h-10 bg-background">
                                        <SelectValue placeholder="Seleccione estado" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="free">Disponible (En depósito)</SelectItem>
                                        <SelectItem value="assigned">Asignado (Requiere responsable)</SelectItem>
                                        <SelectItem value="maintenance">Mantenimiento</SelectItem>
                                        <SelectItem value="broken">Dañado</SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.status" class="text-sm text-destructive">{{ form.errors.status }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="brand">Marca</Label>
                                <Input id="brand" v-model="form.brand" placeholder="Ej. Dell, HP, Cisco" />
                                <p v-if="form.errors.brand" class="text-sm text-destructive">{{ form.errors.brand }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="model">Modelo</Label>
                                <Input id="model" v-model="form.model" placeholder="Ej. Latitude 5420" />
                                <p v-if="form.errors.model" class="text-sm text-destructive">{{ form.errors.model }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="serial">Número de Serie</Label>
                                <Input id="serial" v-model="form.serial_number" placeholder="Identificador único del fabricante" />
                                <p v-if="form.errors.serial_number" class="text-sm text-destructive">{{ form.errors.serial_number }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="code">Código Interno</Label>
                                <Input id="code" v-model="form.code_internal" placeholder="Ej. ACT-001" />
                                <p v-if="form.errors.code_internal" class="text-sm text-destructive">{{ form.errors.code_internal }}</p>
                            </div>
                        </div>

                        <div class="border-t border-border/50 pt-4">
                            <h3 class="text-sm font-medium mb-4">Ubicación y Garantía</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="location">Ubicación Física</Label>
                                    <div class="flex gap-2">
                                        <Select v-model="form.location_id">
                                            <SelectTrigger id="location" class="h-10 bg-background flex-1">
                                                <SelectValue placeholder="Sin ubicación definida" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="none">Sin ubicación definida</SelectItem>
                                                <SelectItem v-for="branch in localBranches" :key="branch.id" :value="branch.id.toString()">
                                                    {{ branch.city?.name }} - {{ branch.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <BranchQuickAdd 
                                            :cities="cities" 
                                            :types="branchTypes" 
                                            @success="onBranchSuccess" 
                                        />
                                    </div>
                                    <p v-if="form.errors.location_id" class="text-sm text-destructive">{{ form.errors.location_id }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label for="purchase_date">Fecha Compra</Label>
                                        <Input id="purchase_date" type="date" v-model="form.purchase_date" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="warranty">Fin Garantía</Label>
                                        <Input id="warranty" type="date" v-model="form.warranty_expiry_date" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 border-t border-border/50 pt-4">
                            <Label for="notes">Notas Adicionales</Label>
                            <Textarea id="notes" v-model="form.notes" placeholder="Observaciones sobre el estado, accesorios incluidos, etc." rows="3" />
                        </div>

                    </CardContent>
                    <CardFooter class="flex justify-end gap-3 border-t border-border/50 px-6 py-4">
                        <Button variant="ghost" type="button" as-child>
                            <Link :href="route('assets.index')">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing" class="bg-primary text-primary-foreground">
                            <Save class="mr-2 h-4 w-4" />
                            Guardar Activo
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
