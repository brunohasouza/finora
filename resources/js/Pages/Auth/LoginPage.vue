<template>
    <Head title="Acesse sua conta" />
    <UPageCard class="w-full max-w-md">
        <UAuthForm
            ref="authForm"
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
    email: y.string().required().email(),
    password: y.string().required(),
});

type Schema = y.InferType<typeof schema>;
const authForm = useTemplateRef('authForm');

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

const form = useForm<Schema>({
    email: '',
    password: '',
});

router.on('error', (errors) => {
    authForm.value!.formRef!.setErrors(useFormErrors(errors.detail.errors));
});

function onSubmit(payload: FormSubmitEvent<Schema>) {
    form.defaults(payload.data).reset();
    form.post('/login');
}
</script>
