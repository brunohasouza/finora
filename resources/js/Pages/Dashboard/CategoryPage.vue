<template>
    <Head title="Categorias" />
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar title="Categorias">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
                <template #right>
                    <UButton color="primary" size="sm" icon="i-lucide-plus">Nova Categoria</UButton>
                </template>
            </UDashboardNavbar>
        </template>
    </UDashboardPanel>
</template>

<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: DashboardLayout,
});
</script>

<style scoped></style>
