<script setup lang="ts">
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, UserPlus, FileText, ShieldCheck, MonitorCheck, Search, MapPin, CheckCircle2, Building } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';

const props = defineProps<{
    asset: any;
    users: Array<any>;
    checklistDefinitions: Array<any>;
}>();

const form = useForm({
    activo_id: props.asset.id,
    usuario_id: '',
    observaciones: '',
    ubicacion_destino_id: '',
    details: {} // Checklist answers
});

// --- Search & Selection Logic ---
const userSearch = ref('');

// Sort users: Recommended first, then by name
const sortedUsers = computed(() => {
    const search = userSearch.value.toLowerCase();
    const assetLocId = props.asset.ubicacion_id;

    return props.users.filter(u => {
        if (!search) return true;
        return (
            u.name.toLowerCase().includes(search) || 
            u.lastname?.toLowerCase().includes(search) ||
            u.cargo?.toLowerCase().includes(search)
        );
    }).sort((a, b) => {
        // Prioritize current branch
        const aMatch = a.ubicacion_id === assetLocId;
        const bMatch = b.ubicacion_id === assetLocId;
        if (aMatch && !bMatch) return -1;
        if (!aMatch && bMatch) return 1;
        // Then by name
        return a.name.localeCompare(b.name);
    });
});

const selectUser = (userId: number) => {
    form.usuario_id = userId.toString();
};

const getSelectedUser = computed(() => {
    return props.users.find(u => u.id.toString() === form.usuario_id);
});

const submit = () => {
    form.post(route('assignments.store'));
};
</script>

<template>
    <AppLayout :breadcrumbs="[
        { title: 'Inventario', href: route('assets.index') },
        { title: asset.codigo_activo, href: route('assets.show', asset.id) },
        { title: 'Asignar', href: '#' }
    ]">
        <Head title="Asignar Activo" />

        <div class="max-w-3xl mx-auto p-4 md:p-6">
            <Card class="border-t-4 border-t-blue-600 shadow-lg">
                <CardHeader>
                    <div class="flex items-center gap-4">
                        <Button variant="ghost" size="icon" as-child>
                            <Link :href="route('assets.show', asset.id)">
                                <ArrowLeft class="h-4 w-4" />
                            </Link>
                        </Button>
                        <div>
                            <CardTitle>Asignación de Activo</CardTitle>
                            <CardDescription>
                                Seleccione el custodio para generar el Acta de Entrega.
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <!-- Resumen del Activo -->
                     <div class="bg-blue-50/50 border border-blue-100 p-4 rounded-lg flex flex-col gap-2 text-sm">
                         <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Activo a entregar</span>
                                <div class="font-bold text-gray-900 text-lg">{{ asset.codigo_activo }}</div>
                            </div>
                            <div class="text-left sm:text-right" v-if="asset.ubicacion">
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider block">Ubicación Actual</span>
                                <Badge variant="secondary" class="mt-0.5 bg-white border-blue-200 text-blue-700">
                                    <MapPin class="h-3 w-3 mr-1" />
                                    {{ asset.ubicacion.ciudad?.nombre }} - {{ asset.ubicacion.nombre }}
                                </Badge>
                            </div>
                        </div>
                        <div class="flex gap-4 text-gray-600 border-t border-blue-100 pt-2 mt-1">
                             <span class="flex items-center gap-1"><span class="font-semibold">Tipo:</span> {{ asset.tipo_activo?.nombre }}</span>
                             <span class="flex items-center gap-1"><span class="font-semibold">Modelo:</span> {{ asset.modelo?.marca?.nombre }} {{ asset.modelo?.nombre }}</span>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- NEW SELECTION TABLE -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-end">
                                <Label class="text-sm font-bold text-gray-700">Buscar Funcionario (Custodio) <span class="text-red-500">*</span></Label>
                                <div v-if="getSelectedUser" class="text-[11px] text-green-600 font-medium flex items-center gap-1 bg-green-50 px-2 py-0.5 rounded border border-green-100 animate-in fade-in zoom-in duration-300">
                                    <CheckCircle2 class="h-3 w-3" /> Seleccionado: {{ getSelectedUser.name }} {{ getSelectedUser.lastname }}
                                </div>
                            </div>
                            
                            <!-- Search Bar -->
                            <div class="relative">
                                <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-400" />
                                <Input 
                                    v-model="userSearch" 
                                    placeholder="Escriba apellido, nombre o cargo para filtrar..." 
                                    class="pl-9 h-10 border-gray-300 focus:ring-blue-500 shadow-sm"
                                />
                            </div>

                            <!-- Scrollable Table -->
                            <div class="border rounded-md overflow-hidden bg-white shadow-sm border-gray-200">
                                <div class="max-h-64 overflow-y-auto overflow-x-auto scrollbar-thin">
                                    <table class="w-full text-xs text-left border-collapse min-w-[500px] sm:min-w-0">
                                        <thead class="bg-gray-50 sticky top-0 z-10 border-b shadow-sm">
                                            <tr>
                                                <th class="p-2.5 font-bold text-gray-600 uppercase tracking-tight w-8"></th>
                                                <th class="p-2.5 font-bold text-gray-600 uppercase tracking-tight">Nombre y Apellido</th>
                                                <th class="p-2.5 font-bold text-gray-600 uppercase tracking-tight">Cargo / Posición</th>
                                                <th class="p-2.5 font-bold text-gray-600 uppercase tracking-tight">Sede / Agencia</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr 
                                                v-for="u in sortedUsers" 
                                                :key="u.id" 
                                                @click="selectUser(u.id)"
                                                class="cursor-pointer transition-colors border-b last:border-0"
                                                :class="[
                                                    form.usuario_id === u.id.toString() ? 'bg-blue-600 text-white hover:bg-blue-700' : 'hover:bg-gray-50',
                                                    u.ubicacion_id === asset.ubicacion_id && form.usuario_id !== u.id.toString() ? 'bg-amber-50/50' : ''
                                                ]"
                                            >
                                                <td class="p-2.5 text-center">
                                                    <div class="flex items-center justify-center">
                                                        <div 
                                                            class="w-4 h-4 rounded-full border flex items-center justify-center"
                                                            :class="form.usuario_id === u.id.toString() ? 'border-white bg-white' : 'border-gray-300 bg-white'"
                                                        >
                                                            <div v-if="form.usuario_id === u.id.toString()" class="w-2 h-2 rounded-full bg-blue-600"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="p-2.5">
                                                    <div class="flex flex-col">
                                                        <span class="font-bold uppercase">{{ u.name }} {{ u.lastname }}</span>
                                                        <span class="text-[10px]" :class="form.usuario_id === u.id.toString() ? 'text-blue-100' : 'text-gray-400'">{{ u.email }}</span>
                                                    </div>
                                                </td>
                                                <td class="p-2.5 font-medium">{{ u.cargo || u.position || '-' }}</td>
                                                <td class="p-2.5">
                                                    <div class="flex items-center gap-1.5 overflow-hidden">
                                                        <Building class="h-3 w-3 shrink-0" v-if="u.ubicacion_id === asset.ubicacion_id" />
                                                        <span class="truncate" :title="u.ubicacion?.nombre">{{ u.ubicacion?.nombre || 'Central' }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="sortedUsers.length === 0">
                                                <td colspan="4" class="p-8 text-center text-gray-400 italic">
                                                    No se encontraron funcionarios con "{{ userSearch }}"
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <p v-if="form.errors.usuario_id" class="text-sm text-destructive font-medium">{{ form.errors.usuario_id }}</p>
                        </div>

                        <!-- Checklists Section (Legacy - usually empty) -->
                        <div v-if="checklistDefinitions && checklistDefinitions.length > 0" class="border p-4 rounded-md bg-slate-50">
                            <h3 class="font-medium flex items-center gap-2 mb-4 text-slate-800">
                                <ShieldCheck class="h-4 w-4" /> Checklist de Entrega
                            </h3>
                            <!-- ... existing checklist items ... -->
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <Label class="font-bold text-gray-700">Observaciones Generales</Label>
                                <span class="text-[10px] text-gray-400">Ej. "Incluye cargador, mouse, sin daños físicos"</span>
                            </div>
                            <Textarea 
                                v-model="form.observaciones" 
                                placeholder="Describa el estado del equipo al momento de la entrega..." 
                                rows="4" 
                                class="bg-gray-50/30 focus:bg-white transition-colors"
                            />
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t">
                            <Button variant="outline" as-child class="px-6 h-10">
                                <Link :href="route('assets.show', asset.id)">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing || !form.usuario_id" class="px-8 h-10 bg-blue-700 hover:bg-blue-800 shadow-md">
                                <UserPlus class="mr-2 h-4 w-4" />
                                Confirmar y Generar Acta
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: #f8fafc;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
