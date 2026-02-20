<template>
    <UModal
        :dismissable="state.processing"
        :title="title"
        :description="description"
        :close="{ onClick: () => emits('close', false) }"
        :ui="{ footer: 'justify-end' }"
    >
        <template #body>
            <UForm ref="form" :state="state" :schema="schema" class="space-y-4" @submit="onSubmit">
                <UFormField label="Nome" name="name">
                    <UInput v-model="state.name" placeholder="Ex: Conta principal" type="text" :ui="{ root: 'w-full' }" />
                </UFormField>
                <UFormField label="Tipo" name="type">
                    <USelect
                        v-model="state.type"
                        :items="walletTypes"
                        :ui="{ base: 'w-full' }"
                        placeholder="Selecione o tipo"
                        :disabled="isEditing"
                    />
                </UFormField>
                <UFormField label="Banco" name="bank">
                    <USelectMenu
                        v-model="state.bank_id"
                        :items="banks"
                        :search-input="{ placeholder: 'Ex: Banco do Brasil' }"
                        value-key="value"
                        placeholder="Escolha um banco"
                        class="w-full"
                    />
                </UFormField>
                <UFormField v-if="state.type === WALLET_TYPE.CHECKING" label="Saldo inicial" name="balance">
                    <UInput v-model="localBalance" placeholder="R$ 0,00" type="text" :ui="{ root: 'w-full' }" />
                </UFormField>
                <template v-if="state.type === WALLET_TYPE.CREDIT_CARD">
                    <UFormField label="Limite" name="credit_limit">
                        <UInput v-model="localCreditLimit" placeholder="R$ 0,00" type="text" :ui="{ root: 'w-full' }" />
                    </UFormField>
                    <div class="grid grid-cols-2 gap-4">
                        <UFormField label="Dia de fechamento" name="closing_day">
                            <UInput v-model.number="state.closing_day" placeholder="Ex: 25" type="number" min="1" max="31" :ui="{ root: 'w-full' }" />
                        </UFormField>
                        <UFormField label="Dia de vencimento" name="due_day">
                            <UInput v-model.number="state.due_day" placeholder="Ex: 5" type="number" min="1" max="31" :ui="{ root: 'w-full' }" />
                        </UFormField>
                    </div>
                </template>
            </UForm>
            <UAlert v-if="errorMessage" :description="errorMessage" icon="i-lucide-x-circle" color="error" class="mt-5" />
        </template>
        <template #footer>
            <UButton color="neutral" variant="outline" size="sm" @click="emits('close', false)" :disabled="state.processing" icon="i-lucide-x"
                >Cancelar</UButton
            >
            <UButton color="primary" size="sm" icon="i-lucide-check" @click="form.submit()" :disabled="state.processing">Salvar</UButton>
        </template>
    </UModal>
</template>

<script setup lang="ts">
import { Account, Bank, WALLET_TYPE } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { FormSubmitEvent } from '@nuxt/ui';
import { useToast } from '@nuxt/ui/runtime/composables/useToast.js';
import { Mask } from 'maska';
import { computed, onMounted, ref, useTemplateRef } from 'vue';
import * as y from 'yup';

const props = defineProps<{
    account?: Account;
}>();

const MAX_BALANCE = 100000000000;
const isEditing = !!props.account;
const title = isEditing ? 'Editar conta' : 'Nova conta';
const description = isEditing ? `Edite os detalhes da conta '${props.account!.name}'.` : 'Adicione uma nova conta para suas transações.';
const mask = new Mask({
    mask: '9.99#,##',
    reversed: true,
    tokens: {
        9: {
            pattern: /[0-9]/,
            repeated: true,
        },
    },
});

const walletTypes = [
    { label: 'Conta corrente', value: WALLET_TYPE.CHECKING },
    { label: 'Cartão de crédito', value: WALLET_TYPE.CREDIT_CARD },
];

const schema = y.object({
    name: y.string().required(),
    type: y.string().required(),
    bank_id: y.number().required(),
    balance: y
        .number()
        .min(0, 'O saldo não pode ser negativo')
        .max(MAX_BALANCE, `O saldo máximo é de R$ ${mask.masked(MAX_BALANCE)}`)
        .default(0),
    credit_limit: y.number().min(0, 'O limite não pode ser negativo').default(0),
    closing_day: y.number().min(1, 'Mínimo 1').max(31, 'Máximo 31').default(0),
    due_day: y.number().min(1, 'Mínimo 1').max(31, 'Máximo 31').default(0),
});

type Schema = y.InferType<typeof schema>;

const emits = defineEmits<{ close: [boolean] }>();

const form = useTemplateRef('form');
const toast = useToast();
const state = useForm<Schema>({
    name: props.account?.name ?? '',
    type: props.account?.type ?? WALLET_TYPE.CHECKING,
    bank_id: typeof props.account?.bank?.id === 'number' ? props.account.bank.id : 0,
    balance: props.account?.balance ?? 0,
    credit_limit: props.account?.credit_limit ?? 0,
    closing_day: props.account?.closing_day ?? 0,
    due_day: props.account?.due_day ?? 0,
});

const loading = ref(false);
const banksData = ref<Bank[]>([]);
const errorMessage = ref('');
const banks = computed(() =>
    banksData.value.map((bank) => ({
        label: `${bank.code} - ${bank.name}`,
        value: bank.id,
    })),
);

const localBalance = computed({
    get: () => {
        return `R$ ${mask.masked(state.balance)}`;
    },
    set: (value: string) => {
        state.balance = Number(mask.unmasked(value)) || 0;
    },
});

const localCreditLimit = computed({
    get: () => {
        return `R$ ${mask.masked(state.credit_limit)}`;
    },
    set: (value: string) => {
        state.credit_limit = Number(mask.unmasked(value)) || 0;
    },
});

async function fetchBanks() {
    loading.value = true;

    try {
        const response = await fetch('/banks');
        banksData.value = await response.json();
    } catch (error) {
        errorMessage.value = 'Erro ao carregar os bancos. Tente novamente.';
    } finally {
        loading.value = false;
    }
}

function onSubmit(e: FormSubmitEvent<Schema>) {
    const url = isEditing ? `/accounts/${props.account!.id}` : '/accounts';
    const successDesc = isEditing ? 'Conta atualizada com sucesso.' : 'Conta criada com sucesso.';
    const method = isEditing ? 'put' : 'post';

    state.submit(method, url, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: () => {
            emits('close', false);

            toast.add({
                title: 'Sucesso',
                description: successDesc,
                color: 'success',
                icon: 'i-lucide-check-circle',
            });
        },
        onError: (errors) => {
            console.error(errors);
            errorMessage.value = 'Ocorreu um erro ao salvar a conta.';
        },
    });
}

onMounted(() => fetchBanks());
</script>
