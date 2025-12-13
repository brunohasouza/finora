<template>
    <UModal
        :dismissable="state.processing"
        title="Nova conta"
        description="Adicione uma nova conta para gerenciar suas finanças."
        :close="{ onClick: () => emits('close', false) }"
        :ui="{ footer: 'justify-end' }"
    >
        <template #body>
            <UForm ref="form" :state="state" :schema="schema" class="space-y-4" @submit="onSubmit">
                <UFormField label="Nome" name="name">
                    <UInput v-model="state.name" placeholder="Ex: Conta principal" type="text" :ui="{ root: 'w-full' }" />
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
                <UFormField label="Saldo inicial" name="balance">
                    <UInput v-model="localBalance" placeholder="10.0000,00" type="text" :ui="{ root: 'w-full' }" />
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
import { Bank } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { FormSubmitEvent } from '@nuxt/ui';
import { useToast } from '@nuxt/ui/runtime/composables/useToast.js';
import { Mask } from 'maska';
import { computed, onMounted, ref, useTemplateRef } from 'vue';
import * as y from 'yup';

const MAX_BALANCE = 100000000000;
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
    name: y.string().required(),
    bank_id: y.string().required(),
    balance: y
        .number()
        .required()
        .min(0, 'O saldo não pode ser negativo')
        .max(MAX_BALANCE, `O saldo máximo é de R$ ${mask.masked(MAX_BALANCE)}`),
});

type Schema = y.InferType<typeof schema>;

const emits = defineEmits<{ close: [boolean] }>();

const form = useTemplateRef('form');
const toast = useToast();
const state = useForm<Schema>({
    name: '',
    bank_id: '',
    balance: 0,
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
    state.submit('post', `/accounts`, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: () => {
            emits('close', false);

            toast.add({
                title: 'Sucesso',
                description: 'Conta adicionada com sucesso.',
                color: 'success',
                icon: 'i-lucide-check-circle',
            });
        },
        onError: (errors) => {
            console.error(errors);
            errorMessage.value = 'Ocorreu um erro ao adicionar nova conta.';
        },
    });
}

onMounted(() => fetchBanks());
</script>
