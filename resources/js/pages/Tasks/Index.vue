<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Plus, ListChecks, Calendar, User, MapPin } from 'lucide-vue-next';

const props = defineProps<{
    createdTasks: Array<any>;
    assignedTasks: Array<any>;
}>();

const breadcrumbs = [
    { title: 'Inicio', href: '/dashboard' },
    { title: 'Tareas', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Gestión de Tareas" />

        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Gestión de Tareas</h1>
                    <p class="text-muted-foreground">Asignación y seguimiento de actividades operativas.</p>
                </div>
                <Button as-child>
                    <Link :href="route('tasks.create')">
                        <Plus class="mr-2 h-4 w-4" /> Nueva Tarea
                    </Link>
                </Button>
            </div>

            <Tabs default-value="created" class="w-full space-y-6">
                <TabsList>
                    <TabsTrigger value="created">Creadas por mí ({{ createdTasks.length }})</TabsTrigger>
                    <TabsTrigger value="assigned">Asignadas a mí ({{ assignedTasks.length }})</TabsTrigger>
                </TabsList>

                <!-- Tareas Creadas (Supervisor View) -->
                <TabsContent value="created">
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <Card v-for="task in createdTasks" :key="task.id" class="cursor-pointer hover:shadow-md transition-shadow" @click="$inertia.visit(route('tasks.show', task.id))">
                            <CardHeader>
                                <div class="flex justify-between items-start">
                                    <Badge variant="outline">{{ task.estado_tarea_id === 1 ? 'Activa' : 'Finalizada' }}</Badge>
                                    <span class="text-xs text-muted-foreground flex items-center">
                                        <Calendar class="h-3 w-3 mr-1" /> {{ new Date(task.fecha_limite).toLocaleDateString() }}
                                    </span>
                                </div>
                                <CardTitle class="mt-2 text-lg">{{ task.titulo }}</CardTitle>
                                <CardDescription class="line-clamp-2">{{ task.descripcion }}</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <span>{{ task.ejecuciones_count }} Asignaciones</span>
                                    <span>Ver detalles -></span>
                                </div>
                            </CardContent>
                        </Card>
                        <div v-if="createdTasks.length === 0" class="col-span-full text-center py-10 text-muted-foreground">
                            No has creado ninguna tarea.
                        </div>
                    </div>
                </TabsContent>

                <!-- Tareas Asignadas (Executor View) -->
                <TabsContent value="assigned">
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <Card v-for="exec in assignedTasks" :key="exec.id" class="border-l-4 border-l-blue-500 hover:shadow-md transition-shadow">
                            <CardHeader>
                                <div class="flex justify-between items-start">
                                    <Badge :variant="exec.estado_ejecucion_id === 1 ? 'secondary' : 'default'">
                                        {{ exec.estado_ejecucion_id === 1 ? 'Pendiente' : 'Completada' }}
                                    </Badge>
                                    <span class="text-xs text-red-600 font-medium flex items-center">
                                        Vence: {{ new Date(exec.tarea.fecha_limite).toLocaleDateString() }}
                                    </span>
                                </div>
                                <CardTitle class="mt-2">{{ exec.tarea.titulo }}</CardTitle>
                                <CardDescription>Por: {{ exec.tarea.supervisor?.name }}</CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-2">
                                <div v-if="exec.ubicacion" class="flex items-center gap-1 text-sm text-gray-600">
                                    <MapPin class="h-3 w-3" /> {{ exec.ubicacion.nombre }}
                                </div>
                                <Button size="sm" class="w-full mt-2" variant="outline" as-child>
                                     <!-- Link to Execution Detail specifically? Or just show task? -->
                                     <!-- Ideally we show the execution context. For now linking to task show -->
                                    <Link :href="route('tasks.show', exec.tarea_id)">Actualizar / Resolver</Link>
                                </Button>
                            </CardContent>
                        </Card>
                         <div v-if="assignedTasks.length === 0" class="col-span-full text-center py-10 text-muted-foreground">
                            No tienes tareas pendientes. ¡Buen trabajo!
                        </div>
                    </div>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
