<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    ArrowLeft, Plus, Edit, Trash2, Calendar, AlertCircle, Info 
} from 'lucide-vue-next';

// UI Components
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps<{
    software: any;
}>();

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Configuración', href: route('configs.index') },
    { title: 'Catálogo de Software', href: route('software-catalog.index') },
    { title: props.software.nombre, href: '#' },
];

const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const editingVersion = ref<any>(null);

const form = useForm({
    version: '',
    fecha_lanzamiento: '',
    eol_date: '',
    descripcion: ''
});

const formEdit = useForm({
    version: '',
    fecha_lanzamiento: '',
    eol_date: '',
    descripcion: ''
});

const openCreate = () => {
    form.reset();
    showCreateDialog.value = true;
};

const createVersion = () => {
    form.post(route('software-catalog.versions.store', props.software.id), {
        onSuccess: () => showCreateDialog.value = false
    });
};

const openEdit = (version: any) => {
    editingVersion.value = version;
    formEdit.version = version.version;
    formEdit.fecha_lanzamiento = version.fecha_lanzamiento;
    formEdit.eol_date = version.eol_date;
    formEdit.descripcion = version.descripcion;
    showEditDialog.value = true;
};

const updateVersion = () => {
    if (!editingVersion.value) return;
    formEdit.put(route('software-catalog.versions.update', editingVersion.value.id), {
        onSuccess: () => showEditDialog.value = false
    });
};

const deleteVersion = (id: number) => {
    if(confirm('¿Está seguro de eliminar esta versión?')) {
        useForm({}).delete(route('software-catalog.versions.destroy', id));
    }
};

const formatDate = (date: string) => {
    if (!date) return '---';
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Versiones: ${software.nombre}`" />

        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <Button variant="outline" size="icon" as-child>
                        <Link :href="route('software-catalog.index')"><ArrowLeft class="h-4 w-4" /></Link>
                    </Button>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold text-foreground">{{ software.nombre }}</h1>
                            <Badge variant="outline">{{ software.tipo }}</Badge>
                        </div>
                        <p class="text-muted-foreground">{{ software.fabricante }} - {{ software.descripcion || 'Sin descripción' }}</p>
                    </div>
                </div>
            </div>

            <!-- Versions Card -->
            <Card class="border-none shadow-md bg-white">
                <CardHeader class="border-b bg-slate-50/50 flex flex-row items-center justify-between pb-4">
                    <div>
                        <CardTitle>Versiones Registradas</CardTitle>
                        <CardDescription>Gestione las versiones disponibles para este título.</CardDescription>
                    </div>
                    <Button @click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white shadow-lg active:scale-95 transition-all">
                        <Plus class="mr-2 h-4 w-4" /> Registrar Versión
                    </Button>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader class="bg-slate-50">
                            <TableRow>
                                <TableHead class="w-[50px]"></TableHead>
                                <TableHead>Versión / Build</TableHead>
                                <TableHead>Fecha Lanzamiento</TableHead>
                                <TableHead>Fin de Soporte (EOL)</TableHead>
                                <TableHead>Descripción</TableHead>
                                <TableHead class="text-right pr-6">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="ver in software.versions" :key="ver.id" class="group hover:bg-slate-50/50 transition-colors">
                                <TableCell>
                                    <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                        <Info class="h-4 w-4" />
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <span class="font-bold text-base text-slate-800">{{ ver.version }}</span>
                                </TableCell>
                                <TableCell>
                                    <div v-if="ver.fecha_lanzamiento" class="flex items-center gap-2 text-slate-600">
                                        <Calendar class="h-3 w-3" /> {{ formatDate(ver.fecha_lanzamiento) }}
                                    </div>
                                    <span v-else class="text-slate-400">---</span>
                                </TableCell>
                                <TableCell>
                                    <Badge v-if="ver.eol_date" variant="outline" class="font-mono bg-red-50 text-red-700 border-red-200">
                                        {{ formatDate(ver.eol_date) }}
                                    </Badge>
                                    <span v-else class="text-slate-400">---</span>
                                </TableCell>
                                <TableCell class="max-w-[300px] truncate text-muted-foreground">{{ ver.descripcion || '---' }}</TableCell>
                                <TableCell class="text-right pr-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <Button variant="ghost" size="icon" @click="openEdit(ver)" class="h-8 w-8 text-slate-400 hover:text-blue-600">
                                            <Edit class="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon" @click="deleteVersion(ver.id)" class="h-8 w-8 text-slate-400 hover:text-red-600">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="!software.versions || software.versions.length === 0">
                                <TableCell colspan="6" class="h-32 text-center text-muted-foreground italic">
                                    No hay versiones registradas para este software.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>

        <!-- Create Version Dialog -->
        <Dialog v-model:open="showCreateDialog">
            <DialogContent class="sm:max-w-[450px] rounded-2xl">
                <DialogHeader>
                    <DialogTitle>Nueva Versión</DialogTitle>
                    <DialogDescription>Añada una versión a {{ software.nombre }}</DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label>Identificador de Versión</Label>
                        <Input v-model="form.version" placeholder="Ej: 24H2, 2.0.1, 2024" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>Lanzamiento</Label>
                            <Input type="date" v-model="form.fecha_lanzamiento" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Fin de Soporte (EOL)</Label>
                            <Input type="date" v-model="form.eol_date" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label>Notas de la Versión</Label>
                        <Textarea v-model="form.descripcion" placeholder="Características clave..." />
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showCreateDialog = false">Cancelar</Button>
                    <Button @click="createVersion" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700">Guardar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Edit Version Dialog -->
        <Dialog v-model:open="showEditDialog">
             <DialogContent class="sm:max-w-[450px] rounded-2xl">
                <DialogHeader>
                    <DialogTitle>Editar Versión</DialogTitle>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label>Identificador</Label>
                        <Input v-model="formEdit.version" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>Lanzamiento</Label>
                            <Input type="date" v-model="formEdit.fecha_lanzamiento" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Fin de Soporte (EOL)</Label>
                            <Input type="date" v-model="formEdit.eol_date" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label>Notas</Label>
                        <Textarea v-model="formEdit.descripcion" />
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showEditDialog = false">Cancelar</Button>
                    <Button @click="updateVersion" :disabled="formEdit.processing" class="bg-blue-600 hover:bg-blue-700">Actualizar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>
