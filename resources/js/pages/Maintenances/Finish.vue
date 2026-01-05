<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, CheckCircle, Wrench } from 'lucide-vue-next';

const props = defineProps<{
    asset: any;
    maintenance: any;
}>();

const form = useForm({
    estado_final_id: '1', // Default Operativo
    costo_bs: props.maintenance.costo_bs || 0,
    detalles_cierre: '' 
});

const submit = () => {
    form.put(route('maintenances.update', props.maintenance.id));
};
</script>

<template>
    <AppLayout :breadcrumbs="[
        { title: 'Inventario', href: route('assets.index') },
        { title: asset.codigo_activo, href: route('assets.show', asset.id) },
        { title: 'Finalizar Mantenimiento', href: '#' }
    ]">
        <Head title="Finalizar Mantenimiento" />

        <div class="max-w-2xl mx-auto p-4 md:p-6">
            <Card class="border-t-4 border-t-green-600 shadow-lg">
                <CardHeader>
                    <div class="flex items-center gap-4">
                        <Button variant="ghost" size="icon" as-child>
                            <Link :href="route('assets.show', asset.id)">
                                <ArrowLeft class="h-4 w-4" />
                            </Link>
                        </Button>
                        <div>
                            <CardTitle>Finalizar Mantenimiento</CardTitle>
                            <CardDescription>
                                Registrar costos finales y restaurar operatividad del activo.
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <!-- Resumen -->
                    <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg flex items-center gap-4 text-sm">
                        <Wrench class="h-5 w-5 text-gray-500" />
                        <div>
                            <div class="font-bold">Solicitud de Mantenimiento #{{ maintenance.id }}</div>
                            <div class="text-muted-foreground">{{ new Date(maintenance.fecha_inicio).toLocaleDateString() }} - {{ maintenance.proveedor?.nombre || 'Interno' }}</div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label>Costo Final (Bs) <span class="text-red-500">*</span></Label>
                                <Input type="number" step="0.01" v-model="form.costo_bs" />
                            </div>

                            <div class="space-y-2">
                                <Label>Estado Post-Mantenimiento</Label>
                                <Select v-model="form.estado_final_id">
                                    <SelectTrigger class="bg-background">
                                        <SelectValue placeholder="Estado" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">Operativo (Disponible)</SelectItem>
                                        <SelectItem value="3">Baja (Irreparable)</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Detalles del Servicio Realizado</Label>
                            <Textarea 
                                v-model="form.detalles_cierre" 
                                placeholder="Describa qué se reparó o cambió..." 
                                rows="5" 
                            />
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <Button variant="outline" as-child>
                                <Link :href="route('assets.show', asset.id)">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="bg-green-600 hover:bg-green-700">
                                <CheckCircle class="mr-2 h-4 w-4" />
                                Finalizar y Guardar
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
