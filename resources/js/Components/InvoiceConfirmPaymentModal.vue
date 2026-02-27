<template>
    <UModal
        title="Pagar fatura"
        description="Tem certeza que deseja efetuar o pagamento desta fatura?"
        :close="{ onClick: () => emits('close', false) }"
        :ui="{ footer: 'justify-end', title: 'text-primary' }"
    >
        <template #body>
            <p class="mb-4 text-neutral-300">
                Ao pagar esta fatura, uma transação no valor de <strong class="text-primary">{{ formatCurrency(invoice.total) }}</strong> será criada
                automaticamente na conta selecionada.
            </p>
            <UCard
                variant="outline"
                class="mb-2"
                :ui="{
                    body: 'flex items-center gap-4 sm:p-4',
                }"
            >
                <p class="w-20 flex-none">Valor:</p>
                <p class="flex-1">{{ formatCurrency(invoice.total) }}</p>
            </UCard>
            <UCard
                variant="outline"
                class="mb-2"
                :ui="{
                    body: 'flex items-center gap-4 sm:p-4',
                }"
            >
                <p class="w-20 flex-none">Cartão:</p>
                <p class="flex-1">{{ invoice.wallet.name }}</p>
            </UCard>
            <UCard
                variant="outline"
                class="mb-4"
                :ui="{
                    body: 'flex items-center gap-4 sm:p-4',
                }"
            >
                <p class="w-20 flex-none">Fatura:</p>
                <p class="flex-1">{{ formatReferenceDate }}</p>
            </UCard>
            <UFormField label="Débitar da conta" name="wallet_id" class="mb-4">
                <USelect
                    v-model="form.wallet_id"
                    :items="walletOptions"
                    :loading="loadingWallets"
                    placeholder="Selecione uma conta corrente..."
                    :ui="{ base: 'w-full' }"
                />
            </UFormField>
            <p class="text-neutral-300">Você confirma o pagamento?</p>
            <UAlert v-if="errorMessage" :description="errorMessage" icon="i-lucide-x-circle" color="error" class="mt-5" />
        </template>
        <template #footer>
            <UButton
                color="neutral"
                size="sm"
                variant="outline"
                icon="i-lucide-x"
                :disabled="form.processing"
                @click="emits('close', false)"
                >Não</UButton
            >
            <UButton color="primary" size="sm" icon="i-lucide-check" :loading="form.processing" @click="confirmPayment">Sim</UButton>
        </template>
    </UModal>
</template>

<script setup lang="ts">
import { Account, Invoice, WALLET_TYPE } from '@/types';
import { formatCurrency } from '@/utils';
import { useForm } from '@inertiajs/vue3';
import { useToast } from '@nuxt/ui/runtime/composables/useToast.js';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    invoice: Invoice;
}>();

const emits = defineEmits<{ close: [boolean] }>();

const toast = useToast();
const loadingWallets = ref(false);
const accounts = ref<Account[]>([]);
const errorMessage = ref('');

const form = useForm({ wallet_id: '' });

const walletOptions = computed(() =>
    accounts.value
        .filter((a) => a.type === WALLET_TYPE.CHECKING)
        .map((a) => ({ label: a.name, value: String(a.id) })),
);

const formatReferenceDate = computed(() => {
    const referenceDate = new Intl.DateTimeFormat('pt-BR', { month: 'long', year: 'numeric' }).format(
        new Date(props.invoice.reference_date as string),
    );
    return `${referenceDate[0].toUpperCase()}${referenceDate.slice(1).toLowerCase()}`;
});

async function fetchAccounts() {
    loadingWallets.value = true;
    try {
        const response = await fetch('/accounts/list');
        accounts.value = (await response.json()) as Account[];
    } catch {
        errorMessage.value = 'Erro ao carregar as contas. Tente novamente.';
    } finally {
        loadingWallets.value = false;
    }
}

function confirmPayment() {
    if (!form.wallet_id) {
        errorMessage.value = 'Selecione uma conta corrente para débito.';
        return;
    }

    errorMessage.value = '';

    form.post(`/invoices/${props.invoice.id}/pay`, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            emits('close', true);
            toast.add({
                title: 'Fatura paga com sucesso!',
                description: `Fatura ${formatReferenceDate.value} foi marcada como paga.`,
                color: 'success',
                icon: 'i-lucide-circle-check',
            });
        },
        onError: (errors) => {
            errorMessage.value = (Object.values(errors)[0] as string) || 'Erro ao pagar fatura.';
        },
    });
}

onMounted(fetchAccounts);
</script>
