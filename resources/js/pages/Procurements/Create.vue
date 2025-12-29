<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Save, Plus, Trash2 } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const form = useForm({
    justification: '',
    items: [
        { name: '', quantity: 1, specs: '' }
    ] // Start with one empty item
});

const addItem = () => {
    form.items.push({ name: '', quantity: 1, specs: '' });
};

const removeItem = (index: number) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const submit = () => {
    form.post(route('procurements.store'));
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Adquisiciones', href: route('procurements.index') },
    { title: 'Nueva Solicitud', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Nueva Solicitud de Adquisición" />

        <div class="max-w-4xl mx-auto p-4 md:p-6 space-y-6">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="route('procurements.index')">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Nueva Solicitud</h1>
                    <p class="text-muted-foreground">Solicita la compra de nuevos equipos o suministros.</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <Card class="border-border/50 shadow-sm">
                    <CardHeader>
                        <CardTitle>Justificación</CardTitle>
                        <CardDescription>Explica el motivo de esta solicitud.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Label for="justification" class="sr-only">Justificación</Label>
                        <Textarea 
                            id="justification" 
                            v-model="form.justification" 
                            placeholder="Ej. Reemplazo de equipos obsoletos para el área de Contabilidad..."
                            rows="4" 
                            required
                        />
                        <p v-if="form.errors.justification" class="text-sm text-destructive mt-1">{{ form.errors.justification }}</p>
                    </CardContent>
                </Card>

                <Card class="border-border/50 shadow-sm">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Ítems a Solicitar</CardTitle>
                            <Button type="button" variant="outline" size="sm" @click="addItem">
                                <Plus class="mr-2 h-3 w-3" /> Agregar Ítem
                            </Button>
                        </div>
                        <CardDescription>Detalla los bienes requeridos.</CardDescription>
                    </CardHeader>
                    <CardContent class="p-0">
                         <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-muted/50 text-muted-foreground">
                                    <tr>
                                        <th class="p-4 w-16 text-center">Cant.</th>
                                        <th class="p-4">Descripción del Bien</th>
                                        <th class="p-4">Especificaciones Técnicas / Referencia</th>
                                        <th class="p-4 w-12"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in form.items" :key="index" class="border-b border-border/50 last:border-0 hover:bg-muted/20">
                                        <td class="p-3 align-top">
                                            <Input type="number" min="1" v-model="item.quantity" class="text-center" required />
                                        </td>
                                        <td class="p-3 align-top">
                                            <Input v-model="item.name" placeholder="Ej. Laptop Core i7" required />
                                        </td>
                                        <td class="p-3 align-top">
                                            <Input v-model="item.specs" placeholder="RAM 16GB, SSD 512GB..." required />
                                        </td>
                                        <td class="p-3 align-top text-center">
                                            <Button 
                                                type="button" 
                                                variant="ghost" 
                                                size="icon" 
                                                class="text-muted-foreground hover:text-destructive"
                                                @click="removeItem(index)"
                                                :disabled="form.items.length === 1"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="form.errors['items']" class="p-4 text-sm text-destructive bg-destructive/10">
                             {{ form.errors['items'] }}
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-3 border-t border-border/50 px-6 py-4">
                        <Button variant="ghost" type="button" as-child>
                            <Link :href="route('procurements.index')">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing" class="bg-primary text-primary-foreground">
                            <Save class="mr-2 h-4 w-4" />
                            Enviar Solicitud
                        </Button>
                    </CardFooter>
                </Card>
            </form>
        </div>
    </AppLayout>
</template>
