<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Settings, Save, Mail, Globe } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    settings: Array<{
        id: number;
        key: string;
        value: string;
        description: string;
    }>;
}>();

const forms = ref(
    props.settings.map(s => ({
        id: s.id,
        form: useForm({
            value: s.value
        }),
        key: s.key,
        description: s.description
    }))
);

const submit = (index: number) => {
    const item = forms.value[index];
    item.form.put(route('configs.update', item.id), {
        preserveScroll: true,
        onSuccess: () => {
             // Success notification could be added here
        }
    });
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Configuración', href: '#' },
    { title: 'Ajustes del Sistema', href: route('configs.index') },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Ajustes del Sistema" />

        <div class="max-w-4xl mx-auto p-4 md:p-8">
            <div class="mb-8">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary shadow-sm border border-primary/20">
                        <Settings class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Ajustes del Sistema</h1>
                        <p class="text-muted-foreground">Gestiona las configuraciones globales de la plataforma.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <Card v-for="(item, index) in forms" :key="item.id" class="border-border/60 shadow-lg bg-card/50 backdrop-blur-sm">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <CardTitle class="text-lg flex items-center gap-2">
                                    <Globe v-if="item.key.includes('domain')" class="h-4 w-4 text-primary" />
                                    <Settings v-else class="h-4 w-4 text-primary" />
                                    {{ item.key.replace('_', ' ').toUpperCase() }}
                                </CardTitle>
                                <CardDescription>{{ item.description }}</CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit(index)" class="flex gap-4 items-end">
                            <div class="flex-1 space-y-2">
                                <Label :for="'setting-' + item.id">Valor</Label>
                                <div class="relative">
                                    <span v-if="item.key.includes('domain')" class="absolute left-3 top-2.5 text-muted-foreground">@</span>
                                    <Input 
                                        :id="'setting-' + item.id" 
                                        v-model="item.form.value" 
                                        :class="item.key.includes('domain') ? 'pl-8' : ''"
                                        placeholder="Ej. grupofortaleza.com.bo" 
                                    />
                                </div>
                                <p v-if="item.form.errors.value" class="text-xs text-destructive">{{ item.form.errors.value }}</p>
                            </div>
                            <Button type="submit" :disabled="item.form.processing" class="mb-0.5">
                                <Save v-if="!item.form.processing" class="mr-2 h-4 w-4" />
                                <span v-else class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-primary border-t-transparent"></span>
                                {{ item.form.processing ? 'Guardando...' : 'Guardar' }}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <div v-if="forms.length === 0" class="text-center py-12 border-2 border-dashed rounded-xl">
                    <p class="text-muted-foreground">No hay configuraciones disponibles.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
