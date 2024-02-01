<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    units: {
        type: Array,
        required: true,
    },
});

onMounted(() => {
});
</script>

<template>
    <Head title="Unidades" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unidades</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8 items-center justify-start h-screen">
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
</style>