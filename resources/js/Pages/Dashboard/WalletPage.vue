<template>
    <Head title="Contas" />
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar title="Contas">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
                <template #right>
                    <UButton color="primary" size="sm" icon="i-lucide-plus" @click="modalAdd.open()">Nova conta</UButton>
                </template>
            </UDashboardNavbar>
        </template>
        <template #body>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                <UCard variant="outline" class="col-span-full">
                    <p class="mb-1.5 text-sm text-muted uppercase">Todas as contas</p>
                    <p class="text-3xl font-semibold text-highlighted">{{ formatCurrency(totalBalance ?? 0) }}</p>
                </UCard>
                <template v-if="!accounts || accounts.length === 0">
                    <div class="col-span-full">
                        <UEmpty
                            variant="subtle"
                            title="Nenhuma conta encontrada"
                            description="Você ainda não adicionou nenhuma conta. Clique no botão abaixo para criar sua primeira conta."
                        >
                            <template #actions>
                                <UButton color="primary" size="sm" icon="i-lucide-plus" @click="modalAdd.open()">Nova conta</UButton>
                            </template>
                        </UEmpty>
                    </div>
                </template>
                <template v-else>
                    <AccountItem v-for="(account, i) in accounts" :key="i" :account="account" />
                </template>
            </div>
        </template>
    </UDashboardPanel>
</template>

<script setup lang="ts">
import AccountAddModal from '@/Components/AccountAddModal.vue';
import AccountItem from '@/Components/AccountItem.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { AccountResponse } from '@/types';
import { formatCurrency } from '@/utils';
import { useOverlay } from '@nuxt/ui/runtime/composables/useOverlay.js';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps<{
    accounts: AccountResponse | null;
    totalBalance: number;
}>();

const overlay = useOverlay();
const modalAdd = overlay.create(AccountAddModal);
</script>
