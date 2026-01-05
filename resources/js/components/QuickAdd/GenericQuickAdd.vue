<script setup lang="ts">
import { ref, watch, reactive } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Plus, Save, Loader2, Database } from 'lucide-vue-next';
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
    title: string;
    description?: string;
    endpoint: string; // route name e.g. 'brands.store'
    params?: Record<string, any>; // extra data e.g. { marca_id: 1 }
    placeholder?: string;
    label?: string;
    icon?: any;

    disabled?: boolean;
    enableColor?: boolean; // New prop
}>();

const emit = defineEmits(['success']);

const open = ref(false);
const loading = ref(false);
const errors = ref<Record<string, string>>({});

const form = reactive({
    name: '',
    color: '#000000', // Default color
});

const submit = async () => {
    loading.value = true;
    errors.value = {};
    
    // Merge name with extra params
    const payload = {
        name: form.name,
        nombre: form.name, // Most controllers use 'nombre' or 'name'. 
        ...props.params,
        ...(props.enableColor ? { color: form.color } : {})
    };

    // Mapping for AssetType legacy (already covered by name: form.name above, but keeping structure)
    if (props.endpoint === 'asset-types.store') {
        // @ts-ignore
        payload.name = form.name;
    }

    try {
        const url = route(props.endpoint);
        const response = await axios.post(url, payload);
        
        emit('success', response.data);
        open.value = false;
        form.name = '';
    } catch (error: any) {
        if (error.response?.status === 422) {
             const laravelErrors = error.response.data.errors;
            const formattedErrors: Record<string, string> = {};
            for (const key in laravelErrors) {
                // Map errors to both name and nombre to ensure they show up
                formattedErrors[key] = laravelErrors[key][0];
                if (key === 'nombre') formattedErrors['name'] = laravelErrors[key][0];
                if (key === 'name') formattedErrors['nombre'] = laravelErrors[key][0];
            }
            errors.value = formattedErrors;
        } else {
            console.error('Error creating item:', error);
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
                    :disabled="disabled"
                    class="h-10 w-10 shrink-0 border-dashed border-2 hover:border-primary hover:text-primary transition-colors"
                    title="Añadir Nuevo"
                >
                    <Plus class="h-4 w-4" />
                </Button>
            </slot>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary border border-primary/20">
                        <component :is="icon || Database" class="h-5 w-5" />
                    </div>
                    <div>
                        <DialogTitle>{{ title }}</DialogTitle>
                        <DialogDescription>
                            {{ description || 'Crear nuevo registro en el catálogo.' }}
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">{{ label || 'Nombre' }}</Label>
                    <Input 
                        v-model="form.name" 
                        :placeholder="placeholder || 'Ingrese nombre...'" 
                        class="h-11 bg-background/50" 
                        @keyup.enter="submit" 
                    />
                    <p v-if="errors.name" class="text-xs font-semibold text-destructive">{{ errors.name }}</p>
                    <p v-if="errors.nombre" class="text-xs font-semibold text-destructive">{{ errors.nombre }}</p>
                </div>

                
                <!-- Optional Color Input -->
                <div v-if="enableColor" class="grid gap-2">
                    <Label class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Color (Distintivo)</Label>
                    <div class="flex items-center gap-2">
                         <Input 
                            v-model="form.color" 
                            type="color"
                            class="h-11 w-20 p-1 cursor-pointer" 
                        />
                        <Input 
                            v-model="form.color" 
                            placeholder="#000000" 
                            class="h-11 flex-1 font-mono uppercase" 
                        />
                    </div>
                </div>
            </div>
            <DialogFooter>
                <Button variant="ghost" type="button" @click="open = false" :disabled="loading">
                    Cancelar
                </Button>
                <Button type="button" @click="submit" :disabled="loading" class="shadow-lg shadow-primary/20">
                    <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                    <Save v-else class="mr-2 h-4 w-4" />
                    Guardar
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
