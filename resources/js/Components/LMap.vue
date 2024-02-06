<script setup>
// Importando a biblioteca Leaflet e algumas funções do Vue 3
import L from 'leaflet';
import { ref, onMounted, defineEmits } from 'vue';

// Criando referências reativas para o mapa e o marcador
const map = ref(null);
const marker = ref(null);

// Definindo um emissor de eventos para atualizar o valor do modelo no componente pai
const emit = defineEmits(['update:modelValue']);

// Função que será executada após o componente ser montado
onMounted(() => {
  // Inicializando o mapa e definindo a visão inicial
  map.value = L.map('map').setView([0.0396198, -51.0455364], 12);
  
  // Adicionando a camada de tiles do OpenStreetMap ao mapa
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map.value);

  // Adicionando um ouvinte de evento de clique ao mapa
  map.value.on('click', function(e) {
    // Se já existe um marcador, removê-lo antes de adicionar um novo
    if (marker.value) {
      map.value.removeLayer(marker.value);
      marker.value = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map.value);
    } else {
      // Adicionando um novo marcador ao mapa nas coordenadas do clique
      marker.value = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map.value);
    }
    
    // Emitindo um evento para atualizar o valor do modelo no componente pai com as coordenadas do marcador
    emit('update:modelValue', { lat: e.latlng.lat, lng: e.latlng.lng });
  });
});
</script>

<template>
  <!-- Definindo o elemento do mapa -->
  <div id="map" class="w-full h-60"></div>
</template>