<template>
    <UModal
        title="Excluir categoria"
        :description="`Tem certeza que deseja excluir a categoria '${categoryName}'?`"
        :close="{ onClick: () => emits('close', false) }"
        :ui="{ footer: 'justify-end', title: 'text-error-400' }"
    >
        <template #body>
            <p class="text-sm text-neutral-300">
                Ao excluir a categoria <strong class="text-error-400">{{ categoryName }}</strong
                >, todas as transações associadas a ela serão afetadas. Por favor, confirme sua decisão.
            </p>
        </template>
        <template #footer>
            <UButton color="neutral" size="sm" @click="emits('close', false)" icon="i-lucide-x">Cancelar</UButton>
            <UButton color="error" size="sm" icon="i-lucide-trash">Excluir</UButton>
        </template>
    </UModal>
</template>

<script setup lang="ts">
const emits = defineEmits<{ close: [boolean] }>();

defineProps<{
    categoryId?: number | string;
    categoryName?: string;
}>();
</script>

<style scoped></style>
