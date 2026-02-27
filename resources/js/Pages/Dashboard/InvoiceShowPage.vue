<template>
    <Head :title="`Fatura de ${formatReferenceDate(invoice.reference_date)}`" />
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar :title="`Fatura de ${formatReferenceDate(invoice.reference_date)}`">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
                <template #right>
                    <UButton color="neutral" variant="ghost" icon="i-lucide-arrow-left" to="/invoices">Voltar</UButton>
                </template>
            </UDashboardNavbar>
        </template>
        <template #body>
            <UCard variant="outline">
                <div class="mb-2 flex items-center justify-between">
                    <p class="text-sm text-muted uppercase">Limite utilizado</p>
                    <p class="text-sm font-medium text-highlighted">
                        {{ formatCurrency(usedLimit) }} de {{ formatCurrency(invoice.wallet.credit_limit) }}
                    </p>
                </div>
                <UProgress :model-value="usedLimitPercentage" :color="progressColor" size="sm" />
            </UCard>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <UCard variant="outline">
                    <p class="mb-1 text-sm text-muted uppercase">Cartão</p>
                    <p class="text-lg font-semibold text-highlighted">{{ invoice.wallet.name }}</p>
                </UCard>
                <UCard variant="outline">
                    <p class="mb-1 text-sm text-muted uppercase">Fechamento</p>
                    <p class="text-lg font-semibold text-highlighted">{{ formatDate(String(invoice.closing_date)) }}</p>
                </UCard>
                <UCard variant="outline">
                    <p class="mb-1 text-sm text-muted uppercase">Vencimento</p>
                    <p class="text-lg font-semibold text-highlighted">{{ formatDate(String(invoice.due_date)) }}</p>
                </UCard>
                <UCard variant="outline">
                    <p class="mb-1 text-sm text-muted uppercase">Status</p>
                    <UBadge :color="statusConfig.color" variant="subtle" class="mt-1">{{ statusConfig.label }}</UBadge>
                </UCard>
            </div>
            <UCard
                variant="subtle"
                :ui="{
                    body: 'flex items-center gap-4',
                }"
            >
                <div class="flex-1">
                    <p class="mb-1 text-sm text-muted uppercase">Total da fatura</p>
                    <p class="flex-1 text-3xl font-bold text-highlighted">{{ formatCurrency(invoice.total) }}</p>
                </div>
                <UButton
                    v-if="invoice.status === INVOICE_STATUS.CLOSED"
                    class="flex-none"
                    size="xl"
                    color="success"
                    icon="i-lucide-circle-dollar-sign"
                    @click="openConfirmPaymentModal"
                    >Pagar fatura</UButton
                >
            </UCard>
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
                <UPagination v-model:page="page" :total="transactions?.total ?? 0" size="lg" @update:page="to" />
            </div>
        </template>
    </UDashboardPanel>
</template>

<script setup lang="ts">
import InvoiceConfirmPaymentModal from '@/Components/InvoiceConfirmPaymentModal.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { CATEGORY_TYPE, Invoice, INVOICE_STATUS, PaginatedResponse, Transaction } from '@/types';
import { formatCurrency } from '@/utils';
import { Head, router } from '@inertiajs/vue3';
import { parseDate } from '@internationalized/date';
import type { TableColumn } from '@nuxt/ui';
import { useOverlay } from '@nuxt/ui/runtime/composables/useOverlay.js';
import { computed, h, ref, resolveComponent } from 'vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps<{
    invoice: Invoice;
    transactions: PaginatedResponse<Transaction> | null;
}>();

const overlay = useOverlay();
const UBadge = resolveComponent('UBadge');
const modalPayInvoice = overlay.create(InvoiceConfirmPaymentModal);

const page = ref(props.transactions?.current_page || 1);

const statusMap: Record<INVOICE_STATUS, { label: string; color: 'success' | 'warning' | 'info' }> = {
    [INVOICE_STATUS.OPEN]: { label: 'Aberta', color: 'info' },
    [INVOICE_STATUS.CLOSED]: { label: 'Fechada', color: 'warning' },
    [INVOICE_STATUS.PAID]: { label: 'Paga', color: 'success' },
};

const statusConfig = computed(() => statusMap[props.invoice.status] ?? { label: props.invoice.status, color: 'neutral' });

const usedLimit = computed(() => props.invoice.wallet.credit_limit - props.invoice.wallet.available_limit);

const usedLimitPercentage = computed(() => (usedLimit.value / props.invoice.wallet.credit_limit) * 100);

const progressColor = computed(() => {
    let color = 'primary';

    if (usedLimitPercentage.value > 75) {
        color = 'warning';
    }

    if (usedLimitPercentage.value > 95) {
        color = 'error';
    }

    return color;
});

function formatReferenceDate(date: Date | number | string): string {
    const formatted = new Intl.DateTimeFormat('pt-BR', { month: 'long', year: 'numeric' }).format(new Date(date as string));
    return `${formatted[0].toUpperCase()}${formatted.slice(1).toLowerCase()}`;
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
        cell: ({ row }) => formatCurrency(row.getValue('amount') as number),
    },
    {
        accessorKey: 'category',
        header: 'Categoria',
        meta: {
            class: {
                td: 'w-[15%]',
            },
        },
        cell: ({ row }) =>
            h('div', { class: 'flex items-center gap-2' }, [
                h('div', {
                    class: 'h-5 w-5 rounded',
                    style: `background-color: ${row.original.category.color}`,
                }),
                h('span', row.original.category.name),
            ]),
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
];

function to(pageNumber: number) {
    const link = props.transactions?.links[pageNumber];
    if (link?.active === false && link?.url) {
        router.get(link.url, {});
    }
}

const openConfirmPaymentModal = () =>
    modalPayInvoice.open({
        invoice: props.invoice,
    });
</script>
