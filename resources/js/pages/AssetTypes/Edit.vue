<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Save, Settings2 } from 'lucide-vue-next';
import { route } from 'ziggy-js';

const props = defineProps<{
    assetType: {
        id: number;
        name: string;
    };
}>();

const form = useForm({
    name: props.assetType.name,
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
                            <Label for="name" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Nombre del Tipo</Label>
                            <Input 
                                id="name" 
                                v-model="form.name" 
                                required 
                                class="h-11 bg-background/50 focus-visible:ring-primary shadow-sm"
                                placeholder="Ej. Laptop, Router, Switch..."
                            />
                            <p v-if="form.errors.name" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.name }}</p>
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
        </div>
    </AppLayout>
</template>
