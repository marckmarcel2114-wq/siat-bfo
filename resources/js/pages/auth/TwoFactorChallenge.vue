<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { InputOTP, InputOTPGroup, InputOTPSlot } from '@/components/ui/input-otp';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/two-factor-challenge';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const recovery = ref(false);

const toggleRecovery = () => {
    recovery.value = !recovery.value;
};
</script>

<template>
    <AuthLayout
        title="Two factor challenge"
        :description="recovery
            ? 'Enter one of your emergency recovery codes.'
            : 'Please confirm access to your account by entering the authentication code provided by your authenticator application.'"
    >
        <Head title="Two factor challenge" />

        <Form
            v-bind="store.form({ recovery })"
            :reset-on-success="['code', 'recovery_code']"
            v-slot="{ errors, processing }"
            class="space-y-6"
        >
            <div class="grid gap-6">
                <div v-show="!recovery">
                    <Label for="code" class="sr-only">Authentication code</Label>
                    <InputOTP
                        id="code"
                        name="code"
                        :maxlength="6"
                        class="justify-center"
                        :disabled="processing"
                    >
                        <InputOTPGroup>
                            <InputOTPSlot :index="0" />
                            <InputOTPSlot :index="1" />
                            <InputOTPSlot :index="2" />
                            <InputOTPSlot :index="3" />
                            <InputOTPSlot :index="4" />
                            <InputOTPSlot :index="5" />
                        </InputOTPGroup>
                    </InputOTP>
                    <InputError :message="errors.code" class="mt-2" />
                </div>

                <div v-show="recovery">
                    <Label for="recovery_code">Recovery code</Label>
                    <Input
                        id="recovery_code"
                        name="recovery_code"
                        type="text"
                        :disabled="processing"
                        :tabindex="1"
                        placeholder="Recovery code"
                    />
                    <InputError :message="errors.recovery_code" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <button
                        type="button"
                        class="text-sm underline hover:text-foreground"
                        @click="toggleRecovery"
                    >
                        {{
                            recovery
                                ? 'Use an authentication code'
                                : 'Use a recovery code'
                        }}
                    </button>

                    <Button
                        type="submit"
                        :tabindex="2"
                        :disabled="processing"
                        data-test="two-factor-challenge-button"
                    >
                        <Spinner v-if="processing" />
                        Log in
                    </Button>
                </div>
            </div>
        </Form>
    </AuthLayout>
</template>