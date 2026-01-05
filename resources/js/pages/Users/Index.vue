<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Search, Plus, Pencil, Trash2, Shield, MapPin, Mail, Phone, User as UserIcon, Filter, X, Eye } from 'lucide-vue-next';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    users: Array<{
        id: number;
        name: string;
        email: string;
        role: string;
        city?: { id: number; nombre: string };
        branch?: { id: number; nombre: string };
        job_title?: { name: string };
        position?: string;
        phone?: string;
        is_active: boolean;
        created_at: string;
        city_id?: number;
        branch_id?: number;
    }>;
    cities: Array<{ id: number; nombre: string }>;
    branches: Array<{ id: number; nombre: string; ciudad_id: number }>;
}>();

const searchTerm = ref('');
const selectedCity = ref<string>('all');
const selectedBranch = ref<string>('all');
const selectedRole = ref<string>('all');
const selectedStatus = ref<string>('all');

// Reset branch when city changes
watch(selectedCity, () => {
    selectedBranch.value = 'all';
});

const filteredBranches = computed(() => {
    if (selectedCity.value === 'all') return props.branches;
    return props.branches.filter(branch => branch.ciudad_id.toString() === selectedCity.value);
});

const filteredUsers = computed(() => {
    let result = props.users;

    // Filter by City
    if (selectedCity.value !== 'all') {
        result = result.filter(user => user.city_id?.toString() === selectedCity.value);
    }

    // Filter by Branch
    if (selectedBranch.value !== 'all') {
        result = result.filter(user => user.branch_id?.toString() === selectedBranch.value);
    }

    // Filter by Role
    if (selectedRole.value !== 'all') {
        result = result.filter(user => user.role === selectedRole.value);
    }

    // Filter by Status
    if (selectedStatus.value !== 'all') {
        const isActive = selectedStatus.value === 'active';
        result = result.filter(user => user.is_active === isActive);
    }

    // Search term
    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        result = result.filter(user => 
            user.name.toLowerCase().includes(term) ||
            user.email.toLowerCase().includes(term) ||
            user.role.toLowerCase().includes(term) ||
            (user.job_title?.name && user.job_title.name.toLowerCase().includes(term))
        );
    }

    return result;
});

const deleteUser = (user: any) => {
    if (confirm(`¿Estás seguro de eliminar al usuario "${user.name}"?`)) {
        router.delete(route('users.destroy', user.id));
    }
};

const getRoleBadgeClass = (role: string) => {
    switch(role) {
        case 'admin': return 'bg-purple-100 text-purple-800 border-purple-200';
        case 'city_admin': return 'bg-blue-100 text-blue-800 border-blue-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};

const getRoleLabel = (role: string) => {
    switch(role) {
        case 'admin': return 'Administrador Global';
        case 'city_admin': return 'Admin Regional';
        default: return 'Usuario';
    }
};

const resetFilters = () => {
    searchTerm.value = '';
    selectedCity.value = 'all';
    selectedBranch.value = 'all';
    selectedRole.value = 'all';
    selectedStatus.value = 'all';
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Usuarios', href: route('users.index') },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Gestión de Usuarios" />

        <div class="max-w-7xl mx-auto px-4 py-6">
            
            <!-- 🔝 Barra de Control -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
                <Button as-child class="bg-blue-700 hover:bg-blue-800 text-white shadow-sm">
                    <Link :href="route('users.create')">
                        <Plus class="mr-1 h-4 w-4" /> Nuevo Usuario
                    </Link>
                </Button>
            </div>

            <!-- 🔍 FILTROS JERÁRQUICOS -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    <!-- Filtro 1: Ciudad -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">CIUDAD</label>
                        <Select v-model="selectedCity">
                           <SelectTrigger class="h-8 text-xs w-full border-gray-300">
                               <SelectValue placeholder="Todas las ciudades" />
                           </SelectTrigger>
                           <SelectContent>
                               <SelectItem value="all">Todas las ciudades</SelectItem>
                               <SelectItem v-for="c in cities" :key="c.id" :value="c.id.toString()">{{ c.nombre }}</SelectItem>
                           </SelectContent>
                        </Select>
                    </div>

                    <!-- Filtro 2: Sucursal -->
                    <div>
                       <label class="block text-xs font-medium text-gray-700 mb-1">SUCURSAL</label>
                       <Select v-model="selectedBranch" :disabled="!selectedCity || selectedCity === 'all'">
                          <SelectTrigger class="h-8 text-xs w-full border-gray-300">
                              <SelectValue placeholder="Todas las sucursales" />
                          </SelectTrigger>
                          <SelectContent>
                              <SelectItem value="all">Todas las sucursales</SelectItem>
                              <SelectItem v-for="b in filteredBranches" :key="b.id" :value="b.id.toString()">{{ b.nombre }}</SelectItem>
                          </SelectContent>
                       </Select>
                   </div>

                   <!-- Filtro 3: Rol -->
                   <div>
                       <label class="block text-xs font-medium text-gray-700 mb-1">ROL</label>
                       <Select v-model="selectedRole">
                          <SelectTrigger class="h-8 text-xs w-full border-gray-300">
                              <SelectValue placeholder="Todos" />
                          </SelectTrigger>
                          <SelectContent>
                              <SelectItem value="all">Todos</SelectItem>
                              <SelectItem value="admin">Administrador Global</SelectItem>
                              <SelectItem value="city_admin">Admin Regional</SelectItem>
                              <SelectItem value="user">Usuario</SelectItem>
                          </SelectContent>
                       </Select>
                   </div>
                   
                   <!-- Filtro 4: Estado -->
                   <div>
                       <label class="block text-xs font-medium text-gray-700 mb-1">ESTADO</label>
                       <Select v-model="selectedStatus">
                          <SelectTrigger class="h-8 text-xs w-full border-gray-300">
                              <SelectValue placeholder="Todos" />
                          </SelectTrigger>
                          <SelectContent>
                              <SelectItem value="all">Todos</SelectItem>
                              <SelectItem value="active">Activo</SelectItem>
                              <SelectItem value="inactive">Inactivo</SelectItem>
                          </SelectContent>
                       </Select>
                   </div>

                   <!-- Filtro 5: Búsqueda Global -->
                   <div>
                       <label class="block text-xs font-medium text-gray-700 mb-1">BUSCAR</label>
                       <div class="relative">
                           <input 
                               v-model="searchTerm"
                               type="text" 
                               placeholder="Nombre, email..." 
                               class="w-full text-xs px-2 py-1.5 pl-8 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                           />
                           <Search class="absolute left-2 top-2 h-3 w-3 text-gray-400" />
                       </div>
                   </div>
                </div>
                
                <div v-if="searchTerm || selectedCity !== 'all' || selectedBranch !== 'all' || selectedRole !== 'all' || selectedStatus !== 'all'" class="mt-3 flex justify-end">
                     <Button variant="ghost" size="xs" @click="resetFilters" class="text-xs h-6 text-gray-500 hover:text-gray-700">
                        <X class="w-3 h-3 mr-1" /> Limpiar Filtros
                    </Button>
                </div>
            </div>

            <!-- 📋 TABLA DINÁMICA -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
               <div class="overflow-x-auto">
                   <table class="min-w-full divide-y divide-gray-200">
                       <thead class="bg-gray-50">
                           <tr>
                               <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                   <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xs">
                                       #
                                   </div>
                               </th>
                               <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                               <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol & Ubicación</th>
                               <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                               <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                           </tr>
                       </thead>
                       <tbody class="bg-white divide-y divide-gray-200">
                           <tr v-if="filteredUsers.length === 0">
                               <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">
                                   No se encontraron usuarios que coincidan con los filtros.
                               </td>
                           </tr>
                           <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 transition-colors group">
                               <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                    </div>
                               </td>
                               <td class="px-4 py-3">
                                   <div class="flex flex-col">
                                       <span class="font-medium text-sm text-gray-900">{{ user.last_name }}, {{ user.first_name }}</span>
                                       <span class="text-xs text-gray-500">{{ user.email }}</span>
                                       <span v-if="user.job_title?.name || user.position" class="text-[10px] text-gray-400 mt-0.5">
                                            {{ user.job_title?.name || user.position }}
                                       </span>
                                   </div>
                               </td>
                               <td class="px-4 py-3">
                                   <div class="flex flex-col gap-1">
                                       <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium border w-fit" :class="getRoleBadgeClass(user.role)">
                                           {{ getRoleLabel(user.role) }}
                                       </span>
                                       <div v-if="user.city" class="flex items-center text-[10px] text-gray-500">
                                           <MapPin class="w-3 h-3 mr-1 text-gray-400" />
                                           {{ user.city.nombre }}
                                           <span v-if="user.branch" class="mx-1 text-gray-300">|</span>
                                           <span v-if="user.branch" class="text-gray-500">{{ user.branch.nombre }}</span>
                                       </div>
                                   </div>
                               </td>
                               <td class="px-4 py-3">
                                   <span v-if="user.is_active" class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                       Activo
                                   </span>
                                   <span v-else class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                       Inactivo
                                   </span>
                               </td>
                               <td class="px-4 py-3 text-right text-sm">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900 p-1 hover:bg-indigo-50 rounded transition-colors" title="Editar">
                                            <Pencil class="h-4 w-4" />
                                        </Link>
                                        <button @click="deleteUser(user)" class="text-red-600 hover:text-red-900 p-1 hover:bg-red-50 rounded transition-colors" title="Eliminar">
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                               </td>
                           </tr>
                       </tbody>
                   </table>
               </div>
               
               <!-- Footer / Stats -->
               <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-xs text-gray-500">
                        Mostrando <span class="font-medium">{{ filteredUsers.length }}</span> de <span class="font-medium">{{ users.length }}</span> usuarios
                    </div>
               </div>
            </div>

        </div>
    </AppLayout>
</template>
