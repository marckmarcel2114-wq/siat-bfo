<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { route } from 'ziggy-js';

const props = defineProps<{
    owner: {
        id: number;
        nombre: string;
    };
}>();

const form = useForm({
    nombre: props.owner.nombre,
});

const submit = () => {
    form.put(route('owners.update', props.owner.id));
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Propietarios', href: route('owners.index') }, { title: 'Editar', href: '#' }]">
        <Head title="Editar Propietario" />

        <div class="max-w-2xl mx-auto p-4 md:p-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-4">
                        <Button variant="ghost" size="icon" as-child>
                            <Link :href="route('owners.index')">
                                <ArrowLeft class="h-4 w-4" />
                            </Link>
                        </Button>
                        <div>
                            <CardTitle>Editar Propietario</CardTitle>
                            <CardDescription>Modifique los datos del propietario.</CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="nombre">Nombre</Label>
                            <Input id="nombre" v-model="form.nombre" placeholder="Ej: Banco Fortaleza, Outsourcing..." required />
                            <div v-if="form.errors.nombre" class="text-sm text-destructive">{{ form.errors.nombre }}</div>
                        </div>
                    </form>
                </CardContent>
                <CardFooter class="flex justify-end">
                    <Button @click="submit" :disabled="form.processing">
                        <Save class="mr-2 h-4 w-4" /> Actualizar
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template>
