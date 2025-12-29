<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Plus, Save, Building, Loader2 } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import axios from 'axios';
import { route } from 'ziggy-js';

const props = defineProps<{
    cities: Array<{ id: number; name: string }>;
    types: Array<{ id: number; name: string }>;
}>();

const emit = defineEmits(['success']);

const open = ref(false);
const loading = ref(false);
const errors = ref<Record<string, string>>({});

const form = ref({
    city_id: '',
    branch_type_id: '',
    code: '',
    name: '',
    address: '',
});

const resetForm = () => {
    form.value = { city_id: '', branch_type_id: '', code: '', name: '', address: '' };
    errors.value = {};
};

const submit = async () => {
    loading.value = true;
    errors.value = {};
    
    try {
        const response = await axios.post(route('branches.store'), form.value, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        emit('success', response.data);
        open.value = false;
        resetForm();
    } catch (error: any) {
        if (error.response?.status === 422) {
            const laravelErrors = error.response.data.errors;
            const formattedErrors: Record<string, string> = {};
            for (const key in laravelErrors) {
                formattedErrors[key] = laravelErrors[key][0];
            }
            errors.value = formattedErrors;
        } else {
            console.error('Error creating branch:', error);
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <slot>
                <Button 
                    type="button" 
                    variant="outline" 
                    size="icon" 
                    class="h-10 w-10 shrink-0 border-dashed border-2 hover:border-primary hover:text-primary transition-colors"
                >
                    <Plus class="h-4 w-4" />
                </Button>
            </slot>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[550px]">
            <DialogHeader>
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary border border-primary/20">
                        <Building class="h-5 w-5" />
                    </div>
                    <div>
                        <DialogTitle>Nueva Sucursal/Agencia</DialogTitle>
                        <DialogDescription>
                            Registra una nueva ubicación base rápidamente.
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Ciudad</Label>
                        <Select v-model="form.city_id">
                            <SelectTrigger class="h-11 bg-background/50">
                                <SelectValue placeholder="Seleccione..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="city in cities" :key="city.id" :value="city.id.toString()">
                                    {{ city.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="errors.city_id" class="text-xs font-semibold text-destructive">{{ errors.city_id }}</p>
                    </div>
                    <div class="grid gap-2">
                        <Label class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Tipo</Label>
                        <Select v-model="form.branch_type_id">
                            <SelectTrigger class="h-11 bg-background/50">
                                <SelectValue placeholder="Seleccione..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="t in types" :key="t.id" :value="t.id.toString()">
                                    {{ t.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="errors.branch_type_id" class="text-xs font-semibold text-destructive">{{ errors.branch_type_id }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="grid gap-2 col-span-1">
                        <Label class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Código</Label>
                        <Input v-model="form.code" placeholder="Ej. 001" class="h-11 bg-background/50 font-mono" />
                    </div>
                    <div class="grid gap-2 col-span-2">
                        <Label class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Nombre</Label>
                        <Input v-model="form.name" placeholder="Ej. Central" class="h-11 bg-background/50" />
                    </div>
                </div>
                <p v-if="errors.name" class="text-xs font-semibold text-destructive">{{ errors.name }}</p>

                <div class="grid gap-2">
                    <Label class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Dirección</Label>
                    <Input v-model="form.address" placeholder="Ej. Av. Blanco Galindo..." class="h-11 bg-background/50" />
                </div>
            </div>
            <DialogFooter>
                <Button variant="ghost" type="button" @click="open = false" :disabled="loading">
                    Cancelar
                </Button>
                <Button type="button" @click="submit" :disabled="loading" class="shadow-lg shadow-primary/20">
                    <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                    <Save v-else class="mr-2 h-4 w-4" />
                    Guardar Sucursal
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
