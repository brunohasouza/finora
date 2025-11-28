<template>
    <UDropdownMenu
        :items="items"
        :content="{ align: 'center', collisionPadding: 12 }"
        :ui="{ content: collapsed ? 'w-48' : 'w-(--reka-dropdown-menu-trigger-width)' }"
    >
        <UButton
            v-bind="{
                ...user,
                label: collapsed ? undefined : user?.first_name,
                trailingIcon: collapsed ? undefined : 'i-lucide-chevrons-up-down',
            }"
            :avatar="{
                text: user?.first_name[0] ?? '',
            }"
            color="neutral"
            variant="ghost"
            block
            :square="collapsed"
            class="data-[state=open]:bg-elevated"
            :ui="{
                trailingIcon: 'text-dimmed',
            }"
        />
        <template #item="{ item }">
            <Link
                :href="item.href"
                :method="item.method"
                :type="item.label === 'Sair' ? 'button' : 'a'"
                class="flex w-full cursor-pointer items-center gap-1.5 font-medium text-white"
            >
                <UIcon :name="item.icon" />
                {{ item.label }}
            </Link>
        </template>
    </UDropdownMenu>
</template>

<script setup lang="ts">
import { PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { DropdownMenuItem } from '@nuxt/ui';
import { computed } from 'vue';

defineProps<{
    collapsed?: boolean;
}>();

const page = usePage<PageProps>();

const items = computed<DropdownMenuItem[]>(() => [
    {
        label: 'Sair',
        icon: 'i-lucide-log-out',
        href: '/logout',
        method: 'post',
    },
]);
const user = computed(() => page.props.auth?.user);
</script>

<style scoped></style>
