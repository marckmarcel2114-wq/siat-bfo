<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Save, MapPin } from 'lucide-vue-next';
import { route } from 'ziggy-js';

const props = defineProps<{
    city: {
        id: number;
        nombre: string;
        codigo: string;
    }
}>();

const form = useForm({
    nombre: props.city.nombre,
    codigo: props.city.codigo,
});

const submit = () => {
    form.put(route('cities.update', props.city.id));
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Ubicaciones', href: '#' },
    { title: 'Ciudades', href: route('cities.index') },
    { title: 'Editar', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Editar Ciudad" />

        <div class="max-w-xl mx-auto p-4 md:p-8">
            <div class="mb-8">
                <Button variant="ghost" as-child class="-ml-2 mb-4 text-muted-foreground hover:text-foreground">
                    <Link :href="route('cities.index')">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver al listado
                    </Link>
                </Button>
                
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary shadow-sm border border-primary/20">
                        <MapPin class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Editar Ciudad</h1>
                        <p class="text-muted-foreground">Actualice la información de la ubicación seleccionada.</p>
                    </div>
                </div>
            </div>

            <Card class="border-border/60 shadow-xl shadow-primary/5 bg-card/50 backdrop-blur-sm overflow-hidden">
                <form @submit.prevent="submit">
                    <CardHeader class="border-b border-border/50 bg-muted/20 pb-6">
                        <CardTitle class="text-xl text-primary/80">Información General</CardTitle>
                        <CardDescription>Modifique el nombre o código según sea necesario.</CardDescription>
                    </CardHeader>
                    
                    <CardContent class="p-6 space-y-6">
                        <div class="space-y-2">
                            <Label for="nombre" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Nombre de la Ciudad</Label>
                            <Input 
                                id="nombre" 
                                v-model="form.nombre" 
                                required 
                                class="h-11 bg-background/50 focus-visible:ring-primary shadow-sm"
                                placeholder="Ej. Cochabamba"
                            />
                            <p v-if="form.errors.nombre" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.nombre }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="codigo" class="text-sm font-bold uppercase tracking-wider text-muted-foreground/80">Código Ref.</Label>
                            <Input 
                                id="codigo" 
                                v-model="form.codigo" 
                                required 
                                class="h-11 bg-background/50 focus-visible:ring-primary shadow-sm font-mono uppercase"
                                placeholder="Ej. CBBA"
                            />
                            <p v-if="form.errors.codigo" class="text-xs font-semibold text-destructive mt-1">{{ form.errors.codigo }}</p>
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-end gap-3 border-t border-border/50 bg-muted/10 px-6 py-4">
                        <Button variant="ghost" type="button" as-child class="hover:bg-background">
                            <Link :href="route('cities.index')">Cancelar</Link>
                        </Button>
                        <Button 
                            type="submit" 
                            :disabled="form.processing" 
                            class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-lg shadow-primary/20 transition-all active:scale-95 px-6"
                        >
                            <Save class="mr-2 h-4 w-4" />
                            Guardar Cambios
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
