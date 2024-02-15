<script setup>
import { defineProps, computed, ref, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/Components/ui/card'
import { Select, SelectTrigger, SelectContent, SelectItem, SelectValue } from '@/Components/ui/select'

import LMap from '@/Components/LMap.vue';

const props = defineProps({
    unit: {
        type: Object,
        required: true,
    },
    parent: {
        type: Object,
        required: true,
    },
    units: {
        type: Array,
        required: true,
    },
    isEditUnitModalOpen: {
        type: Boolean,
        required: true,
    },
    onClosed: {
        type: Function,
        required: true,
    },
});

// Definindo a referência para o estado do formulário
const markerPosition = ref({ lat: 0, lng: 0 });

// Inicializando o formulário com valores nulos
const unitForm = useForm({
    name: null,
    description: null,
    email: null,
    phone: null,
    location: null,
    latitude: null,
    longitude: null,
    parent_id: null,
});

// Função para fechar o modal
const closeUnitModal = () => {
    props.onClosed();
};

// Função para salvar as alterações
const submitUnitForm = () => {
    unitForm.put(route('units.update', props.unit.id), {
        onSuccess: () => {
            unitForm.reset();
            closeUnitModal();
            // Recarregando a página para atualizar a hierarquia
            window.location.reload();
        },
    });
};

watch(() => props.unit, (newValue) => {
    unitForm.name = newValue.name;
    unitForm.description = newValue.description;
    unitForm.email = newValue.email;
    unitForm.phone = newValue.phone;
    unitForm.location = newValue.location;
    unitForm.latitude = newValue.latitude;
    unitForm.longitude = newValue.longitude;
    unitForm.parent_id = newValue.parent_id ? newValue.parent_id.toString() : null;
});

// Observando a posição do marcador e atualizando os valores de latitude e longitude do formulário
onMounted(() => {
    watch(markerPosition, (newValue) => {
        unitForm.latitude = newValue.lat;
        unitForm.longitude = newValue.lng;
    });
});
</script>

<template>
    <!-- Modal para editar unidade -->
    <div v-if="isEditUnitModalOpen" class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <Card class="w-1/3">
            <!-- Cabeçalho do modal -->
            <CardHeader>
                <CardTitle>
                    Editar Unidade
                </CardTitle>
                <CardDescription>
                    Edite os detalhes da unidade
                </CardDescription>
            </CardHeader>
            <!-- Conteúdo do modal -->
            <CardContent>
                <!-- Formulário para editar unidade -->
                <form @submit.prevent="submitUnitForm">
                    <!-- Campos do formulário -->
                    <div class="grid items-center w-full gap-4">
                        <!-- Campos de nome e descrição -->
                        <div class="flex space-x-1.5 items-center">
                            <div class="w-full flex flex-col space-y-1.5">
                                <Label for="name" :class="{ 'text-red-600': unitForm.errors.name }">
                                    Nome da Unidade
                                </Label>
                                <Input id="name" v-model="unitForm.name" :class="{ 'border-red-600': unitForm.errors.name }"/>
                                <div class="text-red-600 text-sm" v-if="unitForm.errors.name">
                                    {{ unitForm.errors.name }}
                                </div>
                            </div>
                            <div class="w-full flex flex-col space-y-1.5">
                                <Label for="description" :class="{ 'text-red-600': unitForm.errors.description }">
                                    Descrição
                                </Label>
                                <Input id="description" v-model="unitForm.description" :class="{ 'border-red-600': unitForm.errors.description }"/>
                                <div class="text-red-600 text-sm" v-if="unitForm.errors.description">
                                    {{ unitForm.errors.description }}
                                </div>
                            </div>
                        </div>
                        <!-- Campos de e-mail e telefone -->
                        <div class="flex space-x-1.5 items-center">
                            <div class="w-full flex flex-col space-y-1.5">
                                <Label for="email" :class="{ 'text-red-600': unitForm.errors.email }">
                                    E-mail
                                </Label>
                                <Input id="email" v-model="unitForm.email" :class="{ 'border-red-600': unitForm.errors.email }"/>
                                <div class="text-red-600 text-sm" v-if="unitForm.errors.email">
                                    {{ unitForm.errors.email }}
                                </div>
                            </div>
                            <div class="w-full flex flex-col space-y-1.5">
                                <Label for="phone" :class="{ 'text-red-600': unitForm.errors.phone }">
                                    Telefone
                                </Label>
                                <Input id="phone" v-model="unitForm.phone" :class="{ 'border-red-600': unitForm.errors.phone }"/>
                                <div class="text-red-600 text-sm" v-if="unitForm.errors.phone">
                                    {{ unitForm.errors.phone }}
                                </div>
                            </div>
                        </div>

                        <!-- Campo de localização -->
                        <div class="flex flex-col space-y-1.5">
                            <Label :class="{ 'text-red-600': unitForm.errors.latitude || unitForm.errors.longitude }">
                                Selecione a localização da unidade
                            </Label>
                            <LMap :modelValue="unitForm.location" v-model="markerPosition" />
                            <div class="text-red-600 text-sm" v-if="unitForm.errors.latitude || unitForm.errors.longitude">
                                {{ unitForm.errors.latitude || unitForm.errors.longitude }}
                            </div>
                            <input type="hidden" id="latitude" v-model="unitForm.latitude" />
                            <input type="hidden" id="longitude" v-model="unitForm.longitude" />
                        </div>
                        
                        <!-- Campo de unidade pai -->
                        <div class="flex flex-col space-y-1.5">
                            <Label for="parent_id" :class="{ 'text-red-600': unitForm.errors.parent_id }">
                                Unidade pai
                            </Label>
                            <div v-if="parent">
                                <Select v-model="unitForm.parent_id" id="parent_id">
                                    <SelectTrigger :class="{ 'border-red-600': unitForm.errors.parent_id }">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <div v-for="unit in units" :key="unit.id">
                                            <!-- mostra as opções diferentes da unidade selecionada -->
                                            <SelectItem v-if="unit.id !== props.unit.id" :value="unit.id.toString()">
                                                {{ unit.name }}
                                            </SelectItem>
                                        </div>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div v-else>
                                <Input id="parent_id" placeholder="Unidade Raiz" disabled />
                            </div>
                            
                            <div class="text-red-600 text-sm" v-if="unitForm.errors.parent_id">
                                {{ unitForm.errors.parent_id }}
                            </div>
                        </div>
                    </div>
                </form>
            </CardContent>
            <!-- Rodapé do modal -->
            <CardFooter class="flex justify-between px-6 pb-6">
                <Button variant="destructive" @click="closeUnitModal">
                    Fechar
                </Button>
                <Button @click="submitUnitForm">
                    Salvar
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>