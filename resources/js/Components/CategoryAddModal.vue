<template>
    <UModal
        :dismissable="state.processing"
        :title="title"
        :description="description"
        :close="{ onClick: () => emits('close', false) }"
        :ui="{ footer: 'justify-end' }"
    >
        <template #body>
            <UForm ref="form" :state="state" :schema="schema" class="grid grid-cols-2 gap-4" @submit="onSubmit">
                <UFormField label="Nome" name="name" class="col-span-2">
                    <UInput v-model="state.name" placeholder="Ex: Alimentação" type="text" :ui="{ root: 'w-full' }" />
                </UFormField>
                <UFormField label="Tipo" name="type">
                    <USelect v-model="state.type" :items="types" :ui="{ base: 'w-full' }" />
                </UFormField>
                <UFormField label="Cor" name="color">
                    <USelect v-model="state.color" :items="colors" :ui="{ base: 'w-full' }">
                        <template #default="{ modelValue }">
                            <div class="h-5 w-full rounded-md" :style="{ backgroundColor: modelValue }"></div>
                        </template>
                        <template #item="{ item }">
                            <div class="h-5 w-full rounded-md p-0.5">
                                <div class="h-full w-full rounded-md" :style="{ backgroundColor: item }"></div>
                            </div>
                        </template>
                    </USelect>
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
import { colors } from '@/constants';
import { Category, CATEGORY_TYPE } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { FormSubmitEvent, SelectItem } from '@nuxt/ui';
import { useToast } from '@nuxt/ui/runtime/composables/useToast.js';
import { ref, useTemplateRef } from 'vue';
import * as y from 'yup';

const emits = defineEmits<{ close: [boolean] }>();
const props = defineProps<{
    category?: Category;
}>();

const form = useTemplateRef('form');
const toast = useToast();

const title = props.category ? 'Editar categoria' : 'Nova categoria';
const description = props.category ? `Edite os detalhes da categoria '${props.category.name}'.` : 'Adicione uma nova categoria para suas transações.';

const schema = y.object({
    name: y.string().required(),
    type: y.string().required(),
    color: y.string().required(),
});

type Schema = y.InferType<typeof schema>;

const state = useForm<Schema>({
    name: props.category?.name ?? '',
    type: props.category?.type ?? '',
    color: props.category?.color ?? '',
});

const types = ref<SelectItem[]>([
    { label: 'Receita', value: CATEGORY_TYPE.INCOME },
    { label: 'Despesa', value: CATEGORY_TYPE.EXPENSE },
]);

const errorMessage = ref('');

function onSubmit(_: FormSubmitEvent<Schema>) {
    const query = window.location.search;
    const url = props.category ? `/categories/${props.category.id}` : '/categories';
    const successDesc = props.category ? 'Categoria atualizada com sucesso.' : 'Categoria criada com sucesso.';
    const method = props.category ? 'put' : 'post';

    state.submit(method, `${url}${query}`, {
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
            errorMessage.value = 'Ocorreu um erro ao salvar a categoria.';
        },
    });
}
</script>

<style scoped></style>
