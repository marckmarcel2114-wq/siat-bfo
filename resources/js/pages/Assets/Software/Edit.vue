<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowLeft, Save, Disc, Settings } from 'lucide-vue-next';

const props = defineProps<{
    asset: any;
    definitions: Array<any>;
    currentAttributes: Record<string, any>;
}>();

const form = useForm({
    atributos: { ...props.currentAttributes }
});

const submit = () => {
    form.put(route('assets.software.update', props.asset.id));
};
</script>

<template>
    <AppLayout :breadcrumbs="[
        { title: 'Inventario', href: route('assets.index') },
        { title: asset.codigo_activo, href: route('assets.show', asset.id) },
        { title: 'Software e Información Logica', href: '#' }
    ]">
        <Head title="Información de Software" />

        <div class="max-w-5xl mx-auto p-4 md:p-6">
            <Card class="border-t-4 border-t-blue-500 shadow-lg overflow-hidden">
                <CardHeader class="bg-muted/30 pb-6">
                    <div class="flex items-center gap-4">
                        <Link 
                            :href="route('assets.show', asset.id)" 
                            class="inline-flex items-center justify-center h-10 w-10 rounded-full text-muted-foreground hover:text-primary hover:bg-primary/10 transition-colors"
                        >
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                        <div>
                            <CardTitle class="flex items-center gap-2 text-xl">
                                <Disc class="h-6 w-6 text-blue-500" /> Información de Software y S.O.
                            </CardTitle>
                            <CardDescription class="text-base">
                                Detalle las versiones de software base y configuraciones lógicas para <strong>{{ asset.codigo_activo }}</strong>.
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="mt-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <div v-if="definitions.length > 0">
                            <div class="flex items-center gap-2 mb-6 pb-2 border-b">
                                <div class="h-6 w-1 bg-blue-500 rounded"></div>
                                <h3 class="font-bold text-lg text-foreground">Versiones y Aplicaciones Base</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
                                <div v-for="def in definitions" :key="def.id" class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                    <Label :for="'attr_' + def.id" class="text-sm font-semibold text-muted-foreground sm:w-[40%] flex-shrink-0 uppercase tracking-tight">
                                        {{ def.nombre }}
                                    </Label>
                                    
                                    <div class="flex-grow">
                                        <!-- Select Input -->
                                        <div v-if="def.tipo_dato === 'select'">
                                            <Select v-model="form.atributos[def.id]">
                                                <SelectTrigger :id="'attr_' + def.id" class="bg-background w-full h-10 border-muted-foreground/20 focus:ring-blue-500/20 shadow-sm transition-all">
                                                    <SelectValue :placeholder="'Seleccione ' + def.nombre" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="opt in def.opciones" :key="opt" :value="opt">
                                                        {{ opt }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        <!-- Boolean Input -->
                                        <div v-else-if="def.tipo_dato === 'boolean'" class="flex items-center space-x-3 h-10 border rounded px-3 bg-background border-muted-foreground/20">
                                            <Checkbox 
                                                :id="'attr_' + def.id" 
                                                :checked="form.atributos[def.id] === 'SI' || form.atributos[def.id] === true"
                                                @update:checked="(val) => form.atributos[def.id] = val ? 'SI' : 'NO'"
                                            />
                                            <Label :for="'attr_' + def.id" class="cursor-pointer text-sm font-medium w-full">
                                                {{ form.atributos[def.id] === 'SI' ? 'Sí' : 'No' }}
                                            </Label>
                                        </div>
                                        
                                        <!-- Generic Input -->
                                        <Input 
                                            v-else 
                                            :id="'attr_' + def.id" 
                                            v-model="form.atributos[def.id]" 
                                            :type="def.tipo_dato === 'number' ? 'number' : 'text'"
                                            class="bg-background h-10 border-muted-foreground/20 focus:ring-blue-500/20 shadow-sm transition-all"
                                            :placeholder="'Ingrese ' + def.nombre"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-20 bg-muted/20 rounded-xl border border-dashed text-muted-foreground">
                            <Settings class="h-10 w-10 mx-auto mb-4 opacity-20" />
                            No hay campos de software definidos para este tipo de activo.
                        </div>

                        <div class="flex justify-end pt-8 border-t gap-3">
                            <Link 
                                :href="route('assets.show', asset.id)" 
                                class="inline-flex items-center justify-center px-6 h-10 rounded-full border border-muted-foreground/20 bg-background text-sm font-medium hover:bg-muted transition-colors shadow-sm"
                            >
                                Cancelar
                            </Link>
                            <Button type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-8 shadow-md shadow-blue-500/20 active:scale-95 transition-all h-10">
                                <Save class="mr-2 h-4 w-4" />
                                Guardar Información
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
