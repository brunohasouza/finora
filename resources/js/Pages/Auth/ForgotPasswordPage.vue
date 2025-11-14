<template>
    <Head title="Esqueceu sua senha?" />
    <UPageCard class="w-full max-w-md">
        <UAuthForm
            ref="authForm"
            title="Esqueceu sua senha?"
            description="Insira seu e-mail para receber instruções de redefinição de senha."
            icon="i-lucide-lock-open"
            :submit="{ label: 'Enviar Instruções', size: 'lg' }"
            :schema="schema"
            :fields="fields"
            :loading="form.processing"
            @submit="onSubmit"
        >
            <template #validation>
                <UAlert v-if="status" color="info" icon="i-lucide-info" :title="status" />
            </template>
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
import { AuthFormField, FormSubmitEvent } from '@nuxt/ui';
import { useTemplateRef } from 'vue';
import * as y from 'yup';

defineOptions({
    layout: AuthLayout,
});

defineProps<{
    status?: string;
}>();

const schema = y.object({
    email: y.string().required().email(),
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
];

const form = useForm<Schema>({
    email: '',
});

router.on('error', (errors) => {
    authForm.value!.formRef!.setErrors(useFormErrors(errors.detail.errors));
});

function onSubmit(payload: FormSubmitEvent<Schema>) {
    form.defaults(payload.data).reset();
    form.post('/forgot-password');
}
</script>
