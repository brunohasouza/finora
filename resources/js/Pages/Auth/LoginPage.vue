<template>
    <div class="flex flex-col items-center justify-center gap-4 p-4">
        <UPageCard class="w-full max-w-md">
            <UAuthForm
                title="Login"
                description="Enter your credentials to access your account."
                icon="i-lucide-user"
                :fields="fields"
                @submit="onSubmit"
            />
        </UPageCard>
    </div>
</template>

<script setup lang="ts">
    import type { FormSubmitEvent, AuthFormField } from '@nuxt/ui'
    import BlankLayout from '@/Layouts/BlankLayout.vue';

    defineOptions({
        layout: BlankLayout
    })

    const fields: AuthFormField[] = [{
        name: 'email',
        type: 'email',
        label: 'Email',
        placeholder: 'Enter your email',
        required: true
    }, {
        name: 'password',
        label: 'Password',
        type: 'password',
        placeholder: 'Enter your password',
        required: true
    }]

    function onSubmit(payload: FormSubmitEvent<any>) {
        console.log('Submitted', payload)
    }
</script>
