<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Save, Settings2, Trash2, Plus, Edit2, Check, X } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    assetType: {
        id: number;
        nombre: string;
        definitions: Array<{
            id: number;
            nombre: string;
            tipo_dato: string;
            orden: number;
        }>;
    };
}>();

const newDefForm = useForm({
    nombre: '',
    tipo_dato: 'text',
    opciones_text: '', // To handle initial options
});

const submitDefinition = () => {
    // Parse options if it's a select
    const opcionesArray = newDefForm.tipo_dato === 'select' 
        ? newDefForm.opciones_text.split('\n').map(s => s.trim()).filter(s => s.length > 0)
        : [];

    newDefForm.transform((data) => ({
        ...data,
        opciones: opcionesArray
    })).post(route('asset-types.definitions.store', props.assetType.id), {
        onSuccess: () => {
            newDefForm.reset();
            // Optional: Show success toast if available
        },
    });
};

const deleteDefinition = (id: number) => {
    if (confirm('¿Eliminar este campo? Los valores asociados en los activos se mantendrán pero no serán visibles.')) {
        router.delete(route('asset-types.definitions.destroy', [props.assetType.id, id]));
    }
};

// Edit Definition Logic
const editingDefId = ref<number | null>(null);
const editDefForm = useForm({
    nombre: '',
    tipo_dato: 'text',
    opciones_text: '', // Helper for text area
});

const startEdit = (def: any) => {
    editingDefId.value = def.id;
    editDefForm.nombre = def.nombre;
    editDefForm.tipo_dato = def.tipo_dato;
    // Join options with newlines for editing
    editDefForm.opciones_text = def.opciones ? def.opciones.join('\n') : '';
};

const cancelEdit = () => {
    editingDefId.value = null;
    editDefForm.reset();
};

const updateDefinition = () => {
    if (!editingDefId.value) return;
    
    // Parse options from text area (one per line)
    const opcionesArray = editDefForm.opciones_text
        .split('\n')
        .map(s => s.trim())
        .filter(s => s.length > 0);

    router.put(route('asset-types.definitions.update', [props.assetType.id, editingDefId.value]), {
        nombre: editDefForm.nombre,
        tipo_dato: editDefForm.tipo_dato,
        opciones: opcionesArray
    }, {
        onSuccess: () => cancelEdit()
    });
};

const form = useForm({
    nombre: props.assetType.nombre,
});

const submit = () => {
    form.put(route('asset-types.update', props.assetType.id));
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Configuración', href: '#' },
    { title: 'Tipos de Activo', href: route('asset-types.index') },
    { title: 'Editar', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Editar Tipo de Activo" />

        <div class="max-w-2xl mx-auto p-4 md:p-8">
            <div class="mb-8">
                <Button variant="ghost" as-child class="-ml-2 mb-4 text-muted-foreground hover:text-foreground">
                    <Link :href="route('asset-types.index')">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver al listado
                    </Link>
                </Button>
                
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary shadow-sm border border-primary/20">
                        <Settings2 class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Editar Tipo de Activo</h1>
                        <p class="text-muted-foreground">Actualice el nombre de esta categoría de equipo.</p>
                    </div>
                </div>
            </div>

            <Card class="border-border/60 shadow-xl shadow-primary/5 bg-card/50 backdrop-blur-sm overflow-hidden">
                <form @submit.prevent="submit">
                    <CardHeader class="border-b border-border/50 bg-muted/20 pb-6">
                        <CardTitle class="text-xl text-primary/80">Información del Tipo</CardTitle>
                        <CardDescription>Asegúrese de que el nombre siga siendo único y descriptivo.</CardDescription>
                    </CardHeader>
                    
                    <CardContent class="p-6 space-y-6">
                        <div class="space-y-2">
                            <Label for="nombre" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Nombre del Tipo</Label>
                            <Input 
                                id="nombre" 
                                v-model="form.nombre" 
                                required 
                                class="h-11 bg-background/50 focus-visible:ring-primary shadow-sm"
                                placeholder="Ej. Laptop, Router, Switch..."
                            />
                            <p v-if="form.errors.nombre" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.nombre }}</p>
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-end gap-3 border-t border-border/50 bg-muted/10 px-6 py-4">
                        <Button variant="ghost" type="button" as-child class="hover:bg-background">
                            <Link :href="route('asset-types.index')">Cancelar</Link>
                        </Button>
                        <Button 
                            type="submit" 
                            :disabled="form.processing" 
                            class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-lg shadow-primary/20 transition-all active:scale-95 px-6"
                        >
                            <Save v-if="!form.processing" class="mr-2 h-4 w-4" />
                            <span v-else class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" />
                            {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                        </Button>
                    </CardFooter>
                </form>
            </Card>

            <!-- Attribute Definitions Section -->
            <div class="mt-8">
                <h2 class="text-lg font-bold text-foreground mb-4">Especificaciones Técnicas (Campos Personalizados)</h2>
                
                <div class="grid gap-8 lg:grid-cols-5">
                    <!-- List (LHS - 3 columns) -->
                    <Card class="lg:col-span-3 border-border/60 shadow-lg bg-card/30 backdrop-blur-sm overflow-hidden h-fit">
                        <CardHeader class="border-b border-border/40 bg-muted/30">
                            <CardTitle class="text-lg flex items-center gap-2">
                                <Plus class="h-5 w-5 text-primary" /> Campos Definidos
                            </CardTitle>
                            <CardDescription>Atributos que se solicitarán para este tipo de activo.</CardDescription>
                        </CardHeader>
                        <CardContent class="p-0">
                            <ul class="divide-y divide-border/40">
                                <li v-if="assetType.definitions.length === 0" class="p-12 text-center">
                                    <div class="inline-flex h-12 w-12 rounded-full bg-muted items-center justify-center mb-4">
                                        <Plus class="h-6 w-6 text-muted-foreground" />
                                    </div>
                                    <p class="text-sm text-muted-foreground italic">No hay campos personalizados definidos.</p>
                                </li>
                                <li v-for="def in assetType.definitions" :key="def.id" class="p-4 px-6 hover:bg-muted/20 transition-colors">
                                    
                                    <!-- View Mode -->
                                    <div v-if="editingDefId !== def.id" class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="h-10 w-10 rounded-lg bg-primary/5 border border-primary/10 flex items-center justify-center text-[10px] font-bold text-primary uppercase shadow-sm">
                                                {{ def.tipo_dato.substring(0,3) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-foreground">{{ def.nombre }}</p>
                                                <div class="flex items-center gap-3 mt-1">
                                                    <span class="text-[10px] font-medium uppercase tracking-wider text-muted-foreground/70">{{ def.tipo_dato === 'select' ? 'Lista Dinámica' : def.tipo_dato }}</span>
                                                    <span v-if="def.tipo_dato === 'select'" class="inline-flex items-center text-[10px] bg-blue-500/10 text-blue-600 px-2 py-0.5 rounded-full font-bold border border-blue-500/20">
                                                        {{ def.opciones ? def.opciones.length : 0 }} opciones
                                                    </span>
                                                    <span v-if="def.tipo_dato === 'boolean'" class="inline-flex items-center text-[10px] bg-green-500/10 text-green-600 px-2 py-0.5 rounded-full font-bold border border-green-500/20">
                                                        Si/No
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <Button variant="ghost" size="icon" class="h-9 w-9 rounded-full text-muted-foreground hover:text-primary hover:bg-primary/10" @click="startEdit(def)">
                                                <Edit2 class="h-4 w-4" />
                                            </Button>
                                            <Button variant="ghost" size="icon" class="h-9 w-9 rounded-full text-muted-foreground hover:text-destructive hover:bg-destructive/10" @click="deleteDefinition(def.id)">
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </div>

                                    <!-- Edit Mode -->
                                    <div v-else class="space-y-4 py-2 border-l-2 border-primary pl-4 -ml-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1.5">
                                                <Label class="text-[10px] uppercase font-bold text-muted-foreground/80">Nombre del Campo</Label>
                                                <Input v-model="editDefForm.nombre" class="h-9 bg-background focus:ring-1 focus:ring-primary" />
                                            </div>
                                            <div class="space-y-1.5">
                                                <Label class="text-[10px] uppercase font-bold text-muted-foreground/80">Tipo de Dato</Label>
                                                <div class="relative">
                                                    <select v-model="editDefForm.tipo_dato" class="w-full flex h-9 rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-primary appearance-none">
                                                        <option value="text">Texto Corto</option>
                                                        <option value="number">Número</option>
                                                        <option value="date">Fecha</option>
                                                        <option value="select">Lista (Select)</option>
                                                        <option value="boolean">Casilla (Si/No)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Options Editor (Only for Select) -->
                                        <div v-if="editDefForm.tipo_dato === 'select'" class="space-y-2 animate-in fade-in slide-in-from-top-2 duration-300">
                                            <Label class="text-[10px] uppercase font-bold text-muted-foreground/80">Opciones de la Lista (Una por línea)</Label>
                                            <Textarea 
                                                v-model="editDefForm.opciones_text" 
                                                class="min-h-[120px] font-mono text-xs bg-muted/20 border-border/60 focus:ring-1 focus:ring-primary" 
                                                placeholder="Memoria RAM 8 GB&#10;Memoria RAM 16 GB&#10;Tarj. Red Realtek PCIe..."
                                            />
                                            <p class="text-[10px] text-muted-foreground italic">Estas opciones aparecerán en la lista desplegable al editar un equipo.</p>
                                        </div>

                                        <div class="flex justify-end gap-2 pt-2">
                                            <Button variant="outline" size="sm" @click="cancelEdit" class="h-8 rounded-full border-muted-foreground/20 hover:bg-muted">
                                                <X class="mr-1.5 h-3.5 w-3.5" /> Cancelar
                                            </Button>
                                            <Button size="sm" class="h-8 rounded-full bg-primary hover:bg-primary/90 text-primary-foreground shadow-sm shadow-primary/20" @click="updateDefinition">
                                                <Check class="mr-1.5 h-3.5 w-3.5" /> Actualizar Campo
                                            </Button>
                                        </div>
                                    </div>

                                </li>
                            </ul>
                        </CardContent>
                    </Card>

                    <!-- Add New (RHS - 2 columns) -->
                    <Card class="lg:col-span-2 border-border/60 shadow-lg bg-card/30 backdrop-blur-sm h-fit">
                        <CardHeader class="border-b border-border/40 bg-primary/5">
                            <CardTitle class="text-base text-primary">Agregar Nuevo Atributo</CardTitle>
                            <CardDescription>Defina un nuevo campo de información técnica.</CardDescription>
                        </CardHeader>
                        <CardContent class="p-6">
                            <form @submit.prevent="submitDefinition" class="space-y-5">
                                <div class="space-y-2">
                                    <Label for="def_nombre" class="text-xs font-bold text-muted-foreground uppercase">Nombre del Campo</Label>
                                    <Input id="def_nombre" v-model="newDefForm.nombre" placeholder="Ej. Tarjeta de Red, RAM..." required class="h-10 bg-background/50 focus:ring-primary" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="def_type" class="text-xs font-bold text-muted-foreground uppercase">Tipo de Dato</Label>
                                    <select id="def_type" v-model="newDefForm.tipo_dato" class="w-full flex h-10 rounded-md border border-input bg-background/50 px-3 py-2 text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary ring-offset-background">
                                        <option value="text">Texto Corto</option>
                                        <option value="number">Número</option>
                                        <option value="date">Fecha</option>
                                        <option value="select">Lista Dinámica (Select)</option>
                                        <option value="boolean">Casilla (Si/No)</option>
                                    </select>
                                </div>

                                <!-- Dynamic Options for Select on Creation -->
                                <div v-if="newDefForm.tipo_dato === 'select'" class="space-y-2 animate-in fade-in zoom-in-95 duration-300">
                                    <Label class="text-xs font-bold text-muted-foreground uppercase">Opciones Iniciales (Una por línea)</Label>
                                    <Textarea 
                                        v-model="newDefForm.opciones_text" 
                                        class="min-h-[100px] font-mono text-xs bg-background/30" 
                                        placeholder="Memoria 8 GB&#10;Memoria 16 GB"
                                        required
                                    />
                                </div>

                                <Button type="submit" :disabled="newDefForm.processing" class="w-full h-10 rounded-xl bg-primary hover:bg-primary/90 text-primary-foreground font-bold shadow-lg shadow-primary/20 transition-all active:scale-95">
                                    <Plus v-if="!newDefForm.processing" class="mr-2 h-4 w-4" />
                                    <span v-else class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" />
                                    {{ newDefForm.processing ? 'Agregando...' : 'Agregar Al Catálogo' }}
                                </Button>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
