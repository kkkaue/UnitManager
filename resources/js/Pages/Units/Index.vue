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

let levelY = 0;
let indexX = 0;
const nodeDistance = { x: 200, y: 100 }; // Distância entre os nós
const rootNodePosition = { x: 500, y: 0 }; // Posição do nó raiz

// Função para gerar posição baseada no nível e índice
function positionBasedOnTreeLevel(level, index, total, isRoot = false) {
    if (isRoot) {
        return rootNodePosition;
    }

    const middle = Math.floor(total / 2);
    const offset = (index - middle) * nodeDistance.x;

    return {
        x: rootNodePosition.x + offset,
        y: level * nodeDistance.y,
    };
}

// Função para transformar os dados do backend
function transformData(data) {
    const nodes = [];
    const edges = [];

    function traverse(node, parentId = null, level = 0) {
        const isRoot = parentId === null;
        const newNode = {
            id: node.id.toString(),
            label: node.name,
            position: positionBasedOnTreeLevel(level, indexX, node.all_children ? node.all_children.length : 0, isRoot),
        };

        nodes.push(newNode);

        if (!isRoot) {
            edges.push({
                id: `e${parentId}-${node.id}`,
                source: parentId.toString(),
                target: node.id.toString(),
                type: 'smoothstep',
            });
        }

        if (node.all_children) {
            levelY++;
            let childIndex = 0;
            for (const child of node.all_children) {
                traverse(child, node.id, levelY, childIndex++);
            }
            levelY--;
        }
    }

    for (const root of data) {
        traverse(root);
    }

    return { nodes, edges };
}

// Uso da função
const backendData = props.units;

const { nodes, edges } = useVueFlow(transformData(backendData));

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