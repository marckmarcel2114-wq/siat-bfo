<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Save, Network, Wifi } from 'lucide-vue-next';

const props = defineProps<{
    asset: any;
    puntos_red: Array<any>;
}>();

const form = useForm({
    ip_address: props.asset.ip_address || '',
    mac_ethernet: props.asset.mac_ethernet || '',
    mac_wifi: props.asset.mac_wifi || '',
    punto_red_id: props.asset.punto_red_id?.toString() || '',
});

const submit = () => {
    form.put(route('assets.network.update', props.asset.id));
};
</script>

<template>
    <AppLayout :breadcrumbs="[
        { title: 'Inventario', href: route('assets.index') },
        { title: asset.codigo_activo, href: route('assets.show', asset.id) },
        { title: 'Configuración de Red', href: '#' }
    ]">
        <Head title="Configuración de Red" />

        <div class="max-w-4xl mx-auto p-4 md:p-6">
            <Card class="border-t-4 border-t-cyan-500 shadow-lg overflow-hidden">
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
                                <Network class="h-6 w-6 text-cyan-500" /> Conectividad y Red
                            </CardTitle>
                            <CardDescription class="text-base">
                                Configuración lógica y física de red para <strong>{{ asset.tipo_activo?.nombre }} {{ asset.codigo_activo }}</strong>.
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="mt-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Network Identity -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-2 pb-2 border-b">
                                    <div class="h-8 w-1 bg-cyan-500 rounded"></div>
                                    <h3 class="font-bold text-lg text-foreground">Identidad de Red</h3>
                                </div>

                                <div class="space-y-2">
                                    <Label class="text-xs font-bold text-muted-foreground uppercase">Dirección IP (IPv4)</Label>
                                    <div class="flex items-center pl-3 border border-muted-foreground/20 rounded-md bg-background focus-within:ring-2 focus-within:ring-cyan-500/20 transition-all">
                                        <Wifi class="h-4 w-4 text-muted-foreground mr-2" />
                                        <Input v-model="form.ip_address" placeholder="Ej. 192.168.1.100" class="border-0 focus-visible:ring-0 shadow-none h-10" />
                                    </div>
                                    <p v-if="form.errors.ip_address" class="text-xs font-medium text-destructive">{{ form.errors.ip_address }}</p>
                                </div>

                                <div class="space-y-2">
                                    <Label class="text-xs font-bold text-muted-foreground uppercase">Punto de Red (Físico)</Label>
                                    <Select v-model="form.punto_red_id">
                                        <SelectTrigger class="bg-background w-full h-10 border-muted-foreground/20 focus:ring-cyan-500/20 shadow-sm transition-all">
                                            <SelectValue placeholder="Seleccione Punto de Red" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">Sin conexión física</SelectItem>
                                            <SelectItem v-for="p in puntos_red" :key="p.id" :value="p.id.toString()">
                                                {{ p.descripcion }} ({{ p.ubicacion?.nombre || 'General' }})
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.punto_red_id" class="text-xs font-medium text-destructive">{{ form.errors.punto_red_id }}</p>
                                </div>
                            </div>

                            <!-- Hardware Connectivity -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-2 pb-2 border-b">
                                    <div class="h-8 w-1 bg-slate-400 rounded"></div>
                                    <h3 class="font-bold text-lg text-foreground">Direcciones Físicas (MAC)</h3>
                                </div>

                                <div class="space-y-2">
                                    <Label class="text-xs font-bold text-muted-foreground uppercase">MAC Ethernet</Label>
                                    <Input v-model="form.mac_ethernet" placeholder="XX:XX:XX:XX:XX:XX" class="font-mono uppercase bg-background h-10 border-muted-foreground/20 focus-ring-cyan-500/20 transition-all" />
                                    <p v-if="form.errors.mac_ethernet" class="text-xs font-medium text-destructive">{{ form.errors.mac_ethernet }}</p>
                                </div>

                                <div class="space-y-2">
                                    <Label class="text-xs font-bold text-muted-foreground uppercase">MAC Wi-Fi</Label>
                                    <Input v-model="form.mac_wifi" placeholder="XX:XX:XX:XX:XX:XX" class="font-mono uppercase bg-background h-10 border-muted-foreground/20 focus:ring-cyan-500/20 transition-all" />
                                    <p v-if="form.errors.mac_wifi" class="text-xs font-medium text-destructive">{{ form.errors.mac_wifi }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-8 border-t gap-3">
                            <Link 
                                :href="route('assets.show', asset.id)" 
                                class="inline-flex items-center justify-center px-6 h-10 rounded-full border border-muted-foreground/20 bg-background text-sm font-medium hover:bg-muted transition-colors shadow-sm"
                            >
                                Cancelar
                            </Link>
                            <Button type="submit" :disabled="form.processing" class="bg-cyan-600 hover:bg-cyan-700 text-white rounded-full px-8 shadow-md shadow-cyan-500/20 active:scale-95 transition-all h-10">
                                <Save class="mr-2 h-4 w-4" />
                                Guardar Configuración
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
