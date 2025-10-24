<template>
    <UDashboardPanel>
        <template #body>
            <h1>Hello World</h1>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
defineOptions({
    layout: DashboardLayout,
})
</script>
