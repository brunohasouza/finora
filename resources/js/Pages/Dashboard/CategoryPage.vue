<template>
    <Head title="Categorias" />
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar title="Categorias">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
                <template #right>
                    <UButton color="primary" size="sm" icon="i-lucide-plus" @click="modalAdd.open()">Nova categoria</UButton>
                </template>
            </UDashboardNavbar>
        </template>
        <template #body>
            <UTable
                empty="Nenhuma categoria encontrada"
                :data="categories || []"
                :columns="columns"
                :ui="{
                    base: 'table-fixed border-separate border-spacing-0',
                    thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
                    tbody: '[&>tr]:last:[&>td]:border-b-0 [&>tr]:hover:bg-elevated/50',
                    th: 'py-2 first:rounded-l-lg last:rounded-r-lg border-y border-default first:border-l last:border-r',
                    td: 'border-b border-default',
                    separator: 'h-0',
                }"
            />
        </template>
    </UDashboardPanel>
</template>

<script setup lang="ts">
import CategoryAddModal from '@/Components/CategoryAddModal.vue';
import CategoryDeleteModal from '@/Components/CategoryDeleteModal.vue';
import { colors } from '@/constants';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Category, CATEGORY_TYPE } from '@/types';
import { faker } from '@faker-js/faker';
import { Head } from '@inertiajs/vue3';
import type { TableColumn } from '@nuxt/ui';
import { useOverlay } from '@nuxt/ui/runtime/composables/useOverlay.js';
import { h, resolveComponent } from 'vue';

defineOptions({
    layout: DashboardLayout,
});

const overlay = useOverlay();
const modalAdd = overlay.create(CategoryAddModal);
const modalDelete = overlay.create(CategoryDeleteModal); // Placeholder for CategoryDeleteModal
const UBadge = resolveComponent('UBadge');
const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');

const categories: Category[] = Array.from({ length: faker.number.int({ min: 10, max: 20 }) }, () => ({
    id: faker.number.int(),
    name: faker.commerce.department(),
    type: faker.helpers.enumValue(CATEGORY_TYPE),
    color: faker.helpers.arrayElement(colors),
    created_at: faker.date.past().toISOString(),
    updated_at: faker.date.recent().toISOString(),
}));

const columns: TableColumn<Category>[] = [
    {
        accessorKey: 'name',
        header: 'Nome',
        cell: ({ row }) => row.getValue('name'),
    },
    {
        accessorKey: 'color',
        header: 'Cor',
        meta: {
            class: {
                td: 'w-[15%]',
            },
        },
        cell: ({ row }) => {
            const color = row.getValue('color');
            return h('div', {
                class: 'h-6 w-full max-w-[4rem] rounded-md',
                style: `background-color: ${color}`,
            });
        },
    },
    {
        accessorKey: 'type',
        header: 'Tipo',
        meta: {
            class: {
                td: 'w-[15%]',
            },
        },
        cell: ({ row }) => {
            const type = row.getValue('type');
            if (type === CATEGORY_TYPE.INCOME) {
                return h(UBadge, { color: 'info', variant: 'outline' }, () => 'Receita');
            }

            if (type === CATEGORY_TYPE.EXPENSE) {
                return h(UBadge, { color: 'error', variant: 'outline' }, () => 'Despesa');
            }

            return '';
        },
    },
    {
        id: 'actions',
        meta: {
            class: {
                td: 'w-16',
            },
        },
        cell: ({ row }) =>
            h(
                'div',
                { class: 'text-right' },
                h(
                    UDropdownMenu,
                    {
                        content: { align: 'end' },
                        items: [
                            {
                                label: 'Editar categoria',
                                icon: 'i-lucide-pen',
                                color: 'neutral',
                                onSelect: () => {
                                    modalAdd.open({
                                        category: row.original,
                                    });
                                },
                            },
                            {
                                label: 'Excluir categoria',
                                icon: 'i-lucide-trash',
                                color: 'error',
                                onSelect: () => {
                                    modalDelete.open({
                                        categoryId: row.original.id,
                                        categoryName: row.original.name,
                                    });
                                },
                            },
                        ],
                    },
                    () =>
                        h(UButton, {
                            icon: 'i-lucide-ellipsis-vertical',
                            color: 'neutral',
                            variant: 'ghost',
                            class: 'ml-auto',
                        }),
                ),
            ),
    },
];
</script>

<style scoped></style>
