<template>
    <h1>Hello World</h1>
    <p>{{ title }}</p>
</template>

<script lang="ts" setup>
    type Props = {
        title: string;
    };

    defineProps<Props>();
</script>
