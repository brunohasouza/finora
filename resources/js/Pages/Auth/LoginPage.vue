<template>
    <Head title="Acesse sua conta" />
    <UPageCard class="w-full max-w-md">
        <UAuthForm
            title="Acesse sua conta"
            description="Insira seus dados para acessar sua conta."
            icon="i-lucide-user"
            :fields="fields"
            :schema="schema"
            :submit="{ label: 'Entrar', size: 'lg' }"
            @submit="onSubmit"
        >
            <template #password-hint>
                <Link href="/forgot-password" class="text-sm font-bold text-primary-600 hover:underline">Esqueceu sua senha?</Link>
            </template>
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
import { Head, Link } from '@inertiajs/vue3';
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
    email: y.string().required().email(),
    password: y.string().required(),
});

function onSubmit(payload: FormSubmitEvent<y.InferType<typeof schema>>) {
    console.log('Submitted', payload);
}
</script>
