<script setup lang="ts">
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Save, Disc, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    license: any;
    proveedores: Array<any>;
}>();

const form = useForm({
    nombre: props.license.nombre,
    key: props.license.key || '',
    tipo: props.license.tipo,
    seats_total: props.license.seats_total,
    fecha_expiracion: props.license.fecha_expiracion ? new Date(props.license.fecha_expiracion).toISOString().split('T')[0] : '',
    proveedor_id: props.license.proveedor_id,
    observaciones: props.license.observaciones || ''
});

const submit = () => {
    form.put(route('software.update', props.license.id));
};

const deleteLicense = () => {
    if (confirm('¿Está seguro de eliminar esta licencia? Esta acción no se puede deshacer si no hay instalaciones.')) {
        form.delete(route('software.destroy', props.license.id));
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="[
        { title: 'Software', href: route('software.index') },
        { title: license.nombre, href: route('software.show', license.id) },
        { title: 'Editar', href: '#' }
    ]">
        <Head :title="`Editar: ${license.nombre}`" />

        <div class="max-w-4xl mx-auto p-4 md:p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" as-child class="rounded-full h-10 w-10">
                        <Link :href="route('software.show', license.id)">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Editar Licencia</h1>
                        <p class="text-muted-foreground">Actualice los términos y detalles del software.</p>
                    </div>
                </div>
                <Button variant="ghost" size="icon" @click="deleteLicense" class="text-muted-foreground hover:text-red-600 hover:bg-red-50 rounded-full h-10 w-10 transition-colors">
                    <Trash2 class="h-5 w-5" />
                </Button>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left: Form Core -->
                <Card class="md:col-span-2 border-t-4 border-t-blue-500 shadow-lg">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Disc class="h-5 w-5 text-blue-500" /> Datos Principales
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-2">
                            <Label for="nombre" class="text-xs font-bold uppercase text-muted-foreground">Nombre del Software</Label>
                            <Input id="nombre" v-model="form.nombre" placeholder="Ej: Windows 11 Pro" required class="h-11 focus:ring-blue-500/20" />
                            <p v-if="form.errors.nombre" class="text-xs text-red-500 font-medium">{{ form.errors.nombre }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label class="text-xs font-bold uppercase text-muted-foreground">Tipo de Licencia</Label>
                                <Select v-model="form.tipo">
                                    <SelectTrigger class="h-11 focus:ring-blue-500/20">
                                        <SelectValue placeholder="Seleccionar tipo" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="OEM">OEM (Preinstalada)</SelectItem>
                                        <SelectItem value="Volume">Volumen / Corporativa</SelectItem>
                                        <SelectItem value="Subscription">Suscripción (Anual/Mensual)</SelectItem>
                                        <SelectItem value="Free">Gratuito / Open Source</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label for="seats" class="text-xs font-bold uppercase text-muted-foreground">Cantidad de Asientos</Label>
                                <Input id="seats" type="number" min="1" v-model="form.seats_total" required class="h-11 focus:ring-blue-500/20" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="key" class="text-xs font-bold uppercase text-muted-foreground">Clave de Producto / Serial</Label>
                            <Input id="key" v-model="form.key" placeholder="XXXXX-XXXXX-XXXXX-XXXXX-XXXXX" class="h-11 font-mono uppercase focus:ring-blue-500/20" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Right: Metadata -->
                <div class="space-y-6">
                    <Card class="shadow-md">
                        <CardHeader>
                            <CardTitle class="text-sm font-bold uppercase text-muted-foreground tracking-wider">Temporada y Origen</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="space-y-2">
                                <Label for="fecha_expiracion" class="text-xs font-bold uppercase">Fecha de Expiración</Label>
                                <Input id="fecha_expiracion" type="date" v-model="form.fecha_expiracion" class="h-10 focus:ring-blue-500/20" />
                                <p class="text-[10px] text-muted-foreground italic">Dejar vacío para licencias perpetuas.</p>
                            </div>

                            <div class="space-y-2">
                                <Label class="text-xs font-bold uppercase">Proveedor</Label>
                                <Select v-model="form.proveedor_id">
                                    <SelectTrigger class="h-10 focus:ring-blue-500/20">
                                        <SelectValue placeholder="Opcional" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem :value="null">Ninguno</SelectItem>
                                        <SelectItem v-for="prov in proveedores" :key="prov.id" :value="prov.id">
                                            {{ prov.nombre }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="shadow-md">
                        <CardHeader>
                            <CardTitle class="text-sm font-bold uppercase text-muted-foreground tracking-wider">Notas Internas</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Textarea v-model="form.observaciones" placeholder="Detalles de compra, contactos, etc..." class="h-32 resize-none text-sm" />
                        </CardContent>
                    </Card>

                    <Button type="submit" :disabled="form.processing" class="w-full h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg shadow-blue-500/20 transition-all active:scale-[0.98]">
                        <Save class="mr-2 h-5 w-5" /> Guardar Cambios
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
