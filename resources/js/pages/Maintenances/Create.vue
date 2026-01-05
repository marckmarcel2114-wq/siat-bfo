<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Wrench, AlertOctagon } from 'lucide-vue-next';

const props = defineProps<{
    asset: any;
    tipos: Array<any>;
    proveedores: Array<any>;
    estados: Array<any>;
}>();

const form = useForm({
    activo_id: props.asset.id,
    tipo_mantenimiento_id: '',
    proveedor_id: '',
    fecha_inicio: new Date().toISOString().split('T')[0],
    hoja_trabajo: '' // Description
});

const submit = () => {
    form.post(route('maintenances.store'));
};
</script>

<template>
    <AppLayout :breadcrumbs="[
        { title: 'Inventario', href: route('assets.index') },
        { title: asset.codigo_activo, href: route('assets.show', asset.id) },
        { title: 'Solicitar Mantenimiento', href: '#' }
    ]">
        <Head title="Registrar Mantenimiento" />

        <div class="max-w-2xl mx-auto p-4 md:p-6">
            <Card class="border-t-4 border-t-red-600 shadow-lg">
                <CardHeader>
                    <div class="flex items-center gap-4">
                        <Button variant="ghost" size="icon" as-child>
                            <Link :href="route('assets.show', asset.id)">
                                <ArrowLeft class="h-4 w-4" />
                            </Link>
                        </Button>
                        <div>
                            <CardTitle>Registrar Mantenimiento / Reparación</CardTitle>
                            <CardDescription>
                                El activo pasará a estado "En Mantenimiento".
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <!-- Resumen del Activo -->
                    <div class="bg-red-50 border border-red-100 p-4 rounded-lg flex items-center gap-4 text-sm text-red-900">
                        <AlertOctagon class="h-5 w-5" />
                        <div>
                            <span class="font-bold">{{ asset.codigo_activo }}</span> - 
                            {{ asset.tipo_activo?.nombre }} {{ asset.marca?.nombre }}
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label>Tipo de Mantenimiento <span class="text-red-500">*</span></Label>
                                <Select v-model="form.tipo_mantenimiento_id">
                                    <SelectTrigger class="bg-background">
                                        <SelectValue placeholder="Seleccione Tipo" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="t in tipos" :key="t.id" :value="t.id.toString()">
                                            {{ t.nombre }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.tipo_mantenimiento_id" class="text-sm text-destructive">{{ form.errors.tipo_mantenimiento_id }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label>Proveedor (Servicio Técnico)</Label>
                                <Select v-model="form.proveedor_id">
                                    <SelectTrigger class="bg-background">
                                        <SelectValue placeholder="Seleccione Proveedor" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="p in proveedores" :key="p.id" :value="p.id.toString()">
                                            {{ p.nombre }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Fecha de Inicio <span class="text-red-500">*</span></Label>
                            <Input type="date" v-model="form.fecha_inicio" />
                        </div>

                        <div class="space-y-2">
                            <Label>Descripción del Problema / Trabajo a Realizar <span class="text-red-500">*</span></Label>
                            <Textarea 
                                v-model="form.hoja_trabajo" 
                                placeholder="Describa la falla detalladamente..." 
                                rows="5" 
                            />
                            <p v-if="form.errors.hoja_trabajo" class="text-sm text-destructive">{{ form.errors.hoja_trabajo }}</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <Button variant="outline" as-child>
                                <Link :href="route('assets.show', asset.id)">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="bg-red-600 hover:bg-red-700">
                                <Wrench class="mr-2 h-4 w-4" />
                                Registrar Evento
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
