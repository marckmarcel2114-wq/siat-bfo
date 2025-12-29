<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Save, Upload } from 'lucide-vue-next';

const props = defineProps<{
    asset: any;
    users: Array<{ id: number; name: string; position?: string }>; // Potential assignees
}>();

const form = useForm({
    asset_id: props.asset.id,
    user_id: '',
    assigned_at: new Date().toISOString().split('T')[0], // Today
    details: {},
    act_document: null as File | null,
    notes: '',
});

const submit = () => {
    form.post(route('asset-assignments.store'), {
        forceFormData: true,
    });
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Inventario', href: '#' },
    { title: 'Activos', href: route('assets.index') },
    { title: 'Detalle', href: route('assets.show', props.asset.id) },
    { title: 'Asignar', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Asignar Activo" />

        <div class="max-w-2xl mx-auto p-4 md:p-6 space-y-6">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="route('assets.show', asset.id)">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Asignar Activo</h1>
                    <p class="text-muted-foreground">Registrar entrega de equipo a un responsable.</p>
                </div>
            </div>

            <Card class="border-border/50 shadow-sm">
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Detalles de la Asignación</CardTitle>
                        <CardDescription>
                            Activo: <span class="font-medium text-primary">{{ asset.code_internal }}</span> ({{ asset.type?.name }})
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        
                        <div class="space-y-2">
                            <Label for="user">Funcionario Responsable *</Label>
                            <select 
                                id="user" 
                                v-model="form.user_id" 
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                                <option value="" disabled>Seleccione un usuario</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }} <span v-if="user.position">({{ user.position }})</span>
                                </option>
                            </select>
                            <p v-if="form.errors.user_id" class="text-sm text-destructive">{{ form.errors.user_id }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="date">Fecha de Entrega *</Label>
                            <Input id="date" type="date" v-model="form.assigned_at" required />
                            <p v-if="form.errors.assigned_at" class="text-sm text-destructive">{{ form.errors.assigned_at }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="file">Acta de Entrega (PDF)</Label>
                            <div class="flex items-center gap-2">
                                <Input 
                                    id="file" 
                                    type="file" 
                                    accept=".pdf"
                                    @input="form.act_document = ($event.target as HTMLInputElement).files?.[0] || null"
                                    class="cursor-pointer"
                                />
                            </div>
                            <p class="text-xs text-muted-foreground">Sube el acta firmada y escaneada.</p>
                            <p v-if="form.errors.act_document" class="text-sm text-destructive">{{ form.errors.act_document }}</p>
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full h-2 rounded-full overflow-hidden bg-muted [&::-webkit-progress-bar]:bg-muted [&::-webkit-progress-value]:bg-primary">
                                {{ form.progress.percentage }}%
                            </progress>
                        </div>

                        <div class="space-y-2">
                            <Label for="notes">Observaciones</Label>
                            <Textarea id="notes" v-model="form.notes" placeholder="Estado de entrega, accesorios, etc." />
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-3 border-t border-border/50 px-6 py-4">
                        <Button variant="ghost" type="button" as-child>
                            <Link :href="route('assets.show', asset.id)">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing" class="bg-primary text-primary-foreground">
                            <Save class="mr-2 h-4 w-4" />
                            Registrar Asignación
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
