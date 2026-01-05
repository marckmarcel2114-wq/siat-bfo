<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { User, ArrowLeft, Save, Briefcase, Shield, Building, Phone, Mail, UserPlus, Plus } from 'lucide-vue-next';
import JobTitleQuickAdd from '@/components/QuickAdd/JobTitleQuickAdd.vue';

const props = defineProps<{
    cities: Array<{ id: number; nombre: string }>;
    branches: Array<{ id: number; nombre: string; ciudad_id: number }>;
    roles: Array<string>;
    jobTitles: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    first_name: '',
    last_name: '',
    username: '',
    email: '',
    password: '',
    role: 'user',
    city_id: '',
    branch_id: '',
    job_title_id: '',
    job_title_new_name: '', // For dynamic creation
    position: '', // Legacy fallback
    phone: '',
    is_active: true,
    hire_date: '',
});

const emailDomain = computed(() => usePage().props.emailDomain as string || 'grupofortaleza.com.bo');

// Auto-fill email from username
watch(() => form.username, (newUsername) => {
    const domain = emailDomain.value;
    const oldDomain = 'fortaleza.com.bo';
    
    // Overwrite if empty, doesn't have @, or uses current/old domain
    if (newUsername && (
        !form.email || 
        !form.email.includes('@') || 
        form.email.endsWith(`@${domain}`) || 
        form.email.endsWith(`@${oldDomain}`)
    )) {
        form.email = `${newUsername.toLowerCase()}@${domain}`;
    }
});

// Filter branches based on selected city
const filteredBranches = computed(() => {
    if (!form.city_id) return [];
    return props.branches.filter(branch => branch.ciudad_id.toString() === form.city_id.toString());
});

// Dynamic Job Title Logic
const isJobTitleQuickAddOpen = ref(false);

const handleJobTitleCreated = (newTitle: any) => {
    // Add to local list if not present (although usually we reload props, but for UX speed)
    // Actually, JobTitleQuickAdd emits 'created'.
    // We should reload the page props or just push to local list.
    // Ideally we reload only jobTitles.
    router.reload({ only: ['jobTitles'] });
    form.job_title_id = newTitle.id.toString();
};

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AppLayout title="Crear Usuario">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <Button variant="outline" size="icon" as-child>
                            <Link :href="route('users.index')">
                                <ArrowLeft class="w-4 h-4" />
                            </Link>
                        </Button>
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-foreground">Crear Nuevo Usuario</h2>
                            <p class="text-muted-foreground">Registra un nuevo colaborador en la plataforma.</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- Left Column: Identity & Access -->
                        <div class="md:col-span-2 space-y-6">
                            <!-- Personal Info Card -->
                            <Card>
                                <CardHeader>
                                    <div class="flex items-center gap-2">
                                        <div class="p-2 bg-primary/10 rounded-lg">
                                            <User class="w-5 h-5 text-primary" />
                                        </div>
                                        <div>
                                            <CardTitle>Información Personal</CardTitle>
                                            <CardDescription>Datos básicos del usuario</CardDescription>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label for="first_name">Nombre</Label>
                                        <Input id="first_name" v-model="form.first_name" required placeholder="Ej. Juan" />
                                        <p v-if="form.errors.first_name" class="text-xs text-destructive">{{ form.errors.first_name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="last_name">Apellido</Label>
                                        <Input id="last_name" v-model="form.last_name" required placeholder="Ej. Pérez" />
                                        <p v-if="form.errors.last_name" class="text-xs text-destructive">{{ form.errors.last_name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="phone">Teléfono</Label>
                                        <Input id="phone" v-model="form.phone" placeholder="Ej. 70012345" autocomplete="off" />
                                        <p v-if="form.errors.phone" class="text-xs text-destructive">{{ form.errors.phone }}</p>
                                    </div>
                                </CardContent>
                            </Card>

                            <!-- Access Credentials Card -->
                            <Card>
                                <CardHeader>
                                    <div class="flex items-center gap-2">
                                        <div class="p-2 bg-indigo-100 rounded-lg dark:bg-indigo-900/20">
                                            <Shield class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                                        </div>
                                        <div>
                                            <CardTitle>Credenciales de Acceso</CardTitle>
                                            <CardDescription>Login y seguridad</CardDescription>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                     <div class="space-y-2">
                                        <Label for="username">Nombre de Usuario</Label>
                                        <Input id="username" v-model="form.username" required placeholder="Ej. jperez" autocomplete="off" />
                                        <p v-if="form.errors.username" class="text-xs text-destructive">{{ form.errors.username }}</p>
                                    </div>
                                     <div class="space-y-2">
                                        <Label for="email">Correo Electrónico</Label>
                                        <div class="relative">
                                            <Mail class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                                            <Input id="email" v-model="form.email" type="email" class="pl-9" required placeholder="correo@empresa.com" autocomplete="off" />
                                        </div>
                                        <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="password">Contraseña</Label>
                                        <Input id="password" v-model="form.password" type="password" required placeholder="••••••••" autocomplete="new-password" />
                                        <p v-if="form.errors.password" class="text-xs text-destructive">{{ form.errors.password }}</p>
                                    </div>
                                    <div class="space-y-2 sm:col-span-2">
                                         <Label for="role">Rol del Sistema</Label>
                                        <Select v-model="form.role">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Seleccionar Rol" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="user">Usuario (Solo Consulta)</SelectItem>
                                                <SelectItem value="city_admin">Administrador Regional</SelectItem>
                                                <SelectItem value="admin">Administrador Global</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="form.errors.role" class="text-xs text-destructive">{{ form.errors.role }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        <!-- Right Column: Job & Location -->
                        <div class="space-y-6">
                            <Card>
                                <CardHeader>
                                    <div class="flex items-center gap-2">
                                        <div class="p-2 bg-emerald-100 rounded-lg dark:bg-emerald-900/20">
                                            <Briefcase class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                        </div>
                                        <div>
                                            <CardTitle>Detalles Laborales</CardTitle>
                                            <CardDescription>Ubicación y cargo</CardDescription>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent class="space-y-4">
                                    <div class="space-y-2">
                                        <Label>Ciudad Base</Label>
                                        <Select v-model="form.city_id">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Seleccionar Ciudad" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="city in cities" :key="city.id" :value="city.id.toString()">
                                                    {{ city.nombre }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="form.errors.city_id" class="text-xs text-destructive">{{ form.errors.city_id }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label>Sucursal (Opcional)</Label>
                                        <Select v-model="form.branch_id" :disabled="!form.city_id">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Seleccionar Sucursal" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="branch in filteredBranches" :key="branch.id" :value="branch.id.toString()">
                                                    {{ branch.nombre }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="form.errors.branch_id" class="text-xs text-destructive">{{ form.errors.branch_id }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <Label>Cargo / Puesto</Label>
                                            <Button type="button" variant="ghost" size="xs" class="h-6 px-2 text-xs" @click="isJobTitleQuickAddOpen = true">
                                                <Plus class="w-3 h-3 mr-1" /> Nuevo
                                            </Button>
                                        </div>
                                        <Select v-model="form.job_title_id">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Seleccionar Cargo" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="title in jobTitles" :key="title.id" :value="title.id.toString()">
                                                    {{ title.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="form.errors.job_title_id" class="text-xs text-destructive">{{ form.errors.job_title_id }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="hire_date">Fecha de Alta</Label>
                                        <Input id="hire_date" type="date" v-model="form.hire_date" />
                                        <p v-if="form.errors.hire_date" class="text-xs text-destructive">{{ form.errors.hire_date }}</p>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 pt-2">
                                        <Checkbox id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                                        <Label for="is_active" class="font-normal cursor-pointer">Usuario Activo</Label>
                                    </div>

                                </CardContent>
                                <CardFooter>
                                    <Button class="w-full" type="submit" :disabled="form.processing">
                                        <UserPlus class="w-4 h-4 mr-2" />
                                        Crear Usuario
                                    </Button>
                                </CardFooter>
                            </Card>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <JobTitleQuickAdd
            :open="isJobTitleQuickAddOpen"
            @update:open="isJobTitleQuickAddOpen = $event"
            @created="handleJobTitleCreated"
        />
    </AppLayout>
</template>
