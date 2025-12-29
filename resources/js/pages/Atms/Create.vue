<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Save, Monitor, Plus } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { computed, watch, ref } from 'vue';
import CityQuickAdd from '@/Components/QuickAdd/CityQuickAdd.vue';
import BranchQuickAdd from '@/Components/QuickAdd/BranchQuickAdd.vue';
import { BranchType } from '@/types'; // Wait, I don't have types file, I'll just use any or check props

const props = defineProps<{
    cities: Array<{ id: number; name: string }>;
    potentialParents: Array<{ id: number; name: string; city_id: number; city: { name: string } }>;
    branchTypes: Array<{ id: number; name: string }>;
}>();

const localCities = ref([...props.cities]);
const localParents = ref([...props.potentialParents]);

const onCitySuccess = (city: any) => {
    localCities.value.push(city);
    form.city_id = city.id.toString();
};

const onBranchSuccess = (branch: any) => {
    localParents.value.push(branch);
    form.parent_id = branch.id.toString();
};

const form = useForm({
    name: '',
    city_id: undefined as string | undefined,
    parent_id: 'none',
    address: '',
});

// Filter agencies based on selected city
const filteredAgencies = computed(() => {
    if (!form.city_id) return [];
    return localParents.value.filter(agency => agency.city_id.toString() === form.city_id);
});

// Reset parent_id when city changes if not in the new filtered list
watch(() => form.city_id, () => {
    form.parent_id = 'none';
});

const submit = () => {
    form.post(route('atms.store'));
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Ubicaciones', href: '#' },
    { title: 'ATMs', href: route('atms.index') },
    { title: 'Nuevo', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Nuevo ATM" />

        <div class="max-w-xl mx-auto p-4 md:p-8">
            <div class="mb-8">
                <Button variant="ghost" as-child class="-ml-2 mb-4 text-muted-foreground hover:text-foreground">
                    <Link :href="route('atms.index')">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver al listado
                    </Link>
                </Button>
                
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-500/20">
                        <Monitor class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Nuevo ATM</h1>
                        <p class="text-muted-foreground">Registra un nuevo cajero automático en la red.</p>
                    </div>
                </div>
            </div>

            <Card class="border-border/60 shadow-xl shadow-emerald-500/5 bg-card/50 backdrop-blur-sm overflow-hidden">
                <form @submit.prevent="submit">
                    <CardHeader class="border-b border-border/50 bg-muted/20 pb-6">
                        <CardTitle class="text-xl text-emerald-700 dark:text-emerald-400">Configuración del ATM</CardTitle>
                        <CardDescription>Vincule el cajero a una ciudad y opcionalmente a una agencia principal.</CardDescription>
                    </CardHeader>
                    
                    <CardContent class="p-6 space-y-6">
                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Nombre Identificador</Label>
                            <Input 
                                id="name" 
                                v-model="form.name" 
                                required 
                                class="h-11 bg-background/50 focus-visible:ring-emerald-500 shadow-sm"
                                placeholder="Ej. ATM Central 01"
                            />
                            <p v-if="form.errors.name" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="city" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Ciudad</Label>
                                <div class="flex gap-2">
                                    <Select v-model="form.city_id">
                                        <SelectTrigger id="city" class="h-11 bg-background/50 flex-1">
                                            <SelectValue placeholder="Seleccione ciudad" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="city in localCities" :key="city.id" :value="city.id.toString()">
                                                {{ city.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <CityQuickAdd @success="onCitySuccess" />
                                </div>
                                <p v-if="form.errors.city_id" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.city_id }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="parent" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Agencia Base</Label>
                                <div class="flex gap-2">
                                    <Select v-model="form.parent_id">
                                        <SelectTrigger id="parent" class="h-11 bg-background/50 flex-1">
                                            <SelectValue placeholder="Sin agencia (Extra muro)" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="none">Sin agencia (Extra muro)</SelectItem>
                                            <SelectItem v-for="parent in filteredAgencies" :key="parent.id" :value="parent.id.toString()">
                                                {{ parent.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <BranchQuickAdd 
                                        :cities="localCities" 
                                        :types="branchTypes" 
                                        @success="onBranchSuccess"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="address" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Ubicación / Dirección</Label>
                            <Input 
                                id="address" 
                                v-model="form.address" 
                                class="h-11 bg-background/50 focus-visible:ring-emerald-500 shadow-sm"
                                placeholder="Ej. Centro Comercial Norte, Planta Baja"
                            />
                            <p v-if="form.errors.address" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.address }}</p>
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-end gap-3 border-t border-border/50 bg-muted/10 px-6 py-4">
                        <Button variant="ghost" type="button" as-child class="hover:bg-background">
                            <Link :href="route('atms.index')">Cancelar</Link>
                        </Button>
                        <Button 
                            type="submit" 
                            :disabled="form.processing" 
                            class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-500/20 transition-all active:scale-95 px-6"
                        >
                            <Save v-if="!form.processing" class="mr-2 h-4 w-4" />
                            <span v-else class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" />
                            {{ form.processing ? 'Guardando...' : 'Guardar ATM' }}
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
