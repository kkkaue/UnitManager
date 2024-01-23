<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Unit from '@/Components/Unit.vue';0

const props = defineProps({
    units: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Unidades" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unidades</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex items-center justify-center">
                <Unit :units="units" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
