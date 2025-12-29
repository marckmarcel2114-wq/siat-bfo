<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

defineProps<{
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}>();
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap items-center justify-center gap-1 mt-4">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null" class="px-3 py-2 text-sm text-muted-foreground border border-transparent rounded-md" v-html="link.label" />
            
            <Button
                v-else
                :variant="link.active ? 'default' : 'outline'"
                size="sm"
                as-child
                class="min-w-[32px]"
            >
                <Link :href="link.url" preserve-scroll v-html="link.label" />
            </Button>
        </template>
    </div>
</template>
