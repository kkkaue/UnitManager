<script setup>
import L from 'leaflet';
import { ref, onMounted, defineEmits } from 'vue';

const map = ref(null);
const marker = ref(null);

// Emit the event to the parent component
const emit = defineEmits(['update:modelValue']);

onMounted(() => {
  map.value = L.map('map').setView([0.0396198, -51.0455364], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map.value);

  map.value.on('click', function(e) {
    console.log(e.latlng);
    if (marker.value) {
      map.value.removeLayer(marker.value);
      marker.value = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map.value);
    } else {
      marker.value = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map.value);
    }
    emit('update:modelValue', { lat: e.latlng.lat, lng: e.latlng.lng });
  });
});
</script>

<template>
  <div id="map" class="w-full h-60"></div>
</template>