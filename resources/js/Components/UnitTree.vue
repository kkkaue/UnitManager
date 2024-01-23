<script setup>
import NodeUnit from '@/Components/NodeUnit.vue';
import UnitTree from '@/Components/UnitTree.vue';

const props = defineProps({
    units: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div v-for="unit in units" :key="unit.id" class="w-full flex flex-col items-center justify-start gap-y-2">
      <NodeUnit :content="unit.name" />
      <div v-if="unit.all_children.length > 0" class="flex gap-8">
        <UnitTree :units="unit.all_children" />
      </div>
    </div>
</template>