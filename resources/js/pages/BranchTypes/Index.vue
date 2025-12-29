<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Settings2, Plus, Pencil, Trash2, Search, Layers } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import Pagination from '@/components/Pagination.vue';
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps<{
    branchTypes: {
        data: Array<{
            id: number;
            name: string;
            description: string;
            color: string;
            sort_order: number;
            branches_count: number;
        }>;
        links: any[];
    };
    filters: {
        search: string;
    };
}>();

const search = ref(props.filters.search || '');

watch(search, debounce((value: string) => {
    router.get(route('branch-types.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const deleteType = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este tipo? Solo podrá eliminarse si no tiene sucursales asociadas.')) {
        router.delete(route('branch-types.destroy', id));
    }
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Configuración', href: '#' },
    { title: 'Tipos de Sucursal', href: route('branch-types.index') },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Tipos de Sucursal" />

        <div class="flex flex-col gap-4 p-4 md:p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
                         <Settings2 class="h-6 w-6 text-primary" />
                         Tipos de Sucursal
                    </h1>
                    <p class="text-muted-foreground">Administre las categorías globales de establecimientos.</p>
                </div>
                <Button as-child class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-sm">
                    <Link :href="route('branch-types.create')">
                        <Plus class="mr-2 h-4 w-4" />
                        Nuevo Tipo
                    </Link>
                </Button>
            </div>

            <Card class="border-border/50 shadow-sm overflow-hidden">
                <CardHeader class="pb-3 border-b border-border/40 bg-muted/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Configuración de Categorías</CardTitle>
                            <CardDescription>Defina cómo se clasifican las ubicaciones en el sistema.</CardDescription>
                        </div>
                        <div class="relative w-64 max-w-sm hidden md:block">
                            <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input v-model="search" placeholder="Buscar tipo..." class="pl-9 h-9" />
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead class="bg-muted/40 text-muted-foreground font-medium border-b border-border/50">
                                <tr>
                                    <th class="h-10 px-4 align-middle font-semibold w-1/3">Nombre del Tipo</th>
                                    <th class="h-10 px-4 align-middle font-semibold text-center w-24">Peso</th>
                                    <th class="h-10 px-4 align-middle font-semibold">Descripción</th>
                                    <th class="h-10 px-4 align-middle font-semibold text-center w-32">Cantidad Total</th>
                                    <th class="h-10 px-4 align-middle text-right font-semibold pr-6 w-32">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="branchTypes.data.length === 0">
                                    <td colspan="5" class="p-12 text-center text-muted-foreground italic">
                                        No se encontraron categorías registradas.
                                    </td>
                                </tr>
                                <tr v-for="type in branchTypes.data" :key="type.id" class="border-b border-border/50 last:border-0 hover:bg-muted/30 transition-colors group/row">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div 
                                                class="h-8 w-8 rounded-lg flex items-center justify-center text-white group-hover/row:scale-110 transition-transform shadow-sm"
                                                :class="`bg-${type.color}-500`"
                                            >
                                                <Layers class="h-4 w-4" />
                                            </div>
                                            <span class="font-bold text-foreground">{{ type.name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <Badge variant="outline" class="font-mono text-xs">
                                            {{ type.sort_order }}
                                        </Badge>
                                    </td>
                                    <td class="p-4 text-muted-foreground italic">
                                        {{ type.description || 'Sin descripción' }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <Badge variant="secondary" class="font-mono px-3">
                                            {{ type.branches_count }}
                                        </Badge>
                                    </td>
                                    <td class="p-4 text-right pr-6">
                                        <div class="flex justify-end gap-1">
                                            <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-primary" as-child>
                                                <Link :href="route('branch-types.edit', type.id)">
                                                    <Pencil class="h-3.5 w-3.5" />
                                                </Link>
                                            </Button>
                                            <Button 
                                                variant="ghost" 
                                                size="icon" 
                                                class="h-8 w-8 text-muted-foreground hover:text-destructive" 
                                                @click="deleteType(type.id)"
                                                :disabled="type.name === 'ATM'"
                                            >
                                                <Trash2 class="h-3.5 w-3.5" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-border/50 bg-muted/5">
                        <Pagination :links="branchTypes.links" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
