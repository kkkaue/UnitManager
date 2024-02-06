<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

import { transformUnits } from '../../unitUtils.js';

import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/Components/ui/card'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectTrigger, SelectContent, SelectItem, SelectValue } from '@/Components/ui/select'

import OrgChart from '@/Components/OrgChart.vue';
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
let transformedData = transformUnits(props.unitsWithChildren);
const isAddUnitModalOpen = ref(false);
const markerPosition = ref({ lat: 0, lng: 0 });

const openAddUnitModal = () => {
    isAddUnitModalOpen.value = true;
};

const closeAddUnitModal = () => {
    isAddUnitModalOpen.value = false;
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
                
                <OrgChart :data="transformedData" />

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