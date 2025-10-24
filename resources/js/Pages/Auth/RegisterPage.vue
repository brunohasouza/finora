<template>
    <Head title="Crie sua conta" />
    <UPageCard class="w-full max-w-md">
        <UAuthForm
            title="Crie sua conta"
            description="Cadastre-se e comece a gerenciar suas finanças hoje mesmo."
            icon="i-lucide-user-plus"
            :fields="fields"
            :submit="{ label: 'Cadastrar', size: 'lg' }"
            :schema="schema"
            @submit="onSubmit"
        >
            <template #footer>
                <div class="text-center text-sm">
                    Já possui uma conta?
                    <Link href="/login" class="font-bold text-primary-600 hover:underline">Acesse aqui</Link>
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
        name: 'name',
        label: 'Nome Completo',
        type: 'text',
        placeholder: 'João da Silva',
        required: true,
    },
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
    {
        name: 'password_confirmation',
        label: 'Confirme a Senha',
        type: 'password',
        placeholder: 'Confirme sua senha',
        required: true,
    },
];

const schema = y.object({
    name: y.string().required(),
    email: y.string().required().email(),
    password: y.string().required().min(12),
    password_confirmation: y
        .string()
        .oneOf([y.ref('password')], 'As senhas devem coincidir')
        .required(),
});

function onSubmit(payload: FormSubmitEvent<y.InferType<typeof schema>>) {
    console.log('Submitted', payload);
}
</script>
