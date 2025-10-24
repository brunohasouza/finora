<template>
    <Head title="Esqueceu sua senha?" />
    <UPageCard class="w-full max-w-md">
        <UAuthForm
            title="Esqueceu sua senha?"
            description="Insira seu e-mail para receber instruções de redefinição de senha."
            icon="i-lucide-lock-open"
            :submit="{ label: 'Enviar Instruções', size: 'lg' }"
            :schema="schema"
            :fields="fields"
            @submit="onSubmit"
        >
            <template #foooter>
                <div class="text-center text-sm">
                    Lembrou sua senha?
                    <Link href="/login" class="font-bold text-primary-600 hover:underline">Acesse aqui</Link>
                </div>
            </template>
        </UAuthForm>
    </UPageCard>
</template>

<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { AuthFormField, FormSubmitEvent } from '@nuxt/ui';
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
];

const schema = y.object({
    email: y.string().required().email(),
});

function onSubmit(payload: FormSubmitEvent<y.InferType<typeof schema>>) {
    console.log('Submitted', payload);
}
</script>
