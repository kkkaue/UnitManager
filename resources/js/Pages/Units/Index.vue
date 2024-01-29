<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref, markRaw, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';

import Calendar from 'primevue/calendar';

const props = defineProps({
    units: {
        type: Array,
        required: true,
    },
    components: {
        Calendar,
    },
});

let date = ref(null);

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
            <p>{{ date }}</p>
            <div class="mx-auto sm:px-6 lg:px-8 items-center justify-start h-screen">
                <div class="w-full h-2/3">
                    <Calendar v-model="date" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
</style>