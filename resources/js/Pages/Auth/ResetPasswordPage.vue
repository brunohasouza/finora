<template>
    <Head title="Redefinir senha" />
    <UPageCard class="w-full max-w-md">
        <UAuthForm
            ref="authForm"
            title="Redefinir sua senha"
            description="Insira sua nova senha abaixo."
            icon="i-lucide-key"
            :fields="fields"
            :schema="schema"
            :submit="{ label: 'Redefinir Senha', size: 'lg' }"
            :loading="form.processing"
            @submit="onSubmit"
        >
            <template #footer>
                <div class="text-center text-sm">
                    Lembrou sua senha?
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

const props = defineProps({
    token: String,
    email: String,
});

const schema = y.object({
    password: y.string().required().min(8),
    password_confirmation: y
        .string()
        .oneOf([y.ref('password')], 'As senhas devem coincidir')
        .required(),
});

type Schema = y.InferType<typeof schema>;
const authForm = useTemplateRef('authForm');
const form = useForm<Schema & { token: string; email: string }>({
    token: props.token ?? '',
    email: props.email ?? '',
    password: '',
    password_confirmation: '',
});

const fields: AuthFormField[] = [
    {
        name: 'password',
        label: 'Nova Senha',
        type: 'password',
        placeholder: 'Insira sua nova senha',
        required: true,
    },
    {
        name: 'password_confirmation',
        label: 'Confirme a Nova Senha',
        type: 'password',
        placeholder: 'Confirme sua nova senha',
        required: true,
    },
];

router.on('error', (errors) => {
    authForm.value!.formRef!.setErrors(useFormErrors(errors.detail.errors));
});

const onSubmit = (payload: FormSubmitEvent<Schema>) => {
    form.defaults(payload.data).reset();
    form.post('/reset-password');
};
</script>
