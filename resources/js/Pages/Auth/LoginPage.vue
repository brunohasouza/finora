<template>
    <UPageCard class="w-full max-w-md">
        <UAuthForm title="Acesse sua conta" icon="i-lucide-user" :fields="fields" @submit="onSubmit" :schema="schema">
            <template #footer>
                <div class="text-center text-sm">
                    Não possui uma conta?
                    <Link href="/register" class="font-bold text-primary-600 hover:underline">Cadastre-se</Link>
                </div>
            </template>
        </UAuthForm>
    </UPageCard>
</template>

<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { VALIDATION_MESSAGES } from '@/utils/constants';
import { Link } from '@inertiajs/vue3';
import type { AuthFormField, FormSubmitEvent } from '@nuxt/ui';
import * as y from 'yup';

defineOptions({
    layout: AuthLayout,
});

const fields: AuthFormField[] = [
    {
        name: 'email',
        type: 'email',
        label: 'E-mail',
        placeholder: 'joao.silva@email.com',
        required: true,
    },
    {
        name: 'password',
        label: 'Senha',
        type: 'password',
        placeholder: 'Insira sua senha',
        required: true,
    },
];

const schema = y.object({
    email: y.string().required(VALIDATION_MESSAGES.REQUIRED_FIELD).email(VALIDATION_MESSAGES.INVALID_EMAIL),
    password: y.string().required(VALIDATION_MESSAGES.REQUIRED_FIELD),
});

function onSubmit(payload: FormSubmitEvent<any>) {
    console.log('Submitted', payload);
}
</script>
