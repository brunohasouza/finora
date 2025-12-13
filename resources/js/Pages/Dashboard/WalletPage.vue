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
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                {{ accounts }}
            </div>
        </template>
    </UDashboardPanel>
</template>

<script setup lang="ts">
import AccountAddModal from '@/Components/AccountAddModal.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { AccountResponse } from '@/types';
import { useOverlay } from '@nuxt/ui/runtime/composables/useOverlay.js';

defineOptions({
    layout: DashboardLayout,
});

defineProps<{
    accounts: AccountResponse | null;
}>();

const overlay = useOverlay();
const modalAdd = overlay.create(AccountAddModal);
</script>
