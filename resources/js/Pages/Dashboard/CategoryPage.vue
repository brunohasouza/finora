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
            <div class="flex items-center justify-end">
                <USelect v-model="type" :items="types" :ui="{ base: 'w-48 mr-4' }" @update:modelValue="search" />
                <UForm :state="form" @submit="search">
                    <UInput v-model="form.search" class="max-w-sm" type="search" icon="i-lucide-search" placeholder="Procurar categoria..." />
                </UForm>
            </div>
            <UTable
                empty="Nenhuma categoria encontrada"
                :data="categories?.data || []"
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
            <div class="mt-4 flex justify-end">
                <UPagination v-model:page="page" :total="categories?.total ?? 0" size="lg" @update:page="to" />
            </div>
        </template>
    </UDashboardPanel>
</template>

<script setup lang="ts">
import CategoryAddModal from '@/Components/CategoryAddModal.vue';
import CategoryDeleteModal from '@/Components/CategoryDeleteModal.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Category, CATEGORY_TYPE, CategoryResponse } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import type { TableColumn } from '@nuxt/ui';
import { useOverlay } from '@nuxt/ui/runtime/composables/useOverlay.js';
import { h, ref, resolveComponent } from 'vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps<{
    categories: CategoryResponse | null;
    searchTerm: string | null;
    typeFilter: CATEGORY_TYPE | null;
}>();

const overlay = useOverlay();
const modalAdd = overlay.create(CategoryAddModal);
const modalDelete = overlay.create(CategoryDeleteModal);
const UBadge = resolveComponent('UBadge');
const UDropdownMenu = resolveComponent('UDropdownMenu');
const UButton = resolveComponent('UButton');

const page = ref(props.categories?.current_page || 1);
const form = useForm({
    search: props.searchTerm,
});
const type = ref(props.typeFilter ?? 'all');
const types = [
    { label: 'Todas', value: 'all' },
    { label: 'Receita', value: CATEGORY_TYPE.INCOME },
    { label: 'Despesa', value: CATEGORY_TYPE.EXPENSE },
];

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
                                type: 'separator',
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

function to(pageNumber: number) {
    const link = props.categories?.links[pageNumber];

    if (link?.active === false && link?.url) {
        router.get(link.url, {});
    }
}

const search = () => {
    router.get(props.categories?.path ?? '', {
        search: form.search || undefined,
        page: 1,
        type: type.value === CATEGORY_TYPE.INCOME || type.value === CATEGORY_TYPE.EXPENSE ? type.value : undefined,
    });
};
</script>

<style scoped></style>
