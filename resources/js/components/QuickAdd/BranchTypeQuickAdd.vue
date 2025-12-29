<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Plus, Save, Settings2, Loader2 } from 'lucide-vue-next';
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
    description: '',
    color: 'blue',
    sort_order: 0,
});

const colors = [
    { name: 'Sky', value: 'sky', class: 'bg-sky-500' },
    { name: 'Emerald', value: 'emerald', class: 'bg-emerald-500' },
    { name: 'Orange', value: 'orange', class: 'bg-orange-500' },
    { name: 'Amber', value: 'amber', class: 'bg-amber-500' },
    { name: 'Rose', value: 'rose', class: 'bg-rose-500' },
    { name: 'Indigo', value: 'indigo', class: 'bg-indigo-500' },
    { name: 'Violet', value: 'violet', class: 'bg-violet-500' },
    { name: 'Blue', value: 'blue', class: 'bg-blue-500' },
];

const resetForm = () => {
    form.value = { name: '', description: '', color: 'blue', sort_order: 0 };
    errors.value = {};
};

const submit = async () => {
    loading.value = true;
    errors.value = {};
    
    try {
        const response = await axios.post(route('branch-types.store'), form.value, {
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
            console.error('Error creating branch type:', error);
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
                    title="Nuevo Tipo"
                >
                    <Plus class="h-4 w-4" />
                </Button>
            </slot>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[500px]">
            <DialogHeader>
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary border border-primary/20">
                        <Settings2 class="h-5 w-5" />
                    </div>
                    <div>
                        <DialogTitle>Nuevo Tipo de Sucursal</DialogTitle>
                        <DialogDescription>
                            Crea una nueva categoría rápidamente.
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>
            <div class="grid gap-6 py-4">
                <div class="grid gap-2">
                    <Label for="bt-name" class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Nombre</Label>
                    <Input id="bt-name" v-model="form.name" placeholder="Ej. Agencia Rural" class="h-11 bg-background/50" />
                    <p v-if="errors.name" class="text-xs font-semibold text-destructive">{{ errors.name }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="bt-desc" class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Descripción</Label>
                    <Textarea id="bt-desc" v-model="form.description" placeholder="Opcional..." class="bg-background/50 resize-none" rows="2" />
                    <p v-if="errors.description" class="text-xs font-semibold text-destructive">{{ errors.description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="bt-order" class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Orden (Peso)</Label>
                        <Input id="bt-order" type="number" v-model="form.sort_order" class="h-11 bg-background/50" />
                    </div>
                    <div class="grid gap-2">
                        <Label class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">Color</Label>
                        <div class="flex flex-wrap gap-2 pt-1">
                            <button 
                                v-for="color in colors" 
                                :key="color.value"
                                type="button"
                                @click="form.color = color.value"
                                class="h-8 w-8 rounded-full border-2 transition-all"
                                :class="[
                                    color.class,
                                    form.color === color.value ? 'border-foreground scale-110' : 'border-transparent opacity-70 hover:opacity-100'
                                ]"
                            />
                        </div>
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
                    Guardar Tipo
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
