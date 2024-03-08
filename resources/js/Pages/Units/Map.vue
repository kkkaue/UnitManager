<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import Leaflet from 'leaflet';

const unitProps = defineProps({
    units: {
        type: Array,
        required: true,
    },
});

const map = ref(null);

onMounted(() => {
    console.log(unitProps.units);
    map.value = Leaflet.map('map').setView([0.0396198, -51.0455364], 13);
    Leaflet.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map.value);

    unitProps.units.forEach((unit) => {
        if (unit.latitude && unit.longitude) {
            Leaflet.marker([unit.latitude, unit.longitude])
                .addTo(map.value)
                .bindPopup(`<h1>${unit.name}</h1><br><p style="margin: 0">${unit.description}</p>`);
        }
    });
});
</script>

<template>
    <Head title="Mapa de unidades" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mapa de unidades
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8 items-center justify-start h-screen">
                <div id="map" class="w-full h-full"></div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>