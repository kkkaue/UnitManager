<script setup>
import { Position } from '@vue-flow/core'

const props = defineProps(['label'])
</script>

<template>
  <div>
    <Handle type="target" :position="Position.Top" />
    <div class="bg-blue-200 py-2 px-4 rounded-lg">
      {{ label }}
    </div>
    <Handle type="source" :position="Position.Bottom" />
  </div>
</template>