<template>
    <Suspense>
        <UApp>
            <UDashboardGroup unit="rem" storage="local">
                <UDashboardSidebar collapsible v-model:open="open" :ui="{ footer: 'border-t border-default', header: 'border-b border-default' }">
                    <template #header="{ collapsed }">
                        <img :src="collapsed ? '/images/finora-icon.png' : '/images/finora-logo-white.png'" class="ml-1 h-6 w-auto" />
                    </template>
                    <template #default="{ collapsed }">
                        <UNavigationMenu :items="items" :collapsed="collapsed" orientation="vertical" :ui="{ list: 'space-y-1' }" />
                    </template>
                    <template #footer="{ collapsed }">
                        <!-- <Link href="/logout" method="post" as="button" type="button">Sair</Link> -->
                        <UserMenu :collapsed="collapsed" />
                    </template>
                </UDashboardSidebar>
                <slot></slot>
            </UDashboardGroup>
        </UApp>
    </Suspense>
</template>

<script lang="ts" setup>
import UserMenu from '@/Components/UserMenu.vue';
import type { NavigationMenuItem } from '@nuxt/ui';
import { ref } from 'vue';

const open = ref(false);
const items: NavigationMenuItem[] = [
    {
        label: 'Home',
        icon: 'i-lucide-home',
        to: '/',
        exact: true,
    },
    {
        label: 'Contas',
        icon: 'i-lucide-wallet',
        to: '/accounts',
    },
    {
        label: 'Categorias',
        icon: 'i-lucide-tag',
        to: '/categories',
    },
];
</script>
