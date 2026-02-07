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
        <template #body>
            <div class="flex flex-wrap items-center gap-4">
                <UCard variant="subtle" class="flex-2">
                    <div class="space-y-3">
                        <p class="text-base font-normal opacity-70">Saldo Total</p>
                        <p class="text-3xl font-bold">{{ formatCurrency(balance?.total ?? 0) }}</p>
                    </div>
                </UCard>
                <UCard variant="outline" class="flex-1">
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-5 w-5 rounded bg-info"></span>
                            <p class="text-base font-medium opacity-70">Receitas</p>
                        </div>
                        <p class="text-xl font-bold">{{ formatCurrency(balance?.incomes ?? 0) }}</p>
                    </div>
                </UCard>
                <UCard variant="outline" class="flex-1">
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-5 w-5 rounded bg-error"></span>
                            <p class="text-base font-medium opacity-70">Despesas</p>
                        </div>
                        <p class="text-xl font-bold">{{ formatCurrency(balance?.expenses ?? 0) }}</p>
                    </div>
                </UCard>
            </div>
            <div class="flex items-center justify-end gap-4">
                <USelect v-model="typeFilter" :items="types" :ui="{ base: 'w-48' }" @update:modelValue="search" />
                <USelect v-model="categoryFilter" :items="categoryOptions" :ui="{ base: 'w-48' }" @update:modelValue="search" />
            </div>
            <UTable
                empty="Nenhuma transação encontrada"
                :data="transactions?.data || []"
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
                <UPagination v-model:page="currentPage" :total="transactions?.total ?? 0" size="lg" @update:page="to" />
            </div>
        </template>
    </UDashboardPanel>
</template>

<script lang="ts" setup>
import TransactionAddModal from '@/Components/TransactionAddModal.vue';
import TransactionDeleteModal from '@/Components/TransactionDeleteModal.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Balance, Category, CATEGORY_TYPE, PageProps, Transaction, TransactionResponse } from '@/types';
import { formatCurrency } from '@/utils';
import { router, usePage } from '@inertiajs/vue3';
import { parseDate } from '@internationalized/date';
import type { TableColumn } from '@nuxt/ui';
import { useOverlay } from '@nuxt/ui/runtime/composables/useOverlay.js';
import { computed, h, onMounted, ref, resolveComponent } from 'vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps<{
    transactions: TransactionResponse | null;
    type: CATEGORY_TYPE | null;
    category_id: string | null;
    balance: Balance | null;
}>();

const overlay = useOverlay();
const modalAdd = overlay.create(TransactionAddModal);
const modalDelete = overlay.create(TransactionDeleteModal);

const page = usePage<PageProps>();
const userName = computed(() => page.props!.auth!.user!.full_name);

const UBadge = resolveComponent('UBadge');
const UButton = resolveComponent('UButton');
const UDropdownMenu = resolveComponent('UDropdownMenu');

const currentPage = ref(props.transactions?.current_page || 1);
const typeFilter = ref(props.type ?? 'all');
const categoryFilter = ref(props.category_id ?? 'all');
const categoriesData = ref<Category[]>([]);

const types = [
    { label: 'Todos os tipos', value: 'all' },
    { label: 'Receita', value: CATEGORY_TYPE.INCOME },
    { label: 'Despesa', value: CATEGORY_TYPE.EXPENSE },
];

const categoryOptions = computed(() => [
    { label: 'Todas as categorias', value: 'all' },
    ...categoriesData.value
        .filter((cat) => (typeFilter.value === 'all' ? true : cat.type === typeFilter.value))
        .map((cat) => ({ label: cat.name, value: String(cat.id) })),
]);

async function fetchCategories() {
    try {
        const response = await fetch('/categories/list');
        categoriesData.value = await response.json();
    } catch (error) {
        console.error('Erro ao carregar categorias:', error);
    }
}

const search = () => {
    const selectedCategory = categoriesData.value.find((cat) => String(cat.id) === categoryFilter.value);
    const categoryId =
        categoryFilter.value !== 'all' && (typeFilter.value === selectedCategory?.type || typeFilter.value === 'all')
            ? categoryFilter.value
            : undefined;

    router.get(props.transactions?.path ?? '/', {
        page: 1,
        type: typeFilter.value !== 'all' ? typeFilter.value : undefined,
        category_id: categoryId,
    });
};

onMounted(() => {
    fetchCategories();
});

function to(pageNumber: number) {
    const link = props.transactions?.links[pageNumber];

    if (link?.active === false && link?.url) {
        router.get(link.url, {});
    }
}

function formatDate(dateString: string): string {
    const { year, month, day } = parseDate(dateString);
    return new Date(year, month - 1, day).toLocaleDateString('pt-BR');
}

const columns: TableColumn<Transaction>[] = [
    {
        accessorKey: 'description',
        header: 'Descrição',
        cell: ({ row }) => row.getValue('description'),
    },
    {
        accessorKey: 'type',
        header: 'Tipo',
        meta: {
            class: {
                td: 'w-[6.25rem]',
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
        accessorKey: 'amount',
        header: 'Valor',
        meta: {
            class: {
                td: 'w-[15%]',
            },
        },
        cell: ({ row }) => {
            const amount = row.getValue('amount') as number;
            return formatCurrency(amount);
        },
    },
    {
        accessorKey: 'category',
        header: 'Categoria',
        meta: {
            class: {
                td: 'w-[15%]',
            },
        },
        cell: ({ row }) => {
            return h(
                'div',
                {
                    class: 'flex items-center gap-2',
                },
                [
                    h('div', {
                        class: 'h-5 w-5 rounded',
                        style: `background-color: ${row.original.category.color}`,
                    }),
                    h('span', row.original.category.name),
                ],
            );
        },
    },
    {
        accessorKey: 'date',
        header: 'Data',
        meta: {
            class: {
                td: 'w-28',
            },
        },
        cell: ({ row }) => formatDate(row.getValue('date')),
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
                                label: 'Editar transação',
                                icon: 'i-lucide-pen',
                                color: 'neutral',
                                onSelect: () => {
                                    modalAdd.open({
                                        transaction: row.original,
                                    });
                                },
                            },
                            {
                                type: 'separator',
                            },
                            {
                                label: 'Excluir transação',
                                icon: 'i-lucide-trash',
                                color: 'error',
                                onSelect: () => {
                                    modalDelete.open({
                                        transactionId: row.original.id,
                                        transactionDescription: row.original.description,
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
