<template>
    <UCard variant="subtle">
        <div class="flex gap-3">
            <div class="flex-1 space-y-1 [&>p]:w-full [&>p]:overflow-hidden [&>p]:text-ellipsis [&>p]:whitespace-nowrap">
                <p class="text-base font-medium">{{ account.name }}</p>
                <p class="mb-3 text-sm text-muted">{{ account.bank.name }}</p>
                <p class="text-2xl font-bold">{{ formatCurrency(account.balance) }}</p>
            </div>
            <div class="flex-none">
                <UDropdownMenu :items="dropdownItems" :content="{ align: 'end', side: 'bottom' }" :ui="{ content: 'w-40' }">
                    <UButton icon="i-lucide-ellipsis-vertical" variant="ghost" color="neutral" />
                </UDropdownMenu>
            </div>
        </div>
    </UCard>
</template>

<script setup lang="ts">
import { Account } from '@/types';
import { formatCurrency } from '@/utils';
import { DropdownMenuItem } from '@nuxt/ui';
import { useOverlay } from '@nuxt/ui/runtime/composables/useOverlay.js';
import { ref } from 'vue';
import AccountAddModal from './AccountAddModal.vue';

const props = defineProps<{
    account: Account;
}>();

const overlay = useOverlay();
const modalAdd = overlay.create(AccountAddModal);

const dropdownItems = ref<DropdownMenuItem[]>([
    {
        label: 'Editar conta',
        icon: 'i-lucide-pen',
        onSelect: () => {
            modalAdd.open({
                account: props.account,
            });
        },
    },
    {
        type: 'separator',
    },
    {
        label: 'Excluir conta',
        icon: 'i-lucide-trash',
        color: 'error',
    },
]);
</script>
