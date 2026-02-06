<template>
    <UModal
        :dismissable="state.processing"
        title="Nova transação"
        description="Adicione uma nova transação."
        :close="{ onClick: () => emits('close', false) }"
        :ui="{ footer: 'justify-end' }"
    >
        <template #body>
            <UForm ref="form" :state="state" :schema="schema" class="grid grid-cols-2 gap-4" @submit="onSubmit">
                <UFormField label="Descrição" name="description" class="col-span-2">
                    <UInput v-model="state.description" placeholder="Ex: Compras do mês" type="text" :ui="{ root: 'w-full' }" />
                </UFormField>
                <UFormField label="Valor" name="amount">
                    <UInput v-model="localAmount" placeholder="R$ 0,00" type="text" :ui="{ root: 'w-full' }" />
                </UFormField>
                <UFormField label="Data" name="date">
                    <UInputDate v-model="transactionDate" :ui="{ root: 'w-full' }" @update:model-value="onDateChange" />
                </UFormField>
                <UFormField label="Tipo" name="type">
                    <USelect
                        v-model="state.type"
                        :items="types"
                        :ui="{ base: 'w-full' }"
                        placeholder="Selecione o tipo"
                        @update:model-value="onTypeChange"
                    />
                </UFormField>
                <UFormField label="Categoria" name="category_id">
                    <USelectMenu
                        v-model="localCategoryId"
                        :items="filteredCategories"
                        :search-input="{ placeholder: 'Buscar categoria...' }"
                        value-key="value"
                        placeholder="Selecione a categoria"
                        class="w-full"
                        :disabled="!state.type"
                    />
                </UFormField>
                <UFormField label="Conta" name="wallet_id" class="col-span-2">
                    <USelect v-model="localAccountId" :items="accountOptions" :ui="{ base: 'w-full' }" placeholder="Selecione a conta" />
                </UFormField>
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
import { Account, Category, CATEGORY_TYPE } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { CalendarDate, getLocalTimeZone, today } from '@internationalized/date';
import { FormSubmitEvent, SelectItem } from '@nuxt/ui';
import { useToast } from '@nuxt/ui/runtime/composables/useToast.js';
import { Mask } from 'maska';
import { computed, onMounted, ref, useTemplateRef } from 'vue';
import * as y from 'yup';

const emits = defineEmits<{ close: [boolean] }>();

const MAX_AMOUNT = 100000000000;
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

const schema = y.object({
    description: y.string().required(),
    amount: y
        .number()
        .required()
        .min(1, 'O valor deve ser maior que zero')
        .max(MAX_AMOUNT, `O valor máximo é de R$ ${mask.masked(MAX_AMOUNT)}`),
    type: y.string().required(),
    category_id: y.number().required().min(1, 'Este campo é obrigatório'),
    wallet_id: y.number().required().min(1, 'Este campo é obrigatório'),
    date: y.string().required(),
});

type Schema = y.InferType<typeof schema>;

const form = useTemplateRef('form');
const toast = useToast();
const transactionDate = ref<CalendarDate>(today(getLocalTimeZone()));

const state = useForm<Schema>({
    description: '',
    amount: 0,
    type: '',
    category_id: 0,
    wallet_id: 0,
    date: today(getLocalTimeZone()).toString(),
});

const types = ref<SelectItem[]>([
    { label: 'Receita', value: CATEGORY_TYPE.INCOME },
    { label: 'Despesa', value: CATEGORY_TYPE.EXPENSE },
]);

const loading = ref(false);
const categoriesData = ref<Category[]>([]);
const accountsData = ref<Account[]>([]);
const errorMessage = ref('');

const filteredCategories = computed(() =>
    categoriesData.value
        .filter((category) => category.type === state.type)
        .map((category) => ({
            label: category.name,
            value: category.id,
        })),
);

const accountOptions = computed(() =>
    accountsData.value.map((account) => ({
        label: account.name,
        value: account.id,
    })),
);

const localCategoryId = computed({
    get: () => (state.category_id === 0 ? undefined : state.category_id),
    set: (value: number | string | undefined) => {
        state.category_id = typeof value === 'number' ? value : Number(value) || 0;
    },
});

const localAccountId = computed({
    get: () => (state.wallet_id === 0 ? undefined : state.wallet_id),
    set: (value: number | string | undefined) => {
        state.wallet_id = typeof value === 'number' ? value : Number(value) || 0;
    },
});

const localAmount = computed({
    get: () => {
        return `R$ ${mask.masked(state.amount)}`;
    },
    set: (value: string) => {
        state.amount = Number(mask.unmasked(value)) || 0;
    },
});

function onTypeChange() {
    state.category_id = 0;
}

function onDateChange(value: CalendarDate) {
    state.date = value.toString();
}

async function fetchCategories() {
    try {
        const response = await fetch('/categories/list');
        categoriesData.value = await response.json();
    } catch (error) {
        errorMessage.value = 'Erro ao carregar as categorias. Tente novamente.';
    }
}

async function fetchAccounts() {
    try {
        const response = await fetch('/accounts/list');
        accountsData.value = await response.json();
    } catch (error) {
        errorMessage.value = 'Erro ao carregar as contas. Tente novamente.';
    }
}

function onSubmit(_: FormSubmitEvent<Schema>) {
    state.submit('post', '/transactions', {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: () => {
            emits('close', false);

            toast.add({
                title: 'Sucesso',
                description: 'Transação criada com sucesso.',
                color: 'success',
                icon: 'i-lucide-check-circle',
            });
        },
        onError: (errors) => {
            console.error(errors);
            errorMessage.value = 'Ocorreu um erro ao salvar a transação.';
        },
    });
}

onMounted(async () => {
    loading.value = true;
    await Promise.all([fetchCategories(), fetchAccounts()]);
    loading.value = false;
});
</script>
