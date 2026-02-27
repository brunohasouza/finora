<template>
    <Head title="Faturas" />
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar title="Faturas">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
            </UDashboardNavbar>
        </template>
        <template #body>
            <UTable
                empty="Nenhuma fatura encontrada"
                :data="invoices?.data || []"
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
                <UPagination v-model:page="page" :total="invoices?.total ?? 0" size="lg" @update:page="to" />
            </div>
        </template>
    </UDashboardPanel>
</template>

<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Invoice, INVOICE_STATUS, PaginatedResponse } from '@/types';
import { formatCurrency } from '@/utils';
import { Head, router } from '@inertiajs/vue3';
import type { TableColumn } from '@nuxt/ui';
import { h, ref, resolveComponent } from 'vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps<{
    invoices: PaginatedResponse<Invoice> | null;
}>();

const UBadge = resolveComponent('UBadge');
const UButton = resolveComponent('UButton');

const page = ref(props.invoices?.current_page || 1);

function formatReferenceDate(date: Date | number | string): string {
    const referenceDate = new Intl.DateTimeFormat('pt-BR', { month: 'long', year: 'numeric' }).format(new Date(date as string));
    return `${referenceDate[0].toUpperCase()}${referenceDate.slice(1).toLowerCase()}`;
}

const statusMap: Record<INVOICE_STATUS, { label: string; color: 'success' | 'warning' | 'info' }> = {
    [INVOICE_STATUS.OPEN]: { label: 'Aberta', color: 'info' },
    [INVOICE_STATUS.CLOSED]: { label: 'Fechada', color: 'warning' },
    [INVOICE_STATUS.PAID]: { label: 'Paga', color: 'success' },
};

const columns: TableColumn<Invoice>[] = [
    {
        id: 'wallet',
        header: 'Cartão',
        cell: ({ row }) => row.original.wallet.name,
    },
    {
        accessorKey: 'reference_date',
        meta: {
            class: {
                td: 'w-[15%]',
            },
        },
        header: 'Mês/Ano',
        cell: ({ row }) => formatReferenceDate(row.getValue('reference_date')),
    },
    {
        accessorKey: 'status',
        meta: {
            class: {
                td: 'w-[15%]',
            },
        },
        header: 'Status',
        cell: ({ row }) => {
            const status = row.getValue('status') as INVOICE_STATUS;
            const { label, color } = statusMap[status] ?? { label: status, color: 'neutral' };
            return h(UBadge, { color, variant: 'subtle' }, () => label);
        },
    },
    {
        accessorKey: 'total',
        meta: {
            class: {
                td: 'w-[15%] ',
            },
        },
        header: 'Valor',
        cell: ({ row }) => formatCurrency(row.getValue('total')),
    },
    {
        id: 'actions',
        meta: {
            class: {
                td: 'w-24',
            },
        },
        cell: ({ row }) =>
            h(UButton, {
                icon: 'i-lucide-eye',
                color: 'neutral',
                variant: 'outline',
                label: 'Ver',
                size: 'sm',
                to: `/invoices/${row.original.id}`,
                // onClick: () => router.visit(`/invoices/${row.original.id}`),
            }),
    },
];

function to(pageNumber: number) {
    const link = props.invoices?.links[pageNumber];
    if (link?.active === false && link?.url) {
        router.get(link.url, {});
    }
}
</script>
