<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/confirm-password';
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
    routeName: string;
}>();
</script>

<template>
    <AuthLayout
        title="Confirm password"
        description="This action requires password confirmation. Please enter your password to continue."
    >
        <Head title="Confirm password" />

        <Form
            v-bind="store.form({ routeName })"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="space-y-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="1"
                        autocomplete="current-password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <Button
                    type="submit"
                    class="w-full"
                    :tabindex="2"
                    :disabled="processing"
                    data-test="confirm-password-button"
                >
                    <Spinner v-if="processing" />
                    Confirm
                </Button>
            </div>
        </Form>
    </AuthLayout>
</template>