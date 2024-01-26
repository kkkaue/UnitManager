<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref, markRaw, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';

import { VueFlow, useVueFlow, Position } from '@vue-flow/core';
import { Background } from '@vue-flow/background';
import { Controls } from '@vue-flow/controls';
import { MiniMap } from '@vue-flow/minimap';

const props = defineProps({
    units: {
        type: Array,
        required: true,
    },
});

let currentLevel = 0;
const DISTANCE_BETWEEN_NODES = { x: 200, y: 100 };
const ROOT_NODE_POSITION = { x: 500, y: 0 };
const parentPositions = {};

function calculateNodePosition(level, index, totalSiblings, parentPosition) {
    const middleSiblingIndex = (totalSiblings-1) / 2;
    const horizontalOffset = (index - middleSiblingIndex) * DISTANCE_BETWEEN_NODES.x;

    return {
        x: parentPosition.x + horizontalOffset,
        y: level * DISTANCE_BETWEEN_NODES.y,
    };
}

function transformBackendData(data) {
    const nodes = [];
    const edges = [];

    function traverse(node, parent = node, parentId = null, level = 0, index = 0) {
        const isRootNode = parentId === null;
        const parentPosition = isRootNode ? ROOT_NODE_POSITION : parentPositions[parentId];
        const totalSiblings = parent.all_children ? parent.all_children.length : 0;
        const nodePosition = isRootNode ? ROOT_NODE_POSITION : calculateNodePosition(level, index, totalSiblings, parentPosition);

        const newNode = {
            id: node.id.toString(),
            label: node.name,
            position: nodePosition,
        };
        
        nodes.push(newNode);
        parentPositions[node.id] = newNode.position;

        if (!isRootNode) {
            edges.push({
                id: `e${parentId}-${node.id}`,
                source: parentId.toString(),
                target: node.id.toString(),
                type: 'smoothstep',
            });
        }

        if (node.all_children) {
            currentLevel++;
            let childIndex = 0;
            for (const child of node.all_children) {
                traverse(child, node, node.id, currentLevel, childIndex++);
            }
            currentLevel--;
        }
    }

    for (const root of data) {
        traverse(root);
    }

    return { nodes, edges };
}

const backendData = props.units;

const { nodes, edges } = useVueFlow(transformBackendData(backendData));

onMounted(() => {
    console.log(nodes.value);
    console.log(edges.value);
});
</script>

<template>
    <Head title="Unidades" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unidades</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8 items-center justify-start h-screen">
                <div class="w-full h-2/3">
                    <VueFlow auto-connect connection-line-type="smoothstep" fit-view-on-init>

                    <Background bg-color="#F1F5F9" />

                    <MiniMap />

                    <Controls />

                    </VueFlow>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* import the necessary styles for Vue Flow to work */
@import '@vue-flow/core/dist/style.css';

/* import the default theme, this is optional but generally recommended */
@import '@vue-flow/core/dist/theme-default.css';

@import '@vue-flow/controls/dist/style.css';
@import '@vue-flow/minimap/dist/style.css';
</style>