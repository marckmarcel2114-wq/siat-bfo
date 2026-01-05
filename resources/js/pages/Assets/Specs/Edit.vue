<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowLeft, Save, Monitor } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    asset: any;
    definitions: Array<any>;
    currentAttributes: Record<string, any>;
}>();

const form = useForm({
    atributos: { ...props.currentAttributes }
});

const submit = () => {
    form.put(route('assets.specs.update', props.asset.id));
};
</script>

<template>
    <AppLayout :breadcrumbs="[
        { title: 'Inventario', href: route('assets.index') },
        { title: asset.codigo_activo, href: route('assets.show', asset.id) },
        { title: 'Especificaciones Técnicas', href: '#' }
    ]">
        <Head title="Especificaciones Técnicas" />

        <div class="max-w-5xl mx-auto p-4 md:p-6">
            <Card class="border-t-4 border-t-blue-600 shadow-lg overflow-hidden">
                <CardHeader class="bg-muted/30 pb-6">
                    <div class="flex items-center gap-4">
                        <Button variant="ghost" size="icon" as-child class="rounded-full">
                            <Link :href="route('assets.show', asset.id)">
                                <ArrowLeft class="h-4 w-4" />
                            </Link>
                        </Button>
                        <div>
                            <CardTitle class="flex items-center gap-2 text-xl">
                                <Monitor class="h-6 w-6 text-blue-600" /> Especificaciones Técnicas (Hardware)
                            </CardTitle>
                            <CardDescription class="text-base">
                                Actualice las características físicas del equipo <strong>{{ asset.tipo_activo?.nombre }} {{ asset.codigo_activo }}</strong>.
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="mt-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <div v-if="definitions.length > 0">
                            <!-- Flat list with 2-column grid on desktop -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
                                <div v-for="def in definitions" :key="def.id" class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                    <!-- Label at 40% width on tablet/desktop -->
                                    <Label :for="'attr_' + def.id" class="text-sm font-semibold text-muted-foreground sm:w-[40%] flex-shrink-0">
                                        {{ def.nombre }}
                                    </Label>
                                    
                                    <!-- Input at 60% width -->
                                    <div class="flex-grow">
                                        <!-- Select Input -->
                                        <div v-if="def.tipo_dato === 'select'">
                                            <Select v-model="form.atributos[def.id]">
                                                <SelectTrigger :id="'attr_' + def.id" class="bg-background w-full h-10 transition-all border-muted-foreground/20 focus:border-blue-500">
                                                    <SelectValue :placeholder="'Seleccione ' + def.nombre" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="opt in def.opciones" :key="opt" :value="opt">
                                                        {{ opt }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        <!-- Boolean Input (Checkbox) -->
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
                                        
                                        <!-- Text/Number Input -->
                                        <Input 
                                            v-else 
                                            :id="'attr_' + def.id" 
                                            v-model="form.atributos[def.id]" 
                                            :type="def.tipo_dato === 'number' ? 'number' : 'text'"
                                            class="bg-background h-10 transition-all border-muted-foreground/20 focus:border-blue-500"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-20 bg-muted/20 rounded-xl border border-dashed text-muted-foreground">
                            <Monitor class="h-10 w-10 mx-auto mb-4 opacity-20" />
                            No hay especificaciones de hardware definidas para este tipo de activo.
                        </div>

                        <div class="flex justify-end pt-8 border-t gap-3">
                            <Button type="button" variant="outline" as-child>
                                <Link :href="route('assets.show', asset.id)">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700 shadow-md px-6">
                                <Save class="mr-2 h-4 w-4" />
                                Guardar Especificaciones
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
