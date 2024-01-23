<script setup>
import Unit from '@/Components/Unit.vue';

const props = defineProps({
    units: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div v-for="unit in units" :key="unit.id" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center">
                    <div class="text-lg leading-7 font-semibold">
                        {{ unit.name }}
                    </div>
                </div>
            </div>
        </div>
        <div v-if="unit.all_children.length > 0" class="flex">
            <Unit :units="unit.all_children" />
        </div>
    </div>
</template>