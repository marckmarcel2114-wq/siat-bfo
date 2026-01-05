<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Save, ShieldAlert } from 'lucide-vue-next';
import { route } from 'ziggy-js';

const props = defineProps<{
    criticality: {
        id: number;
        nombre: string;
        nivel_numerico: number;
        color: string;
    };
}>();

const form = useForm({
    nombre: props.criticality.nombre,
    nivel_numerico: props.criticality.nivel_numerico?.toString(),
    color: props.criticality.color || '#6b7280',
});

const submit = () => {
    form.put(route('criticality.update', props.criticality.id));
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Configuración', href: '#' },
    { title: 'Criticidad', href: route('criticality.index') },
    { title: 'Editar', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Editar Criticidad" />

        <div class="max-w-2xl mx-auto p-4 md:p-8">
            <div class="mb-8">
                <Button variant="ghost" as-child class="-ml-2 mb-4 text-muted-foreground hover:text-foreground">
                    <Link :href="route('criticality.index')">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver al listado
                    </Link>
                </Button>
                
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary shadow-sm border border-primary/20">
                        <ShieldAlert class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Editar Nivel</h1>
                        <p class="text-muted-foreground">Actualice los parámetros de criticidad.</p>
                    </div>
                </div>
            </div>

            <Card class="border-border/60 shadow-xl shadow-primary/5 bg-card/50 backdrop-blur-sm overflow-hidden">
                <form @submit.prevent="submit">
                    <CardHeader class="border-b border-border/50 bg-muted/20 pb-6">
                        <CardTitle class="text-xl text-primary/80">Detalles de Criticidad</CardTitle>
                    </CardHeader>
                    
                    <CardContent class="p-6 space-y-6">
                         <div class="grid gap-6 md:grid-cols-2">
                             <div class="space-y-2">
                                <Label for="nivel">Nivel Numérico</Label>
                                <Input 
                                    id="nivel" 
                                    type="number"
                                    min="1"
                                    max="10"
                                    v-model="form.nivel_numerico" 
                                    class="h-11 bg-background/50 focus-visible:ring-primary shadow-sm"
                                    placeholder="Ej. 1"
                                />
                                <p v-if="form.errors.nivel_numerico" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.nivel_numerico }}</p>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="color">Color Representativo</Label>
                                <div class="flex gap-2">
                                    <Input 
                                        id="color" 
                                        type="color"
                                        v-model="form.color" 
                                        class="h-11 w-20 p-1 bg-background/50 cursor-pointer"
                                    />
                                    <Input 
                                        v-model="form.color" 
                                        class="h-11 bg-background/50 font-mono"
                                        placeholder="#000000"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="nombre">Nombre del Nivel <span class="text-destructive">*</span></Label>
                            <Input 
                                id="nombre" 
                                v-model="form.nombre" 
                                required 
                                class="h-11 bg-background/50 focus-visible:ring-primary shadow-sm"
                                placeholder="Ej. Crítico, Alto..."
                            />
                            <p v-if="form.errors.nombre" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.nombre }}</p>
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-end gap-3 border-t border-border/50 bg-muted/10 px-6 py-4">
                        <Button variant="ghost" type="button" as-child class="hover:bg-background">
                            <Link :href="route('criticality.index')">Cancelar</Link>
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
