<template>
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar :title="`Olá, ${userName}`">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
                <template #right>
                    <UButton color="primary" size="sm" icon="i-lucide-plus" @click="modalAdd.open()">Nova transação</UButton>
                </template>
            </UDashboardNavbar>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import TransactionAddModal from '@/Components/TransactionAddModal.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { PageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { useOverlay } from '@nuxt/ui/runtime/composables/useOverlay.js';
import { computed } from 'vue';

defineOptions({
    layout: DashboardLayout,
});

const overlay = useOverlay();
const modalAdd = overlay.create(TransactionAddModal);

const page = usePage<PageProps>();
const userName = computed(() => page.props!.auth!.user!.full_name);
</script>
