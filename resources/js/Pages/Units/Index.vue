<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

import axios from 'axios';

import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectTrigger, SelectContent, SelectItem, SelectValue } from '@/Components/ui/select'

import LMap from '@/Components/LMap.vue';
import { watch } from 'vue';

/* define a propriedade units que será recebida do componente pai */
const props = defineProps({
    unitsWithChildren: {
        type: Array,
        required: true,
    },
    units: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: null,
    description: null,
    email: null,
    phone: null,
    latitude: null,
    longitude: null,
    parent_id: null,
});

/* define as variáveis que serão utilizadas no componente */
const hierarchyChanged = ref(false);
const isAddUnitModalOpen = ref(false);
const markerPosition = ref({ lat: 0, lng: 0 });
let initialData = [];

/* define os métodos que serão utilizados no componente */
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

const createNode = (text) => {
    const node = new go.Node("Auto", {
        background: "#ffffff",
        margin: 10,
        stroke: null,
        shadowColor: "rgba(0, 0, 0, 0.3)",
        shadowOffset: new go.Point(3, 3),
        shadowBlur: 5,
        zOrder: 1,
        selectionChanged: p => {
            // para sobrepor os outros nós caso esteja selecionado
            if (p.isSelected) {
                p.layerName = "Foreground";
            } else {
                p.layerName = "";
            }
        }
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
    };

    node.mouseDragLeave = function(e, obj) {
        obj.part.findObject("RoundedRectangle").fill = "#ffffff";
        obj.part.findObject("RoundedRectangle").stroke = "#000000";
    };

    node.mouseDrop = function(e, obj) {
        const draggedNode = e.diagram.selection.first();
        const droppedOnNode = obj.part;

        if (draggedNode !== null && droppedOnNode !== null) {
            draggedNode.data.parent = droppedOnNode.data.key;
            e.diagram.model.updateTargetBindings(draggedNode.data);
            e.diagram.rebuildParts();

            handleHierarchyChanged();
        }
    };

    return node;
};

const handleHierarchyChanged = () => {
    hierarchyChanged.value = true;
};

const openAddUnitModal = () => {
    isAddUnitModalOpen.value = true;
};

const closeAddUnitModal = () => {
    isAddUnitModalOpen.value = false;
};

const mostrarValoresInputConsoleLog = () => {
    console.log(document.getElementById('latitude').value);
    console.log(document.getElementById('longitude').value);
};

const saveChanges = async () => {
    const myDiagram = go.Diagram.fromDiv("myDiagramDiv");
    myDiagram.startTransaction("save Changes");
    const data = myDiagram.model.nodeDataArray;
    const units = data.map(unit => {
        return {
            unit_id: unit.key,
            parent_id: unit.parent,
        };
    });
    
    try {
        await axios.post('/units/update-hierarchy', { units });
        hierarchyChanged.value = false;
    } catch (error) {
        console.error(error);
    }
    myDiagram.zoomToFit();
    myDiagram.commitTransaction("save Changes");

    initialData = JSON.parse(JSON.stringify(myDiagram.model.nodeDataArray));
};

const cancelChanges = () => {
    const myDiagram = go.Diagram.fromDiv("myDiagramDiv");
    myDiagram.startTransaction("cancel Changes");
    while (myDiagram.undoManager.canUndo()) {
        myDiagram.undoManager.undo();
    }
    myDiagram.zoomToFit();
    hierarchyChanged.value = false;

    // Restaura o estado inicial dos nós
    myDiagram.model.nodeDataArray = JSON.parse(JSON.stringify(initialData));
    myDiagram.commitTransaction("cancel Changes");
};

const submit = () => {
    form.post(route('units.store'), {
        onFinish: () => {
            form.reset();
            closeAddUnitModal();
        },
    });
};

onMounted(() => {
    const transformedUnits = transformUnits(props.unitsWithChildren);
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

    initialData = JSON.parse(JSON.stringify(myDiagram.model.nodeDataArray));

    myDiagram.zoomToFit();

    watch(markerPosition, (newValue) => {
        form.latitude = newValue.lat;
        form.longitude = newValue.lng;
    });
});
</script>

<template>
    <Head title="Unidades" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unidades</h2>
        </template>

        <div class="py-12">
            <!-- botão de adicionar unidades -->
            <div class="mx-auto sm:px-6 lg:px-8 items-center justify-start h-screen">
                <div class="flex justify-end mb-4">
                    <Button @click="openAddUnitModal">Adicionar Unidade</Button>
                </div>
                <div class="w-full h-3/5" @hierarchyChanged="hierarchyChanged = true">
                    <div id="myDiagramDiv" class="bg-gray-100 border border-gray-200 rounded-md shadow-md fon" style="width: 100%; height: 100%;">
                    </div>
                </div>

                <!-- caso exista alguma alteração na hierarquia, exibe botões para salvar ou cancelar a alteração -->
                <div class="flex justify-end mt-4" v-if="hierarchyChanged">
                    <Button variant="destructive" class="mr-2" @click="cancelChanges">Cancelar</Button>
                    <Button @click="saveChanges">Salvar</Button>
                </div>

                <div v-if="isAddUnitModalOpen" class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <Card class="w-1/3">
                        <CardHeader>
                            <CardTitle>Adicionar Unidade</CardTitle>
                            <CardDescription>Adicione uma nova unidade no sistema</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submit">
                                <div class="grid items-center w-full gap-4">
                                    <div class="flex space-x-1.5 items-center">
                                        <div class="w-full flex flex-col space-y-1.5">
                                            <Label for="name">Nome da Unidade</Label>
                                            <Input id="name" v-model="form.name" placeholder="Digite o nome da unidade" autocomplete="off" />
                                        </div>
                                        <div class="w-full flex flex-col space-y-1.5">
                                            <Label for="description">Descrição</Label>
                                            <Input id="description" v-model="form.description" placeholder="Digite a descrição da unidade" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="flex space-x-1.5 items-center">
                                        <div class="w-full flex flex-col space-y-1.5">
                                            <Label for="email">E-mail</Label>
                                            <Input id="email" v-model="form.email" placeholder="Digite o e-mail da unidade" autocomplete="off" />
                                        </div>
                                        <div class="w-full flex flex-col space-y-1.5">
                                            <Label for="phone">Telefone</Label>
                                            <Input id="phone" v-model="form.phone" placeholder="Digite o telefone da unidade" autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="flex flex-col space-y-1.5">
                                        <Label>
                                            Selecione a localização da unidade
                                        </Label>
                                        <LMap v-model="markerPosition" />
                                        <input type="hidden" id="latitude" v-model="form.latitude" />
                                        <input type="hidden" id="longitude" v-model="form.longitude" />
                                    </div>
                                    
                                    <div class="flex flex-col space-y-1.5">
                                        <Label for="unit">
                                            Selecione a unidade pai
                                        </Label>
                                        <Select v-model="form.parent_id">
                                            <SelectTrigger>
                                                <SelectValue>
                                                    {{ form.parent_id ? units.find(unit => unit.id.toString() === form.parent_id).name : 'Selecione a unidade pai' }}
                                                </SelectValue>
                                            </SelectTrigger>
                                            <SelectContent position="popper">
                                                <SelectItem v-for="unit in units" :key="unit.id" :value="unit.id.toString()">
                                                    {{ unit.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>
                            </form>
                        </CardContent>
                        <CardFooter class="flex justify-between px-6 pb-6">
                            <Button variant="outline" @click="closeAddUnitModal">
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="form.processing" @click="submit">
                                Save
                            </Button>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
</style>