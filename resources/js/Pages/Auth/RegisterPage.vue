<template>
    <Head title="Crie sua conta" />
    <UPageCard class="w-full max-w-md">
        <UAuthForm
            ref="authForm"
            title="Crie sua conta"
            description="Cadastre-se e comece a gerenciar suas finanças hoje mesmo."
            icon="i-lucide-user-plus"
            :fields="fields"
            :submit="{ label: 'Cadastrar', size: 'lg' }"
            :schema="schema"
            :loading="form.processing"
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
import { useFormErrors } from '@/Composables';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import type { AuthFormField, FormSubmitEvent } from '@nuxt/ui';
import { useTemplateRef } from 'vue';
import * as y from 'yup';

defineOptions({
    layout: AuthLayout,
});

const schema = y.object({
    name: y.string().required(),
    email: y.string().required().email(),
    password: y.string().required().min(8),
    password_confirmation: y
        .string()
        .oneOf([y.ref('password')], 'As senhas devem coincidir')
        .required(),
});

type Schema = y.InferType<typeof schema>;
const authForm = useTemplateRef('authForm');

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

const form = useForm<Schema>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

router.on('error', (errors) => {
    authForm.value!.formRef!.setErrors(useFormErrors(errors.detail.errors));
});

function onSubmit(payload: FormSubmitEvent<Schema>) {
    form.defaults(payload.data).reset();
    form.post('/register');
}
</script>
