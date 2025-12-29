<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Save, Settings2 } from 'lucide-vue-next';
import { route } from 'ziggy-js';

const props = defineProps<{
    branchType: {
        id: number;
        name: string;
        description: string;
        color: string;
        sort_order: number;
    }
}>();

const form = useForm({
    name: props.branchType.name,
    description: props.branchType.description || '',
    color: props.branchType.color || 'blue',
    sort_order: props.branchType.sort_order || 0,
});

const colors = [
    { name: 'Sky', value: 'sky', class: 'bg-sky-500' },
    { name: 'Emerald', value: 'emerald', class: 'bg-emerald-500' },
    { name: 'Orange', value: 'orange', class: 'bg-orange-500' },
    { name: 'Amber', value: 'amber', class: 'bg-amber-500' },
    { name: 'Rose', value: 'rose', class: 'bg-rose-500' },
    { name: 'Indigo', value: 'indigo', class: 'bg-indigo-500' },
    { name: 'Violet', value: 'violet', class: 'bg-violet-500' },
    { name: 'Blue', value: 'blue', class: 'bg-blue-500' },
];

const submit = () => {
    form.put(route('branch-types.update', props.branchType.id));
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Configuración', href: '#' },
    { title: 'Tipos de Sucursal', href: route('branch-types.index') },
    { title: 'Editar', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="'Editar ' + branchType.name" />

        <div class="max-w-2xl mx-auto p-4 md:p-8">
            <div class="mb-8">
                <Button variant="ghost" as-child class="-ml-2 mb-4 text-muted-foreground hover:text-foreground">
                    <Link :href="route('branch-types.index')">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver al listado
                    </Link>
                </Button>
                
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary shadow-sm border border-primary/20">
                        <Settings2 class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Editar Tipo</h1>
                        <p class="text-muted-foreground">Actualice la información de la categoría.</p>
                    </div>
                </div>
            </div>

            <Card class="border-border/60 shadow-xl shadow-primary/5 bg-card/50 backdrop-blur-sm overflow-hidden">
                <form @submit.prevent="submit">
                    <CardHeader class="border-b border-border/50 bg-muted/20 pb-6">
                        <CardTitle class="text-xl text-primary/80">Información de la Categoría</CardTitle>
                        <CardDescription>ID Interno: {{ branchType.id }}</CardDescription>
                    </CardHeader>
                    
                    <CardContent class="p-6 space-y-6">
                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Nombre del Tipo</Label>
                            <Input 
                                id="name" 
                                v-model="form.name" 
                                required 
                                class="h-11 bg-background/50 focus-visible:ring-primary shadow-sm"
                                placeholder="Ej. Agencia Rural, Punto Externo..."
                            />
                            <p v-if="form.errors.name" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Descripción (Opcional)</Label>
                            <Textarea 
                                id="description" 
                                v-model="form.description" 
                                class="min-h-[100px] bg-background/50 focus-visible:ring-primary shadow-sm resize-none"
                                placeholder="Breve descripción del propósito de este tipo..."
                            />
                            <p v-if="form.errors.description" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.description }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="sort_order" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Orden de Visualización (Peso)</Label>
                            <Input 
                                id="sort_order" 
                                type="number"
                                v-model="form.sort_order" 
                                required 
                                class="h-11 bg-background/50 focus-visible:ring-primary shadow-sm w-32"
                                placeholder="0"
                            />
                            <p class="text-[10px] text-muted-foreground italic">Valores menores aparecen primero (ej. 1, 2, 3).</p>
                            <p v-if="form.errors.sort_order" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.sort_order }}</p>
                        </div>

                        <div class="space-y-3">
                            <Label class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Color Temático</Label>
                            <div class="grid grid-cols-4 sm:grid-cols-8 gap-3">
                                <button 
                                    v-for="color in colors" 
                                    :key="color.value"
                                    type="button"
                                    @click="form.color = color.value"
                                    class="h-10 w-10 rounded-full flex items-center justify-center transition-all border-2"
                                    :class="[
                                        color.class,
                                        form.color === color.value ? 'border-foreground scale-110 shadow-lg' : 'border-transparent opacity-80 hover:opacity-100 hover:scale-105'
                                    ]"
                                    :title="color.name"
                                >
                                    <div v-if="form.color === color.value" class="h-2 w-2 rounded-full bg-white shadow-sm" />
                                </button>
                            </div>
                            <p class="text-[10px] text-muted-foreground italic">Este color se usará para representar esta categoría en todo el sistema.</p>
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-end gap-3 border-t border-border/50 bg-muted/10 px-6 py-4">
                        <Button variant="ghost" type="button" as-child class="hover:bg-background">
                            <Link :href="route('branch-types.index')">Cancelar</Link>
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
        </div>
    </AppLayout>
</template>
