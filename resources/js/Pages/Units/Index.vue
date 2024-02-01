<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    units: {
        type: Array,
        required: true,
    },
});

const newNodeName = ref('');
const newNodeParent = ref('');

const transformUnits = (units) => {
    let result = [];

    units.forEach(unit => {
        let transformedUnit = {
            key: unit.id,
            name: unit.name
        };

        if (typeof unit.parent_id === 'number') {
            transformedUnit.parent = unit.parent_id;
        }

        result.push(transformedUnit);

        if (unit.all_children) {
            result = result.concat(transformUnits(unit.all_children));
        }
    });

    return result;
};

onMounted(() => {
    const transformedUnits = transformUnits(props.units);
    const myDiagram = new go.Diagram("myDiagramDiv", {
        "undoManager.isEnabled": true,
        layout: new go.TreeLayout({ angle: 90, layerSpacing: 35 })
    });

    myDiagram.nodeTemplate = createNode("Default Text");

    myDiagram.linkTemplate = new go.Link({ 
        routing: go.Link.Orthogonal, corner: 5 
    }).add( new go.Shape({ 
            strokeWidth: 1,
            stroke: "#d1d5db", 
        }))
    myDiagram.model = new go.TreeModel(transformedUnits);

    myDiagram.zoomToFit();
});

const createNode = (text) => {
    const node = new go.Node("Auto", {
        background: "#ffffff",
        margin: 10,
        stroke: null,
        shadowColor: "rgba(0, 0, 0, 0.3)",
        shadowOffset: new go.Point(3, 3),
        shadowBlur: 5,
    }).add(
        new go.Shape("RoundedRectangle", {
            name: "RoundedRectangle",
            fill: "#ffffff",
            stroke: "#000000",
        }),
        new go.TextBlock(text, {
            margin: new go.Margin(12, 24, 12, 24),
            stroke: "#000000",
            font: "16px sans-serif"
        }).bind("text", "name")
    );

    // Adiciona o efeito de hover
    node.mouseEnter = function(e, obj) {
        obj.part.findObject("RoundedRectangle").fill = "#f3f4f6";
    };

    node.mouseLeave = function(e, obj) {
        obj.part.findObject("RoundedRectangle").fill = "#ffffff";
    };

    // Adiciona o efeito de arrastar e soltar
    node.mouseDragEnter = function(e, obj) {
        obj.part.findObject("RoundedRectangle").fill = "#f3f4f6";
        obj.part.findObject("RoundedRectangle").stroke = "#2563eb";
        obj.part.layerName = "Background";
    };

    node.mouseDragLeave = function(e, obj) {
        obj.part.findObject("RoundedRectangle").fill = "#ffffff";
        obj.part.findObject("RoundedRectangle").stroke = "#000000";
        obj.part.layerName = "Foreground";
    };

    node.mouseDrop = function(e, obj) {
        const draggedNode = e.diagram.selection.first();
        const droppedOnNode = obj.part;

        if (draggedNode !== null && droppedOnNode !== null) {
            draggedNode.data.parent = droppedOnNode.data.key;
            e.diagram.model.updateTargetBindings(draggedNode.data);
            e.diagram.rebuildParts();
        }
    };

    return node;
};

const addValue = (name, parent) => {
    const myDiagram = go.Diagram.fromDiv("myDiagramDiv");
    const newNode = { key: (myDiagram.model.nodeDataArray.length + 1).toString(), name, parent };
    myDiagram.model.addNodeData(newNode);
    myDiagram.model.addNodeData(newNode);
    console.log(data.value);
    newNodeName.value = '';
    newNodeParent.value = '';
};
</script>

<template>
    <Head title="Unidades" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unidades</h2>
        </template>

        <div class="py-12">
            <div class="flex items-center justify-center space-x-2">
                <input v-model="newNodeName" type="text" placeholder="Nome" class="py-2 px-4 rounded border border-gray-300">
                <input v-model="newNodeParent" type="text" placeholder="Parent id" class="py-2 px-4 rounded border border-gray-300">
                <button @click="addValue(newNodeName, newNodeParent)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Adicionar</button>
            </div>
    
            <div class="mt-4">
                <div class="mx-auto sm:px-6 lg:px-8 items-center justify-start h-screen">
                    <div id="myDiagramDiv" class="bg-gray-100 border border-gray-200 rounded-md shadow-md fon" style="width: 100%; height: 60%;">
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
</style>