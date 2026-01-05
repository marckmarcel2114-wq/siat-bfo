<script setup lang="ts">
import GenericQuickAdd from '@/components/QuickAdd/GenericQuickAdd.vue';
import { Users } from 'lucide-vue-next';

defineProps<{
    disabled?: boolean;
}>();

const emit = defineEmits(['success']);

const onSuccess = (data: any) => {
    emit('success', data);
};
</script>

<template>
    <GenericQuickAdd
        title="Nuevo Cargo"
        description="Registra un nuevo cargo laboral rápidamente."
        label="Nombre del Cargo"
        placeholder="Ej. CONTADOR, GERENTE..."
        endpoint="job-titles.store"
        :icon="Users"
        :disabled="disabled"
        @success="onSuccess"
    />
</template>
