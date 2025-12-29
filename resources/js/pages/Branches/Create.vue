<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Save, Building, Plus } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import CityQuickAdd from '@/Components/QuickAdd/CityQuickAdd.vue';
import BranchTypeQuickAdd from '@/Components/QuickAdd/BranchTypeQuickAdd.vue';
import { ref } from 'vue';

const props = defineProps<{
    cities: Array<{ id: number; name: string }>;
    types: Array<{ id: number; name: string }>;
}>();

const localCities = ref([...props.cities]);
const localTypes = ref([...props.types]);

const onCitySuccess = (city: any) => {
    localCities.value.push(city);
    form.city_id = city.id.toString();
};

const onTypeSuccess = (type: any) => {
    localTypes.value.push(type);
    form.branch_type_id = type.id.toString();
};

const form = useForm({
    code: '',
    name: '',
    city_id: undefined as string | undefined,
    branch_type_id: undefined as string | undefined,
    address: '',
    phones: '',
});

const submit = () => {
    form.post(route('branches.store'));
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Ubicaciones', href: '#' },
    { title: 'Sucursales', href: route('branches.index') },
    { title: 'Nueva', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Nueva Sucursal" />

        <div class="max-w-3xl mx-auto p-4 md:p-8">
            <div class="mb-8">
                <Button variant="ghost" as-child class="-ml-2 mb-4 text-muted-foreground hover:text-foreground">
                    <Link :href="route('branches.index')">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver al listado
                    </Link>
                </Button>
                
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 shadow-sm border border-blue-500/20">
                        <Building class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Nueva Sucursal</h1>
                        <p class="text-muted-foreground">Registra una nueva agencia o punto de atención.</p>
                    </div>
                </div>
            </div>

            <Card class="border-border/60 shadow-xl shadow-blue-500/5 bg-card/50 backdrop-blur-sm overflow-hidden">
                <form @submit.prevent="submit">
                    <CardHeader class="border-b border-border/50 bg-muted/20 pb-6">
                        <CardTitle class="text-xl text-blue-700 dark:text-blue-400">Detalles del Establecimiento</CardTitle>
                        <CardDescription>Complete la información técnica y de contacto de la sucursal.</CardDescription>
                    </CardHeader>
                    
                    <CardContent class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <Label for="code" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Código Agencia</Label>
                                <Input 
                                    id="code" 
                                    v-model="form.code" 
                                    class="h-11 bg-background/50 focus-visible:ring-blue-500 shadow-sm font-mono uppercase"
                                    placeholder="Ej. AG-001"
                                />
                                <p v-if="form.errors.code" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.code }}</p>
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <Label for="name" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Nombre de la Sucursal</Label>
                                <Input 
                                    id="name" 
                                    v-model="form.name" 
                                    required 
                                    class="h-11 bg-background/50 focus-visible:ring-blue-500 shadow-sm"
                                    placeholder="Ej. Agencia Central"
                                />
                                <p v-if="form.errors.name" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.name }}</p>
                            </div>
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
                                <Label for="type" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Tipo de Sucursal</Label>
                                <div class="flex gap-2">
                                    <Select v-model="form.branch_type_id">
                                        <SelectTrigger id="type" class="h-11 bg-background/50 flex-1">
                                            <SelectValue placeholder="Seleccione tipo" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem 
                                                v-for="type in localTypes" 
                                                :key="type.id" 
                                                :value="type.id.toString()"
                                                v-show="type.name !== 'ATM'"
                                            >
                                                {{ type.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <BranchTypeQuickAdd @success="onTypeSuccess" />
                                </div>
                                <p v-if="form.errors.branch_type_id" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.branch_type_id }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="phones" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Teléfonos</Label>
                                <Input 
                                    id="phones" 
                                    v-model="form.phones" 
                                    class="h-11 bg-background/50 focus-visible:ring-blue-500 shadow-sm"
                                    placeholder="Ej. 2244556, 70712345"
                                />
                                <p v-if="form.errors.phones" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.phones }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="address" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Dirección</Label>
                                <Input 
                                    id="address" 
                                    v-model="form.address" 
                                    class="h-11 bg-background/50 focus-visible:ring-blue-500 shadow-sm"
                                    placeholder="Ej. Calle Bolívar #456"
                                />
                                <p v-if="form.errors.address" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.address }}</p>
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-end gap-3 border-t border-border/50 bg-muted/10 px-6 py-4">
                        <Button variant="ghost" type="button" as-child class="hover:bg-background">
                            <Link :href="route('branches.index')">Cancelar</Link>
                        </Button>
                        <Button 
                            type="submit" 
                            :disabled="form.processing" 
                            class="bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 transition-all active:scale-95 px-6"
                        >
                            <Save v-if="!form.processing" class="mr-2 h-4 w-4" />
                            <span v-else class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" />
                            {{ form.processing ? 'Guardando...' : 'Guardar Sucursal' }}
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
