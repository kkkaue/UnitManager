<script setup>
// Importando a biblioteca Leaflet e algumas funções do Vue 3
import Leaflet from 'leaflet';
import { ref, onMounted, defineEmits } from 'vue';

// Criando referências reativas para o mapa e o marcador
const map = ref(null);
const mapMarker = ref(null);

// Definindo um emissor de eventos para atualizar o valor do modelo no componente pai
const emitEvent = defineEmits(['update:modelValue']);

// Função que será executada após o componente ser montado
onMounted(() => {
  // Inicializando o mapa e definindo a visão inicial
  map.value = Leaflet.map('map').setView([0.0396198, -51.0455364], 12);
  
  // Adicionando a camada de tiles do OpenStreetMap ao mapa
  Leaflet.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map.value);

  // Adicionando um ouvinte de evento de clique ao mapa
  map.value.on('click', function(event) {
    // Se já existe um marcador, removê-lo antes de adicionar um novo
    if (mapMarker.value) {
      map.value.removeLayer(mapMarker.value);
      mapMarker.value = Leaflet.marker([event.latlng.lat, event.latlng.lng]).addTo(map.value);
    } else {
      // Adicionando um novo marcador ao mapa nas coordenadas do clique
      mapMarker.value = Leaflet.marker([event.latlng.lat, event.latlng.lng]).addTo(map.value);
    }
    
    // Emitindo um evento para atualizar o valor do modelo no componente pai com as coordenadas do marcador
    emitEvent('update:modelValue', { lat: event.latlng.lat, lng: event.latlng.lng });
  });
});
</script>

<template>
  <!-- Definindo o elemento do mapa -->
  <div id="map" class="w-full h-60"></div>
</template>