<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { User, ArrowLeft, Save, Briefcase, History, ChevronDown, Trash2, Clock, CheckCircle2, RotateCcw, Pencil } from 'lucide-vue-next';
import JobTitleQuickAdd from '@/components/QuickAdd/JobTitleQuickAdd.vue';
import { route } from 'ziggy-js';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        username: string;
        is_active: boolean;
        city_id?: number | null;
        branch_id?: number | null;
        role: string; // Corrected: role is a string column, not array
        job_title_id?: number | null;
        first_name?: string;
        last_name?: string;
        hire_date?: string;
        termination_date?: string;
        job_history?: Array<{
            id: number;
            job_title: { name: string };
            city?: { nombre: string };
            branch?: { nombre: string };
            assignment_type?: string;
            notes?: string;
            start_date: string;
            end_date?: string;
        }>;
    };


    cities: Array<{ id: number; nombre: string }>;
    branches: Array<{ id: number; nombre: string; ciudad_id: number }>;
    roles: Array<string>; // Corrected: roles passed from controller are strings
    jobTitles: Array<{ id: number; name: string }>;
} >();

// Helper to split names if incomplete
const splitName = (fullName: string) => {
    const parts = fullName.split(' ');
    const first = parts[0] || '';
    const last = parts.slice(1).join(' ') || '';
    return { first, last };
};

const defaultNames = (!props.user.first_name && !props.user.last_name && props.user.name)
    ? splitName(props.user.name)
    : { first: props.user.first_name, last: props.user.last_name };

const form = useForm({
    first_name: defaultNames.first,
    last_name: defaultNames.last,
    username: props.user.username,
    email: props.user.email,
    password: '',
    role: props.user.role,
    city_id: props.user.city_id?.toString(),
    branch_id: props.user.branch_id?.toString(),
    is_active: props.user.is_active,
    job_title_id: props.user.job_title_id?.toString() || '',
    assignment_type: 'permanent',
    notes: '',
    hire_date: props.user.hire_date || '',
    termination_date: props.user.termination_date || '',
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

const localJobTitles = ref([...props.jobTitles]);

const filteredBranches = computed(() => {
    if (!form.city_id) return [];
    return props.branches.filter(branch => branch.ciudad_id.toString() === form.city_id?.toString());
});

const submit = () => {
    form.put(route('users.update', props.user.id));
};

const onJobTitleSuccess = (newJob: any) => {
    localJobTitles.value.push(newJob);
    localJobTitles.value.sort((a, b) => a.name.localeCompare(b.name));
    form.job_title_id = newJob.id.toString();
};

// End Temporary Assignment Logic
const isEndTemporaryOpen = ref(false);
const endTempForm = useForm({
    return_date: new Date().toISOString().split('T')[0],
    job_title_id: '',
    city_id: '',
    branch_id: '',
    notes: '',
});

const openEndTemporaryModal = () => {
    // Try to find a previous permanent record to pre-fill
    const previousPermanent = props.user.job_history?.find(h => h.assignment_type !== 'temporary');
    
    if (previousPermanent && previousPermanent.job_title) {
        // Find the job title ID by matching the name from the props.jobTitles list
        // This handles cases where history prop might not include ID directly
        const matchedJob = props.jobTitles.find(t => t.name === previousPermanent.job_title.name);
        if (matchedJob) {
             endTempForm.job_title_id = matchedJob.id.toString();
        }
    }
    
    // Set default city/branch (maybe current ones? Often return to same city)
    // If not set, user must select.
    if (!endTempForm.city_id && props.user.city_id) {
         endTempForm.city_id = props.user.city_id.toString();
    }
    
    isEndTemporaryOpen.value = true;
};

const filteredRestoreBranches = computed(() => {
    if (!endTempForm.city_id) return [];
    return props.branches.filter(branch => branch.ciudad_id.toString() === endTempForm.city_id?.toString());
});

const tenure = computed(() => {
    if (!props.user.hire_date) return null;
    
    const start = new Date(props.user.hire_date);
    const end = props.user.termination_date ? new Date(props.user.termination_date) : new Date();
    
    // Naive calc
    let years = end.getFullYear() - start.getFullYear();
    let months = end.getMonth() - start.getMonth();
    
    if (months < 0 || (months === 0 && end.getDate() < start.getDate())) {
        years--;
        months += 12;
    }
    
    // Adjust months for day difference
    if (end.getDate() < start.getDate()) {
        months--;
    }
    // Normalize negative months from day adjustment
    if (months < 0) {
        months += 12;
        years--; // Should not happen if logic handled above, but safety
    }
    
    let result = [];
    if (years > 0) result.push(`${years} año${years > 1 ? 's' : ''}`);
    if (months > 0) result.push(`${months} mes${months > 1 ? 'es' : ''}`);
    if (years === 0 && months === 0) return 'Menos de 1 mes';
    
    return result.join(' y ');
});

// Edit History Logic
const isEditHistoryOpen = ref(false);
const editingHistoryId = ref<number | null>(null);
const editHistoryForm = useForm({
    start_date: '',
    end_date: '',
    notes: '',
});

const openEditHistoryModal = (history: any) => {
    editingHistoryId.value = history.id;
    editHistoryForm.start_date = history.start_date.split('T')[0];
    editHistoryForm.end_date = history.end_date ? history.end_date.split('T')[0] : '';
    editHistoryForm.notes = history.notes || '';
    isEditHistoryOpen.value = true;
};

const submitEditHistory = () => {
    if (!editingHistoryId.value) return;
    
    editHistoryForm.put(route('users.job-history.update', [props.user.id, editingHistoryId.value]), {
        onSuccess: () => {
            isEditHistoryOpen.value = false;
            editingHistoryId.value = null;
            editHistoryForm.reset();
        },
    });
};

const submitEndTemporary = () => {
    endTempForm.post(route('users.end-temporary', props.user.id), {
        onSuccess: () => {
            isEndTemporaryOpen.value = false;
            endTempForm.reset();
        },
    });
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Gestión', href: '#' },
    { title: 'Usuarios', href: route('users.index') },
    { title: 'Editar', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Editar Usuario" />

        <div class="max-w-4xl mx-auto p-4 md:p-8">
            <div class="mb-8">
                <Button variant="ghost" as-child class="-ml-2 mb-4 text-muted-foreground hover:text-foreground">
                    <Link :href="route('users.index')">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver al listado
                    </Link>
                </Button>
                
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100">
                        <User class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Editar Usuario</h1>
                        <p class="text-muted-foreground">Actualiza la información personal, roles y cargos.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Form -->
                <div class="lg:col-span-2 space-y-6">
                    <Card class="border-border/60 shadow-xl shadow-indigo-500/5 bg-card/50 backdrop-blur-sm">
                        <form @submit.prevent="submit">
                            <CardHeader class="border-b border-border/50 bg-muted/20 pb-6">
                                <CardTitle class="text-xl text-indigo-700 dark:text-indigo-400">Información Personal</CardTitle>
                                <CardDescription>Datos básicos y de acceso al sistema.</CardDescription>
                            </CardHeader>
                            
                            <CardContent class="p-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label for="first_name">Nombres <span class="text-destructive">*</span></Label>
                                        <Input id="first_name" v-model="form.first_name" required class="h-11" placeholder="Ej. Juan" />
                                        <p v-if="form.errors.first_name" class="text-xs text-destructive">{{ form.errors.first_name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="last_name">Apellidos <span class="text-destructive">*</span></Label>
                                        <Input id="last_name" v-model="form.last_name" required class="h-11" placeholder="Ej. Pérez" />
                                        <p v-if="form.errors.last_name" class="text-xs text-destructive">{{ form.errors.last_name }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label for="username">Usuario <span class="text-destructive">*</span></Label>
                                        <Input id="username" v-model="form.username" required class="h-11" autocomplete="off" />
                                        <p v-if="form.errors.username" class="text-xs text-destructive">{{ form.errors.username }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="email">Correo Electrónico <span class="text-destructive">*</span></Label>
                                        <Input id="email" v-model="form.email" type="email" required class="h-11" autocomplete="off" />
                                        <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="password">Contraseña (Opcional)</Label>
                                    <Input id="password" v-model="form.password" type="password" class="h-11" placeholder="Dejar vacío para mantener actual" autocomplete="new-password" />
                                    <p v-if="form.errors.password" class="text-xs text-destructive">{{ form.errors.password }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label for="role">Rol de Sistema</Label>
                                        <div class="relative">
                                            <select 
                                                id="role"
                                                v-model="form.role" 
                                                class="flex h-11 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none"
                                            >
                                                <option value="" disabled>Seleccione un rol</option>
                                                <option v-for="role in roles" :key="role" :value="role">
                                                    {{ role.charAt(0).toUpperCase() + role.slice(1) }}
                                                </option>
                                            </select>
                                            <ChevronDown class="absolute right-3 top-3.5 h-4 w-4 opacity-50 pointer-events-none" />
                                        </div>
                                        <p v-if="form.errors.role" class="text-xs text-destructive">{{ form.errors.role }}</p>
                                    </div>

                                    <div class="space-y-2 flex items-center pt-8">
                                         <Checkbox id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                                         <Label for="is_active" class="ml-2 font-normal cursor-pointer text-sm">Usuario Activo (Acceso Permitido)</Label>
                                    </div>
                                </div>
                            </CardContent>
                        
                            <!-- Job Details Section -->
                            <CardHeader class="border-y border-border/50 bg-muted/20 pb-6 mt-4">
                                <CardTitle class="text-xl text-indigo-700 dark:text-indigo-400 flex items-center gap-2">
                                    <Briefcase class="w-5 h-5" /> Detalles Laborales
                                </CardTitle>
                                <CardDescription>Asignación de cargos y puestos.</CardDescription>
                            </CardHeader>
                            <CardContent class="p-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label for="city_id">Ciudad</Label>
                                        <div class="relative">
                                            <select 
                                                id="city_id"
                                                v-model="form.city_id" 
                                                class="flex h-11 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none"
                                            >
                                                <option value="" disabled>Seleccione una ciudad</option>
                                                <option v-for="city in cities" :key="city.id" :value="city.id.toString()">
                                                    {{ city.nombre }}
                                                </option>
                                            </select>
                                            <ChevronDown class="absolute right-3 top-3.5 h-4 w-4 opacity-50 pointer-events-none" />
                                        </div>
                                    </div>
                                     <div class="space-y-2">
                                        <Label for="branch_id">Sucursal (Filtrada)</Label>
                                        <div class="relative">
                                            <select 
                                                id="branch_id"
                                                v-model="form.branch_id" 
                                                :disabled="!form.city_id"
                                                class="flex h-11 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none"
                                            >
                                                <option value="" disabled>{{ form.city_id ? 'Seleccione una sucursal' : 'Seleccione una ciudad primero' }}</option>
                                                <option v-for="branch in filteredBranches" :key="branch.id" :value="branch.id.toString()">
                                                    {{ branch.nombre }}
                                                </option>
                                            </select>
                                            <ChevronDown class="absolute right-3 top-3.5 h-4 w-4 opacity-50 pointer-events-none" />
                                        </div>
                                        <p v-if="form.errors.branch_id" class="text-xs text-destructive">{{ form.errors.branch_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="job_title">Cargo Actual</Label>
                                        <div class="flex gap-2">
                                            <div class="relative flex-1">
                                                <select 
                                                    id="job_title"
                                                    v-model="form.job_title_id" 
                                                    class="flex h-11 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none"
                                                >
                                                    <option value="">Sin cargo asignado</option>
                                                    <option v-for="job in localJobTitles" :key="job.id" :value="job.id.toString()">
                                                        {{ job.name }}
                                                    </option>
                                                </select>
                                                <ChevronDown class="absolute right-3 top-3.5 h-4 w-4 opacity-50 pointer-events-none" />
                                            </div>
                                            <JobTitleQuickAdd @success="onJobTitleSuccess" />
                                        </div>
                                        <p class="text-xs text-muted-foreground">Si cambia el cargo, se registrará en el historial automáticamente.</p>
                                        <p v-if="form.errors.job_title_id" class="text-xs text-destructive">{{ form.errors.job_title_id }}</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                                    <div class="space-y-2">
                                        <Label for="assignment_type">Tipo de Asignación</Label>
                                        <div class="relative">
                                            <select 
                                                id="assignment_type"
                                                v-model="form.assignment_type" 
                                                class="flex h-11 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none"
                                            >
                                                <option value="permanent">Titular (Permanente)</option>
                                                <option value="temporary">Interinato / Suplencia (Temporal)</option>
                                            </select>
                                            <ChevronDown class="absolute right-3 top-3.5 h-4 w-4 opacity-50 pointer-events-none" />
                                        </div>
                                        <p class="text-xs text-muted-foreground">Define si el cargo es definitivo o provisional.</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="notes">Notas / Motivo del Cambio</Label>
                                        <Textarea 
                                            id="notes" 
                                            v-model="form.notes" 
                                            class="min-h-[44px] h-11 py-2 resize-none" 
                                            placeholder="Opcional: Ej. Reemplazo por vacaciones..." 
                                        />
                                    </div>

                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-border/50">
                                    <div class="space-y-2">
                                        <Label for="hire_date">Fecha de Alta</Label>
                                        <Input id="hire_date" type="date" v-model="form.hire_date" class="h-11" />
                                        <p class="text-xs text-muted-foreground">Fecha de inicio de contrato/actividades.</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="termination_date">Fecha de Baja Permanete</Label>
                                        <Input id="termination_date" type="date" v-model="form.termination_date" class="h-11 border-destructive/30 focus:border-destructive" />
                                        <p class="text-xs text-muted-foreground">Si el usuario ya no trabaja en la empresa.</p>
                                    </div>
                                </div>
                                <div class="flex justify-end pt-4" v-if="form.assignment_type === 'temporary' || (user.job_history?.some(h => h.assignment_type === 'temporary' && !h.end_date))">
                                    <Dialog v-model:open="isEndTemporaryOpen">
                                        <DialogTrigger as-child>
                                            <Button type="button" variant="outline" class="border-amber-200 text-amber-700 hover:bg-amber-50 hover:text-amber-800" @click="openEndTemporaryModal">
                                                <RotateCcw class="w-4 h-4 mr-2" />
                                                Finalizar Interinato
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent class="sm:max-w-[500px]">
                                            <DialogHeader>
                                                <DialogTitle>Finalizar Interinato / Suplencia</DialogTitle>
                                                <DialogDescription>
                                                    Registra el retorno al cargo permanente. Se cerrará el historial temporal y se creará uno nuevo permanente.
                                                </DialogDescription>
                                            </DialogHeader>
                                            
                                            <div class="grid gap-4 py-4">
                                                <div class="space-y-2">
                                                    <Label for="return_date">Fecha de Retorno</Label>
                                                    <Input id="return_date" type="date" v-model="endTempForm.return_date" required />
                                                    <p v-if="endTempForm.errors.return_date" class="text-xs text-destructive">{{ endTempForm.errors.return_date }}</p>
                                                </div>

                                                <div class="space-y-2">
                                                    <Label for="restore_city">Ciudad de Retorno</Label>
                                                    <div class="relative">
                                                        <select 
                                                            id="restore_city"
                                                            v-model="endTempForm.city_id" 
                                                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none"
                                                        >
                                                            <option value="" disabled>Seleccione ciudad</option>
                                                            <option v-for="city in cities" :key="city.id" :value="city.id.toString()">
                                                                {{ city.nombre }}
                                                            </option>
                                                        </select>
                                                        <ChevronDown class="absolute right-3 top-3 h-4 w-4 opacity-50 pointer-events-none" />
                                                    </div>
                                                    <p v-if="endTempForm.errors.city_id" class="text-xs text-destructive">{{ endTempForm.errors.city_id }}</p>
                                                </div>

                                                <div class="space-y-2">
                                                    <Label for="restore_branch">Sucursal de Retorno</Label>
                                                    <div class="relative">
                                                        <select 
                                                            id="restore_branch"
                                                            v-model="endTempForm.branch_id" 
                                                            :disabled="!endTempForm.city_id"
                                                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none"
                                                        >
                                                            <option value="" disabled>Seleccione sucursal</option>
                                                            <option v-for="branch in filteredRestoreBranches" :key="branch.id" :value="branch.id.toString()">
                                                                {{ branch.nombre }}
                                                            </option>
                                                        </select>
                                                        <ChevronDown class="absolute right-3 top-3 h-4 w-4 opacity-50 pointer-events-none" />
                                                    </div>
                                                     <p v-if="endTempForm.errors.branch_id" class="text-xs text-destructive">{{ endTempForm.errors.branch_id }}</p>
                                                </div>

                                                 <div class="space-y-2">
                                                    <Label for="restore_job">Cargo de Retorno</Label>
                                                    <div class="relative">
                                                        <select 
                                                            id="restore_job"
                                                            v-model="endTempForm.job_title_id" 
                                                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none"
                                                        >
                                                            <option value="" disabled>Seleccione cargo</option>
                                                            <option v-for="job in localJobTitles" :key="job.id" :value="job.id.toString()">
                                                                {{ job.name }}
                                                            </option>
                                                        </select>
                                                        <ChevronDown class="absolute right-3 top-3 h-4 w-4 opacity-50 pointer-events-none" />
                                                    </div>
                                                    <p v-if="endTempForm.errors.job_title_id" class="text-xs text-destructive">{{ endTempForm.errors.job_title_id }}</p>
                                                </div>

                                                <div class="space-y-2">
                                                    <Label for="restore_notes">Notas (Opcional)</Label>
                                                    <Textarea id="restore_notes" v-model="endTempForm.notes" placeholder="Ej. Retorno a funciones normales" />
                                                </div>
                                            </div>

                                            <DialogFooter>
                                                <Button type="button" variant="outline" @click="isEndTemporaryOpen = false">Cancelar</Button>
                                                <Button type="button" @click="submitEndTemporary" :disabled="endTempForm.processing">
                                                    Confirmar Retorno
                                                </Button>
                                            </DialogFooter>
                                        </DialogContent>
                                    </Dialog>
                                </div>
                            </CardContent>

                            <CardFooter class="flex justify-end gap-3 border-t border-border/50 bg-muted/10 px-6 py-4">
                                <Button variant="ghost" type="button" as-child>
                                    <Link :href="route('users.index')">Cancelar</Link>
                                </Button>
                                <Button 
                                    type="submit" 
                                    :disabled="form.processing" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-500/20 px-6"
                                >
                                    <Save v-if="!form.processing" class="mr-2 h-4 w-4" />
                                    <span v-else class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" />
                                    Guardar Cambios
                                </Button>
                            </CardFooter>
                        </form>
                    </Card>
                </div>

                <!-- Job History Sidebar -->
                 <div class="lg:col-span-1">
                    <Card class="h-full border-border/60 shadow-md">
                        <CardHeader class="pb-3 border-b">
                            <CardTitle class="text-lg flex items-center gap-2">
                                <History class="w-4 h-4 text-muted-foreground" />
                                Historial de Cargos
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="p-0">
                            <div v-if="user.hire_date" class="p-4 bg-muted/30 border-b border-border/50 text-sm">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-muted-foreground">Fecha de Alta:</span>
                                    <span class="font-medium text-foreground">{{ new Date(user.hire_date).toLocaleDateString() }}</span>
                                </div>
                                <div class="flex justify-between items-center" v-if="tenure">
                                    <span class="text-muted-foreground">Antigüedad/Tiempo:</span>
                                    <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ tenure }}</span>
                                </div>
                            </div>
                            <div v-if="!user.job_history || user.job_history.length === 0" class="p-6 text-center text-muted-foreground text-sm">
                                No hay historial registrado.
                            </div>
                            <ul v-else class="divide-y divide-gray-100 dark:divide-gray-800">
                                <li v-for="history in user.job_history" :key="history.id" class="p-4 hover:bg-muted/50 transition-colors flex justify-between items-start group">
                                    <div>
                                        <div class="font-medium text-sm text-foreground mb-1 flex items-center gap-2">
                                            <!-- {{ history.job_title?.name || 'Sin cargo' }} -->
                                            {{ history.job_title?.name || 'Sin cargo' }} 
                                            <span v-if="history.branch" class="text-muted-foreground font-normal"> - {{ history.branch.nombre }}</span>
                                            <span 
                                                v-if="history.assignment_type === 'temporary'" 
                                                class="px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] uppercase font-bold border border-amber-200"
                                            >
                                                Temp
                                            </span>
                                        </div>
                                        <div class="text-xs text-muted-foreground flex flex-col gap-1">
                                            <span v-if="history.city" class="flex items-center gap-1">📍 {{ history.city.nombre }}</span>
                                            <span v-if="history.notes" class="italic text-foreground/70">"{{ history.notes }}"</span>
                                            <span class="flex items-center gap-1 text-xs">
                                                <Clock class="w-3 h-3" />
                                                {{ new Date(history.start_date).toLocaleDateString() }}
                                                <span v-if="history.end_date"> — {{ new Date(history.end_date).toLocaleDateString() }}</span>
                                                <span v-else class="text-green-600 font-medium ml-1 flex items-center gap-0.5"><CheckCircle2 class="w-3 h-3"/> Actualidad</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex gap-1">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="opacity-0 group-hover:opacity-100 text-indigo-600 hover:bg-indigo-50 -mt-2 transition-opacity"
                                            @click="openEditHistoryModal(history)"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="opacity-0 group-hover:opacity-100 text-destructive hover:bg-destructive/10 -mt-2 -mr-2 transition-opacity"
                                            @click="$inertia.delete(route('users.job-history.destroy', [user.id, history.id]), { preserveScroll: true })"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </li>
                            </ul>
                        </CardContent>
                    </Card>

                    <!-- Edit History Modal -->
                    <Dialog v-model:open="isEditHistoryOpen">
                        <DialogContent class="sm:max-w-[425px]">
                            <DialogHeader>
                                <DialogTitle>Editar Registro Histórico</DialogTitle>
                                <DialogDescription>
                                    Corrige las fechas o notas de este registro.
                                </DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="space-y-2">
                                    <Label for="edit_start_date">Fecha de Inicio</Label>
                                    <Input id="edit_start_date" type="date" v-model="editHistoryForm.start_date" required />
                                    <p v-if="editHistoryForm.errors.start_date" class="text-xs text-destructive">{{ editHistoryForm.errors.start_date }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="edit_end_date">Fecha de Fin</Label>
                                    <Input id="edit_end_date" type="date" v-model="editHistoryForm.end_date" />
                                    <p class="text-xs text-muted-foreground">Deja vacío si es el cargo actual.</p>
                                    <p v-if="editHistoryForm.errors.end_date" class="text-xs text-destructive">{{ editHistoryForm.errors.end_date }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="edit_notes">Notas</Label>
                                    <Textarea id="edit_notes" v-model="editHistoryForm.notes" />
                                </div>
                            </div>
                            <DialogFooter>
                                <Button type="button" variant="outline" @click="isEditHistoryOpen = false">Cancelar</Button>
                                <Button type="button" @click="submitEditHistory" :disabled="editHistoryForm.processing">Guardar Corrección</Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
