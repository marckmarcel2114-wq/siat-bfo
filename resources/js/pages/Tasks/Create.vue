<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Checkbox } from '@/components/ui/checkbox';
import { ref, computed } from 'vue';
import { ArrowLeft, Save, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    users: Array<any>;
    locations: Array<any>;
}>();

const form = useForm({
    titulo: '',
    descripcion: '',
    fecha_limite: '',
    assignments: [] as Array<{ user_id: number; ubicacion_id: number | null }>
});

// Helper for managing assignments
const addAssignment = () => {
    form.assignments.push({ user_id: props.users[0]?.id, ubicacion_id: null });
};

const removeAssignment = (index: number) => {
    form.assignments.splice(index, 1);
};

const submit = () => {
    form.post(route('tasks.store'));
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Tareas', href: route('tasks.index') }, { title: 'Nueva Tarea', href: '#' }]">
        <form @submit.prevent="submit" class="max-w-4xl mx-auto p-4 md:p-6 space-y-6">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link :href="route('tasks.index')"><ArrowLeft class="h-4 w-4" /></Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Nueva Tarea Operativa</h1>
                    <p class="text- muted-foreground">Asignar actividades a responsables regionales.</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <!-- General Info -->
                <Card class="md:col-span-1 h-fit">
                    <CardHeader>
                        <CardTitle>Detalles</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="titulo">Título</Label>
                            <Input id="titulo" v-model="form.titulo" placeholder="Ej: Inventario Q3 Beni" required />
                        </div>
                        <div class="space-y-2">
                            <Label for="fecha_limite">Fecha Límite</Label>
                            <Input id="fecha_limite" type="date" v-model="form.fecha_limite" required />
                        </div>
                         <div class="space-y-2">
                            <Label for="descripcion">Instrucciones</Label>
                            <Textarea id="descripcion" v-model="form.descripcion" rows="6" placeholder="Detalle los pasos a seguir..." required />
                        </div>
                    </CardContent>
                </Card>

                <!-- Assignments List -->
                <Card class="md:col-span-2">
                    <CardHeader class="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle>Asignaciones</CardTitle>
                            <CardDescription>Defina quién ejecutará la tarea y dónde.</CardDescription>
                        </div>
                        <Button type="button" variant="outline" size="sm" @click="addAssignment">
                            + Agregar Responsable
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <div v-if="form.assignments.length === 0" class="text-center py-8 border-2 border-dashed rounded-lg text-muted-foreground">
                            No hay asignaciones. Agregue al menos una.
                        </div>
                        
                        <div v-else class="space-y-4">
                            <div v-for="(assign, index) in form.assignments" :key="index" class="flex gap-4 items-end p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
                                <div class="flex-1 space-y-2">
                                    <Label>Responsable (Usuario)</Label>
                                    <Select v-model="assign.user_id">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Seleccionar usuario" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="user in users" :key="user.id" :value="user.id">
                                                {{ user.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="flex-1 space-y-2">
                                    <Label>Ubicación Objetivo (Opcional)</Label>
                                    <Select v-model="assign.ubicacion_id"> <!-- Use proper v-model binding for number/string handling if needed -->
                                        <!-- Note: Select value might need handling if null -->
                                        <SelectTrigger>
                                            <SelectValue placeholder="General / Sin Ubicación" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <!-- Assuming we pass locations flat or handle grouping -->
                                            <SelectItem :value="null">-- General --</SelectItem> 
                                            <SelectItem v-for="loc in locations" :key="loc.id" :value="loc.id">
                                                {{ loc.nombre }} ({{ loc.ciudad?.nombre }})
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <Button type="button" variant="destructive" size="icon" @click="removeAssignment(index)">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                        
                        <!-- Validation Error Display -->
                        <div v-if="form.errors.assignments" class="text-red-500 text-sm mt-2">
                            {{ form.errors.assignments }}
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="flex justify-end gap-2">
                <Button variant="outline" as-child>
                    <Link :href="route('tasks.index')">Cancelar</Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                    <Save class="mr-2 h-4 w-4" /> Crear y Asignar
                </Button>
            </div>
        </form>
    </AppLayout>
</template>
