<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Calendar, User, UploadCloud, MapPin, CheckCircle, FileText } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ref } from 'vue';

const props = defineProps<{
    task: {
        id: number;
        titulo: string;
        descripcion: string;
        fecha_limite: string;
        estado: { nombre: string };
        supervisor: { name: string };
        ejecuciones: Array<{
            id: number;
            admin_ciudad: { id: number, name: string };
            ubicacion: { nombre: string };
            estado: { id: number, nombre: string };
            observaciones: string;
            fecha_ejecucion: string;
            acta_ejecucion_path: string;
        }>;
    };
    auth: { user: { id: number } }; // We need this to check permission
}>();

const breadcrumbs = [
    { title: 'Tareas', href: '/tasks' },
    { title: props.task.titulo, href: '#' },
];

// Evidence Upload Logc
const selectedExecution = ref(null);
const evidenceForm = useForm({
    observaciones: '',
    estado_ejecucion_id: 2, // Default to 'Finalizado' (assuming ID 2)
    evidencia: null as File | null,
});

const openUploadModal = (exec: any) => {
    selectedExecution.value = exec;
    evidenceForm.observaciones = exec.observaciones || '';
    evidenceForm.estado_ejecucion_id = exec.estado?.id || 2;
    evidenceForm.evidencia = null;
};

const submitEvidence = () => {
    if (!selectedExecution.value) return;
    
    evidenceForm.post(route('tasks.execution.update', selectedExecution.value.id), {
        onSuccess: () => {
            // Close modal handled by UI state reset or page reload
            selectedExecution.value = null; 
            // In a real modal component we might need a v-model to close
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="task.titulo" />

        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b pb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <Badge variant="outline">{{ task.estado?.nombre || 'Activa' }}</Badge>
                        <span class="text-sm text-muted-foreground flex items-center">
                             <Calendar class="h-3 w-3 mr-1" /> Vence: {{ new Date(task.fecha_limite).toLocaleDateString() }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">{{ task.titulo }}</h1>
                    <p class="text-muted-foreground mt-1">Supervisado por: {{ task.supervisor?.name }}</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <!-- Description -->
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Instrucciones</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="whitespace-pre-wrap text-sm leading-relaxed text-gray-700 dark:text-gray-300">
                            {{ task.descripcion }}
                        </div>
                    </CardContent>
                </Card>

                <!-- Stats / Meta -->
                <Card>
                    <CardHeader>
                        <CardTitle>Estado de Ejecución</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                         <div class="flex justify-between items-center text-sm">
                            <span class="text-muted-foreground">Completados:</span>
                            <span class="font-bold text-green-600">
                                {{ task.ejecuciones.filter(e => e.estado?.nombre === 'Finalizado').length }} / {{ task.ejecuciones.length }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                :style="`width: ${(task.ejecuciones.filter(e => e.estado?.nombre === 'Finalizado').length / task.ejecuciones.length) * 100}%`">
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Executions Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Desglose por Responsable</CardTitle>
                    <CardDescription>Seguimiento individual de cada asignación.</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Responsable</TableHead>
                                <TableHead>Ubicación</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead>Evidencia</TableHead>
                                <TableHead class="text-right">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="exec in task.ejecuciones" :key="exec.id">
                                <TableCell class="font-medium">
                                    <div class="flex items-center gap-2">
                                        <User class="h-4 w-4 text-gray-400" />
                                        {{ exec.admin_ciudad?.name }}
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div v-if="exec.ubicacion" class="flex items-center gap-2 text-sm">
                                        <MapPin class="h-3 w-3 text-muted-foreground" />
                                        {{ exec.ubicacion.nombre }}
                                    </div>
                                    <span v-else class="text-muted-foreground italic">General</span>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="exec.estado?.nombre === 'Finalizado' ? 'default' : 'secondary'">
                                        {{ exec.estado?.nombre || 'Pendiente' }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <a v-if="exec.acta_ejecucion_path" :href="'/storage/' + exec.acta_ejecucion_path" target="_blank" class="flex items-center gap-1 text-sm text-blue-600 hover:underline">
                                        <FileText class="h-3 w-3" /> Ver Archivo
                                    </a>
                                    <span v-else class="text-xs text-muted-foreground">Pendiente</span>
                                </TableCell>
                                <TableCell class="text-right">
                                    <!-- Upload Dialog Trigger -->
                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button v-if="props.auth.user.id === exec.admin_ciudad?.id || props.auth.user.id === task.supervisor?.id" 
                                                variant="outline" size="sm" @click="openUploadModal(exec)">
                                                <UploadCloud class="h-3 w-3 mr-1" /> {{ exec.acta_ejecucion_path ? 'Actualizar' : 'Cargar' }}
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent class="sm:max-w-[425px]">
                                            <DialogHeader>
                                                <DialogTitle>Registrar Avance</DialogTitle>
                                                <DialogDescription>
                                                    Suba evidencia fotográfica o documental del trabajo realizado.
                                                </DialogDescription>
                                            </DialogHeader>
                                            <form @submit.prevent="submitEvidence" class="space-y-4 py-4">
                                                <div class="space-y-2">
                                                    <Label>Estado</Label>
                                                    <Select v-model="evidenceForm.estado_ejecucion_id"> <!-- Simplification: assuming value binds id -->
                                                        <!-- If Select returns string, we might need Number() logic or use HTML select for simplicity if Select component is complex -->
                                                        <!-- Using native select for reliability in this specific snippet if components are tricky, 
                                                             but let's try assuming standard shadcn Select behavior handling primitive values -->
                                                         <!-- Actually, to avoid type issues blindly, let's use a native select styled properly if we can't see Select implementation -->
                                                        <div class="relative">
                                                            <select v-model="evidenceForm.estado_ejecucion_id" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                                                <option :value="1">Pendiente / En Proceso</option>
                                                                <option :value="2">Finalizado</option>
                                                            </select>
                                                        </div>
                                                    </Select>
                                                </div>
                                                <div class="space-y-2">
                                                    <Label>Observaciones</Label>
                                                    <Textarea v-model="evidenceForm.observaciones" placeholder="Detalles de la ejecución..." />
                                                </div>
                                                 <div class="space-y-2">
                                                    <Label>Evidencia (Img/PDF)</Label>
                                                    <Input type="file" @change="e => evidenceForm.evidencia = e.target.files[0]" accept=".pdf,.jpg,.jpeg,.png" />
                                                </div>
                                                <DialogFooter>
                                                    <Button type="submit" :disabled="evidenceForm.processing">
                                                        Guardar Cambios
                                                    </Button>
                                                </DialogFooter>
                                            </form>
                                        </DialogContent>
                                    </Dialog>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

        </div>
    </AppLayout>
</template>
