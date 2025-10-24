<template>
    <Suspense>
        <UApp>
            <UDashboardGroup unit="rem" storage="local">
                <UDashboardSidebar collapsible v-model:open="open">
                    <template #header>
                        <h1>Finora</h1>
                    </template>
                </UDashboardSidebar>
                <slot></slot>
            </UDashboardGroup>
        </UApp>
    </Suspense>
</template>

<script lang="ts" setup>
import { ref } from 'vue';

const open = ref(false)

</script>