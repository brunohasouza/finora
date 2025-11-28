<template>
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar :title="`Olá, ${userName}`">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
            </UDashboardNavbar>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { PageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({
    layout: DashboardLayout,
});

const page = usePage<PageProps>();
const userName = computed(() => page.props!.auth!.user!.full_name);
</script>
