<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Package, Plus, Search, Filter, MoreVertical, Edit, Trash2, 
    Layers, Monitor, Smartphone, Wrench, Disc, Settings
} from 'lucide-vue-next';

// UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';

const props = defineProps<{
    softwareList: Array<any>;
}>();

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Configuración', href: route('configs.index') },
    { title: 'Catálogo de Software', href: route('software-catalog.index') },
];

const searchQuery = ref('');
const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const editingSoftware = ref<any>(null);

const form = useForm({
    nombre: '',
    fabricante: '',
    tipo: 'Aplicación',
    descripcion: ''
});

const formEdit = useForm({
    nombre: '',
    fabricante: '',
    tipo: '',
    descripcion: ''
});

const filteredSoftware = computed(() => {
    if (!searchQuery.value) return props.softwareList;
    const lower = searchQuery.value.toLowerCase();
    return props.softwareList.filter(s => 
        s.nombre.toLowerCase().includes(lower) || 
        s.fabricante?.toLowerCase().includes(lower)
    );
});

const openCreate = () => {
    form.reset();
    showCreateDialog.value = true;
};

const createSoftware = () => {
    form.post(route('software-catalog.store'), {
        onSuccess: () => showCreateDialog.value = false
    });
};

const openEdit = (software: any) => {
    editingSoftware.value = software;
    formEdit.nombre = software.nombre;
    formEdit.fabricante = software.fabricante;
    formEdit.tipo = software.tipo;
    formEdit.descripcion = software.descripcion;
    showEditDialog.value = true;
};

const updateSoftware = () => {
    if (!editingSoftware.value) return;
    formEdit.put(route('software-catalog.update', editingSoftware.value.id), {
        onSuccess: () => showEditDialog.value = false
    });
};

const deleteSoftware = (id: number) => {
    if(confirm('¿Está seguro de eliminar este software? Se borrarán también todas sus versiones.')) {
        useForm({}).delete(route('software-catalog.destroy', id));
    }
};

const getIcon = (type: string) => {
    switch(type) {
        case 'Sistema Operativo': return Monitor;
        case 'Aplicación': return Package;
        case 'Suite': return Layers;
        case 'Utilidad': return Wrench;
        case 'Driver': return Disc;
        default: return Package;
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Catálogo de Software" />

        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Catálogo de Software</h1>
                    <p class="text-muted-foreground">Gestione las definiciones de software y sus versiones para estandarizar el inventario.</p>
                </div>
                <Button @click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-95 transition-all">
                    <Plus class="mr-2 h-4 w-4" /> Nuevo Software
                </Button>
            </div>

            <!-- Content -->
            <Card class="border-none shadow-md overflow-hidden bg-white">
                <CardHeader class="border-b bg-slate-50/50 pb-4">
                    <div class="flex items-center gap-4">
                        <div class="relative flex-1 max-w-sm">
                            <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input v-model="searchQuery" placeholder="Buscar software..." class="pl-9 bg-white" />
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader class="bg-slate-50">
                            <TableRow>
                                <TableHead class="w-[50px]"></TableHead>
                                <TableHead>Nombre del Software</TableHead>
                                <TableHead>Fabricante</TableHead>
                                <TableHead>Tipo</TableHead>
                                <TableHead class="text-center">Versiones</TableHead>
                                <TableHead class="text-right pr-6">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in filteredSoftware" :key="item.id" class="group hover:bg-slate-50/50 transition-colors">
                                <TableCell>
                                    <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                        <component :is="getIcon(item.tipo)" class="h-5 w-5" />
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Link :href="route('software-catalog.show', item.id)" class="text-base font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                        {{ item.nombre }}
                                    </Link>
                                    <p class="text-xs text-muted-foreground truncate max-w-[300px]">{{ item.descripcion }}</p>
                                </TableCell>
                                <TableCell>{{ item.fabricante || '---' }}</TableCell>
                                <TableCell>
                                    <Badge variant="outline" class="bg-white">{{ item.tipo }}</Badge>
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge variant="secondary" class="font-bold">{{ item.versions_count }}</Badge>
                                </TableCell>
                                <TableCell class="text-right pr-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <Button variant="ghost" size="icon" as-child class="h-8 w-8 text-slate-400 hover:text-emerald-600" title="Gestionar Versiones">
                                            <Link :href="route('software-catalog.show', item.id)">
                                                <Settings class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" size="icon" @click="openEdit(item)" class="h-8 w-8 text-slate-400 hover:text-blue-600" title="Editar Definición">
                                            <Edit class="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon" @click="deleteSoftware(item.id)" class="h-8 w-8 text-slate-400 hover:text-red-600" title="Eliminar Software">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="filteredSoftware.length === 0">
                                <TableCell colspan="6" class="h-32 text-center text-muted-foreground">
                                    No se encontraron resultados.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>

        <!-- Create Dialog -->
        <Dialog v-model:open="showCreateDialog">
            <DialogContent class="sm:max-w-[500px] rounded-2xl">
                <DialogHeader>
                    <DialogTitle>Registrar Nuevo Software</DialogTitle>
                    <DialogDescription>Añada una nueva definición de software al catálogo.</DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label>Nombre</Label>
                        <Input v-model="form.nombre" placeholder="Ej: Windows 11" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>Fabricante</Label>
                            <Input v-model="form.fabricante" placeholder="Ej: Microsoft" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Tipo</Label>
                            <Select v-model="form.tipo">
                                <SelectTrigger><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Sistema Operativo">Sistema Operativo</SelectItem>
                                    <SelectItem value="Aplicación">Aplicación</SelectItem>
                                    <SelectItem value="Suite">Suite / Office</SelectItem>
                                    <SelectItem value="Utilidad">Utilidad</SelectItem>
                                    <SelectItem value="Driver">Driver / Controlador</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label>Descripción</Label>
                        <Input v-model="form.descripcion" placeholder="Breve descripción..." />
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showCreateDialog = false">Cancelar</Button>
                    <Button @click="createSoftware" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700">Guardar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Edit Dialog -->
        <Dialog v-model:open="showEditDialog">
             <DialogContent class="sm:max-w-[500px] rounded-2xl">
                <DialogHeader>
                    <DialogTitle>Editar Software</DialogTitle>
                </DialogHeader>
                <div class="px-6 pt-2">
                    <div class="flex items-center gap-2 p-3 rounded-lg bg-blue-50 text-blue-700 text-xs">
                        <Settings class="h-4 w-4" />
                        <p>Para gestionar <strong>Versiones</strong>, use el botón de engranaje en la lista principal.</p>
                    </div>
                </div>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label>Nombre</Label>
                        <Input v-model="formEdit.nombre" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>Fabricante</Label>
                            <Input v-model="formEdit.fabricante" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Tipo</Label>
                            <Select v-model="formEdit.tipo">
                                <SelectTrigger><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Sistema Operativo">Sistema Operativo</SelectItem>
                                    <SelectItem value="Aplicación">Aplicación</SelectItem>
                                    <SelectItem value="Suite">Suite / Office</SelectItem>
                                    <SelectItem value="Utilidad">Utilidad</SelectItem>
                                    <SelectItem value="Driver">Driver / Controlador</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label>Descripción</Label>
                        <Input v-model="formEdit.descripcion" />
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showEditDialog = false">Cancelar</Button>
                    <Button @click="updateSoftware" :disabled="formEdit.processing" class="bg-blue-600 hover:bg-blue-700">Actualizar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>
