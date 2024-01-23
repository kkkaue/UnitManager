<script setup>

const props = defineProps({
  content: {
    type: String,
    required: true,
  },
});
</script>

<template>
  {{ content }}
</template>