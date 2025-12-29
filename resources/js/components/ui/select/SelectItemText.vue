<script setup lang="ts">
import type { SelectItemTextProps } from 'reka-ui'
import type { HTMLAttributes } from 'vue'
import { SelectItemText } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<SelectItemTextProps & { class?: HTMLAttributes['class'] }>()
</script>

<template>
  <SelectItemText :class="cn('', props.class)" v-bind="props">
    <slot />
  </SelectItemText>
</template>
