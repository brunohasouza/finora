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
                <UCard variant="outline">
                    <p class="mb-1.5 text-sm text-muted uppercase">Saldo total:</p>
                    <p class="text-3xl font-semibold text-highlighted">{{ formatCurrency(totalBalance ?? 0) }}</p>
                </UCard>
                <UCard variant="outline">
                    <p class="mb-1.5 text-sm text-muted uppercase">Limite total de crédito:</p>
                    <p class="text-3xl font-semibold text-highlighted">{{ formatCurrency(totalCreditLimit ?? 0) }}</p>
                </UCard>
                <UCard variant="outline">
                    <p class="mb-1.5 text-sm text-muted uppercase">Limite total de crédito disponível:</p>
                    <p class="text-3xl font-semibold text-highlighted">{{ formatCurrency(totalAvailableLimit ?? 0) }}</p>
                </UCard>
            </div>
            <UPageCard variant="naked" title="Contas bancárias" description="Gerencie suas contas bancárias que estão listadas abaixo." class="mb-4">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <div v-if="bankAccounts.length === 0" class="col-span-full">
                        <UEmpty variant="subtle" title="Nenhuma conta encontrada" description="Você ainda não adicionou nenhuma conta.">
                            <template #actions>
                                <UButton color="primary" size="sm" icon="i-lucide-plus" @click="modalAdd.open()">Nova conta</UButton>
                            </template>
                        </UEmpty>
                    </div>
                    <template v-else>
                        <AccountItem v-for="(account, i) in bankAccounts" :key="i" :account="account" />
                    </template>
                </div>
            </UPageCard>
            <UPageCard variant="naked" title="Cartões de crédito" description="Gerencie seus cartões de crédito que estão listados abaixo.">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <div v-if="creditCards.length === 0" class="col-span-full">
                        <UEmpty
                            variant="subtle"
                            title="Nenhum cartão de crédito encontrado"
                            description="Você ainda não adicionou nenhum cartão de crédito."
                        >
                            <template #actions>
                                <UButton color="primary" size="sm" icon="i-lucide-plus" @click="modalAdd.open()">Novo cartão</UButton>
                            </template>
                        </UEmpty>
                    </div>
                    <template v-else>
                        <AccountItem v-for="(account, i) in creditCards" :key="i" :account="account" />
                    </template>
                </div>
            </UPageCard>
        </template>
    </UDashboardPanel>
</template>

<script setup lang="ts">
import AccountAddModal from '@/Components/AccountAddModal.vue';
import AccountItem from '@/Components/AccountItem.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { AccountResponse, WALLET_TYPE } from '@/types';
import { formatCurrency } from '@/utils';
import { Head } from '@inertiajs/vue3';
import { useOverlay } from '@nuxt/ui/runtime/composables/useOverlay.js';
import { computed } from 'vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps<{
    accounts: AccountResponse | null;
    totalBalance?: number;
    totalCreditLimit?: number;
    totalAvailableLimit?: number;
}>();

const overlay = useOverlay();
const modalAdd = overlay.create(AccountAddModal);
const bankAccounts = computed(() => props.accounts?.filter(({ type }) => type === WALLET_TYPE.CHECKING) ?? []);
const creditCards = computed(() => props.accounts?.filter(({ type }) => type === WALLET_TYPE.CREDIT_CARD) ?? []);
</script>
