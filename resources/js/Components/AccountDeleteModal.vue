<template>
    <UModal
        title="Excluir conta"
        :description="`Tem certeza que deseja excluir a conta '${accountName}'?`"
        :close="{ onClick: () => emits('close', false) }"
        :ui="{ footer: 'justify-end', title: 'text-error-400' }"
    >
        <template #body>
            <p class="text-sm text-neutral-300">
                Ao excluir a conta <strong class="text-error-400">{{ accountName }}</strong
                >, todas as transações associadas a ela serão afetadas. Por favor, confirme sua decisão.
            </p>
            <UAlert v-if="errorMessage" :description="errorMessage" icon="i-lucide-x-circle" color="error" class="mt-5" />
        </template>
        <template #footer>
            <UButton color="neutral" size="sm" variant="outline" @click="emits('close', false)" icon="i-lucide-x">Cancelar</UButton>
            <UButton color="error" size="sm" icon="i-lucide-trash" @click="confirmDelete">Excluir</UButton>
        </template>
    </UModal>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { useToast } from '@nuxt/ui/runtime/composables/useToast.js';
import { ref } from 'vue';

const emits = defineEmits<{ close: [boolean] }>();

const props = defineProps<{
    accountId?: number | string;
    accountName?: string;
}>();

const toast = useToast();

const errorMessage = ref('');

function confirmDelete() {
    const query = window.location.search;
    router.delete(`/accounts/${props.accountId}`, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: () => {
            emits('close', false);

            toast.add({
                title: 'Conta excluída',
                description: `A conta '${props.accountName}' foi excluída com sucesso.`,
                color: 'success',
                icon: 'i-lucide-check-circle',
            });
        },
        onError: (errors) => {
            console.error(errors);
            errorMessage.value = 'Ocorreu um erro ao excluir a conta.';
        },
    });
}
</script>
