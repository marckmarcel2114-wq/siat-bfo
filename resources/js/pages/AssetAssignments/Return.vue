<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, CheckCircle, AlertTriangle } from 'lucide-vue-next';

const props = defineProps<{
    asset: any;
    assignment: any;
}>();

const form = useForm({
    observaciones: '',
    estado_final_id: '1' // Default to Operativo (1). Should be dynamic preferably or fixed IDs.
});

// Hardcoded for now, or pass from controller
const targetStatuses = [
    { id: '1', name: 'Operativo (Disponible)' },
    { id: '2', name: 'Mantenimiento' },
    { id: '3', name: 'Baja (Dañado)' },
];

const submit = () => {
    form.post(route('assignments.process_return', props.assignment.id));
};
</script>

<template>
    <AppLayout :breadcrumbs="[
        { title: 'Inventario', href: route('assets.index') },
        { title: asset.codigo_activo, href: route('assets.show', asset.id) },
        { title: 'Devolver', href: '#' }
    ]">
        <Head title="Devolución de Activo" />

        <div class="max-w-2xl mx-auto p-4 md:p-6">
            <Card class="border-t-4 border-t-orange-600 shadow-lg">
                <CardHeader>
                    <div class="flex items-center gap-4">
                        <Button variant="ghost" size="icon" as-child>
                            <Link :href="route('assets.show', asset.id)">
                                <ArrowLeft class="h-4 w-4" />
                            </Link>
                        </Button>
                        <div>
                            <CardTitle>Recepción de Activo (Devolución)</CardTitle>
                            <CardDescription>
                                Cerrar asignación actual y generar Acta de Devolución.
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <!-- Datos de la Asignación Actual -->
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg space-y-2">
                        <div class="flex items-center gap-2 text-yellow-800 font-bold">
                            <AlertTriangle class="h-4 w-4" /> Activo en Custodia
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-muted-foreground">Custodio:</span><br>
                                <strong>{{ assignment.usuario?.name }} {{ assignment.usuario?.lastname }}</strong>
                            </div>
                            <div>
                                <span class="text-muted-foreground">Fecha Asignación:</span><br>
                                {{ new Date(assignment.fecha_asignacion).toLocaleDateString() }}
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div class="space-y-2">
                            <Label>Estado de Recepción <span class="text-red-500">*</span></Label>
                            <Select v-model="form.estado_final_id">
                                <SelectTrigger class="bg-background h-10">
                                    <SelectValue placeholder="Estado post-devolución" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="s in targetStatuses" :key="s.id" :value="s.id">
                                        {{ s.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p class="text-xs text-muted-foreground">
                                Define si el activo queda disponible para otro usuario o pasa a mantenimiento.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Observaciones de Devolución</Label>
                            <Textarea 
                                v-model="form.observaciones" 
                                placeholder="Ej. Se recibe con pantalla sucia pero funcional. Falta cable de poder." 
                                rows="4" 
                            />
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <Button variant="outline" as-child>
                                <Link :href="route('assets.show', asset.id)">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="bg-orange-600 hover:bg-orange-700">
                                <CheckCircle class="mr-2 h-4 w-4" />
                                Confirmar Recepción
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
