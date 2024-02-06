<script setup>
// Importando componentes e funções necessárias
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

// Definindo as propriedades que serão recebidas do componente pai
const unitProps = defineProps({
    unitsWithChildren: {
        type: Array,
        required: true,
    },
    units: {
        type: Object,
        required: true,
    },
});

// Inicializando o formulário com valores nulos
const unitForm = useForm({
    name: null,
    description: null,
    email: null,
    phone: null,
    latitude: null,
    longitude: null,
    parent_id: null,
});

// Tratando os dados das unidades
const transformedUnitsData = transformUnits(unitProps.unitsWithChildren);

// Definindo a referência para o estado do modal e a posição do marcador
const isAddUnitModalOpen = ref(false);
const markerPosition = ref({ lat: 0, lng: 0 });

// Funções para abrir e fechar o modal
const openUnitModal = () => {
    isAddUnitModalOpen.value = true;
};
const closeUnitModal = () => {
    isAddUnitModalOpen.value = false;
};

// Função para enviar o formulário
const submitUnitForm = () => {
    unitForm.post(route('units.store'), {
        onFinish: () => {
            unitForm.reset();
            closeUnitModal();
        },
    });
};

// Observando a posição do marcador e atualizando os valores de latitude e longitude do formulário
onMounted(() => {
    watch(markerPosition, (newValue) => {
        unitForm.latitude = newValue.lat;
        unitForm.longitude = newValue.lng;
    });
});
</script>

<template>
    <!-- Definindo o título da página -->
    <Head title="Unidades" />

    <!-- Layout autenticado -->
    <AuthenticatedLayout>
        <!-- Cabeçalho da página -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unidades</h2>
        </template>

        <!-- Conteúdo da página -->
        <div class="py-12">
            <!-- Botão para adicionar unidades -->
            <div class="mx-auto sm:px-6 lg:px-8 items-center justify-start h-screen">
                <div class="flex justify-end mb-4">
                    <Button @click="openUnitModal">Adicionar Unidade</Button>
                </div>
                
                <!-- Organograma das unidades -->
                <OrgChart :data="transformedUnitsData" />

                <!-- Modal para adicionar unidade -->
                <div v-if="isAddUnitModalOpen" class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <Card class="w-1/3">
                        <!-- Cabeçalho do modal -->
                        <CardHeader>
                            <CardTitle>Adicionar Unidade</CardTitle>
                            <CardDescription>Adicione uma nova unidade no sistema</CardDescription>
                        </CardHeader>
                        <!-- Conteúdo do modal -->
                        <CardContent>
                            <!-- Formulário para adicionar unidade -->
                            <form @submit.prevent="submitUnitForm">
                                <!-- Campos do formulário -->
                                <div class="grid items-center w-full gap-4">
                                    <!-- Campos de nome e descrição -->
                                    <div class="flex space-x-1.5 items-center">
                                        <div class="w-full flex flex-col space-y-1.5">
                                            <Label for="name">Nome da Unidade</Label>
                                            <Input id="name" v-model="unitForm.name" placeholder="Digite o nome da unidade" autocomplete="off" />
                                        </div>
                                        <div class="w-full flex flex-col space-y-1.5">
                                            <Label for="description">Descrição</Label>
                                            <Input id="description" v-model="unitForm.description" placeholder="Digite a descrição da unidade" autocomplete="off" />
                                        </div>
                                    </div>
                                    <!-- Campos de e-mail e telefone -->
                                    <div class="flex space-x-1.5 items-center">
                                        <div class="w-full flex flex-col space-y-1.5">
                                            <Label for="email">E-mail</Label>
                                            <Input id="email" v-model="unitForm.email" placeholder="Digite o e-mail da unidade" autocomplete="off" />
                                        </div>
                                        <div class="w-full flex flex-col space-y-1.5">
                                            <Label for="phone">Telefone</Label>
                                            <Input id="phone" v-model="unitForm.phone" placeholder="Digite o telefone da unidade" autocomplete="off" />
                                        </div>
                                    </div>

                                    <!-- Campo de localização -->
                                    <div class="flex flex-col space-y-1.5">
                                        <Label>
                                            Selecione a localização da unidade
                                        </Label>
                                        <LMap v-model="markerPosition" />
                                        <input type="hidden" id="latitude" v-model="unitForm.latitude" />
                                        <input type="hidden" id="longitude" v-model="unitForm.longitude" />
                                    </div>
                                    
                                    <!-- Campo de unidade pai -->
                                    <div class="flex flex-col space-y-1.5">
                                        <Label for="unit">
                                            Selecione a unidade pai
                                        </Label>
                                        <Select v-model="unitForm.parent_id">
                                            <SelectTrigger>
                                                <SelectValue>
                                                    {{ unitForm.parent_id ? units.find(unit => unit.id.toString() === unitForm.parent_id).name : 'Selecione a unidade pai' }}
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
                        <!-- Rodapé do modal -->
                        <CardFooter class="flex justify-between px-6 pb-6">
                            <Button variant="outline" @click="closeUnitModal">
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="unitForm.processing" @click="submitUnitForm">
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