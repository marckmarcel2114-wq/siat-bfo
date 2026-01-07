<script setup lang="ts">
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Save, Disc, Briefcase, Key, Calendar } from 'lucide-vue-next';

const props = defineProps<{
    proveedores: Array<any>;
    softwareCatalog: Array<any>;
}>();

const form = useForm({
    software_id: null, // NEW
    nombre: '',
    key: '',
    tipo: 'OEM',
    seats_total: 1,
    fecha_expiracion: '',
    proveedor_id: null,
    observaciones: ''
});

const submit = () => {
    form.post(route('software.store'));
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Software', href: route('software.index') }, { title: 'Registrar', href: '#' }]">
        <Head title="Registrar Software" />
        
        <div class="max-w-4xl mx-auto p-4 md:p-6 space-y-8">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full h-10 w-10">
                    <Link :href="route('software.index')">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight">Nueva Licencia</h1>
                    <p class="text-muted-foreground font-medium">Añada un nuevo software o paquete de licencias al inventario.</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Main Form Section -->
                <Card class="md:col-span-2 border-t-4 border-t-blue-500 shadow-xl overflow-hidden">
                    <CardHeader class="bg-slate-50/50 pb-6 border-b">
                        <CardTitle class="flex items-center gap-2">
                            <Disc class="h-5 w-5 text-blue-500" /> Especificaciones del Software
                        </CardTitle>
                        <CardDescription>Defina el nombre comercial y el tipo de licenciamiento.</CardDescription>
                    </CardHeader>
                    <CardContent class="pt-8 space-y-8">
                        <div class="space-y-3">
                            <Label for="nombre" class="text-xs font-bold uppercase text-muted-foreground tracking-tight">Software Vinculado</Label>
                             <Select v-model="form.software_id" @update:modelValue="(id) => { 
                                     const soft = props.softwareCatalog.find(s => s.id === id); 
                                     if(soft && !form.nombre) form.nombre = soft.nombre; 
                                 }">
                                <SelectTrigger class="h-12 border-slate-200 focus:ring-blue-500/20">
                                    <SelectValue placeholder="Seleccione del Catálogo..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="cat in softwareCatalog" :key="cat.id" :value="cat.id">{{ cat.nombre }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="space-y-3">
                            <Label for="nombre_licencia" class="text-xs font-bold uppercase text-muted-foreground tracking-tight">Nombre de la Licencia</Label>
                            <Input id="nombre_licencia" v-model="form.nombre" placeholder="Ej: Microsoft Office 2024 Professional Plus VL" required class="h-12 text-lg font-bold border-slate-200 focus:ring-blue-500/20 shadow-sm" />
                            <p class="text-xs text-slate-400">Descriptivo (ej: Contrato 2024)</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <Label class="text-xs font-bold uppercase text-muted-foreground tracking-tight flex items-center gap-1.5">
                                    <Key class="h-3 w-3" /> Tipo de Adquisición
                                </Label>
                                <Select v-model="form.tipo">
                                    <SelectTrigger class="h-11 border-slate-200 focus:ring-blue-500/20">
                                        <SelectValue placeholder="Seleccionar tipo" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="OEM">OEM (De Fábrica)</SelectItem>
                                        <SelectItem value="Volume">Contrato por Volumen</SelectItem>
                                        <SelectItem value="Subscription">Suscripción Periódica</SelectItem>
                                        <SelectItem value="Free">Gartuito / Libre</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-3">
                                <Label for="seats" class="text-xs font-bold uppercase text-muted-foreground tracking-tight flex items-center gap-1.5">
                                    <Users class="h-3 w-3" /> Capacidad (Asientos)
                                </Label>
                                <Input id="seats" type="number" min="1" v-model="form.seats_total" required class="h-11 border-slate-200 focus:ring-blue-500/20" />
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <Label for="key" class="text-xs font-bold uppercase text-muted-foreground tracking-tight">Clave de Producto (Opcional)</Label>
                            <Input id="key" v-model="form.key" placeholder="XXXXX-XXXXX-XXXXX-XXXXX-XXXXX" class="h-11 font-mono uppercase tracking-widest border-slate-200 focus:ring-blue-500/20" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Sidebar Sections -->
                <div class="space-y-6">
                    <Card class="shadow-md border-slate-100">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-[11px] font-bold uppercase text-muted-foreground">Logística y Prov.</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="space-y-2">
                                <Label for="fecha_expiracion" class="text-xs font-bold flex items-center gap-1.5">
                                    <Calendar class="h-3 w-3" /> Fecha Expiración
                                </Label>
                                <Input id="fecha_expiracion" type="date" v-model="form.fecha_expiracion" class="h-10 border-slate-200 focus:ring-blue-500/20" />
                            </div>

                            <div class="space-y-2">
                                <Label class="text-xs font-bold flex items-center gap-1.5">
                                    <Briefcase class="h-3 w-3" /> Proveedor
                                </Label>
                                <Select v-model="form.proveedor_id">
                                    <SelectTrigger class="h-10 border-slate-200 focus:ring-blue-500/20">
                                        <SelectValue placeholder="Opcional" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="prov in proveedores" :key="prov.id" :value="prov.id">
                                            {{ prov.nombre }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="shadow-md border-slate-100">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-[11px] font-bold uppercase text-muted-foreground">Notas Internas</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Textarea v-model="form.observaciones" placeholder="Documentación adicional, facturación, etc..." class="h-32 resize-none text-sm border-slate-200 focus:ring-blue-500/20" />
                        </CardContent>
                    </Card>

                    <Button type="submit" :disabled="form.processing" class="w-full h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-xl shadow-blue-500/30 transition-all active:scale-[0.98]">
                        <Save class="mr-2 h-5 w-5" /> Registrar Licencia
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
