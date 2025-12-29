<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Plus, Save, Monitor, Loader2 } from 'lucide-vue-next';
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

const emit = defineEmits(['success']);

const open = ref(false);
const loading = ref(false);
const errors = ref<Record<string, string>>({});

const form = ref({
    name: '',
});

const resetForm = () => {
    form.value = { name: '' };
    errors.value = {};
};

const submit = async () => {
    loading.value = true;
    errors.value = {};
    
    try {
        const response = await axios.post(route('asset-types.store'), form.value, {
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
            console.error('Error creating asset type:', error);
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
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary border border-primary/20">
                        <Monitor class="h-5 w-5" />
                    </div>
                    <div>
                        <DialogTitle>Nuevo Tipo de Activo</DialogTitle>
                        <DialogDescription>
                            Define una categoría de hardware rápidamente.
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="at-name" class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Nombre</Label>
                    <Input id="at-name" v-model="form.name" placeholder="Ej. Laptop, Monitor..." class="h-11 bg-background/50" @keyup.enter="submit" />
                    <p v-if="errors.name" class="text-xs font-semibold text-destructive">{{ errors.name }}</p>
                </div>
            </div>
            <DialogFooter>
                <Button variant="ghost" type="button" @click="open = false" :disabled="loading">
                    Cancelar
                </Button>
                <Button type="button" @click="submit" :disabled="loading" class="shadow-lg shadow-primary/20">
                    <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                    <Save v-else class="mr-2 h-4 w-4" />
                    Guardar Tipo
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
