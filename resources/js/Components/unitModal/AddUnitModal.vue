<script setup>
import { defineProps, ref, onMounted, watch, defineEmits } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/Components/ui/card'
import { Select, SelectTrigger, SelectContent, SelectItem, SelectValue } from '@/Components/ui/select'
import LMap from '@/Components/LMap.vue';

const props = defineProps({
    units: {
        type: Array,
        required: true,
    },
    isAddUnitModalOpen: {
        type: Boolean,
        required: true,
    },
    onClosed: {
        type: Function,
        required: true,
    },
});

const emit = defineEmits(['unit:created']);

// Definindo a referência para o estado do formulário
const markerPosition = ref({ lat: 0, lng: 0 });

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

// Função para fechar o modal
const closeUnitModal = () => {
    props.onClosed();
};

// Função para enviar o formulário
const submitUnitForm = () => {
    unitForm.post(route('units.store'), {
        onSuccess: () => {
            unitForm.reset();
            closeUnitModal();
            emit('unit:created');
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
    <div v-if="isAddUnitModalOpen" class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <Card class="w-1/3">
            <CardHeader>
                <CardTitle>Adicionar Unidade</CardTitle>
                <CardDescription>Adicione uma nova unidade no sistema</CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submitUnitForm">
                    <div class="grid items-center w-full gap-4">
                        <div class="flex space-x-1.5 items-center">
                            <div class="w-full flex flex-col space-y-1.5">
                                <Label for="name" :class="{ 'text-red-600': unitForm.errors.name }">
                                    Nome da Unidade
                                </Label>
                                <Input id="name" v-model="unitForm.name" :class="{ 'border-red-600': unitForm.errors.name }" placeholder="Digite o nome da unidade" autocomplete="off" />
                                <div class="text-red-600 text-sm" v-if="unitForm.errors.name">
                                    {{ unitForm.errors.name }}
                                </div>
                            </div>
                            <div class="w-full flex flex-col space-y-1.5">
                                <Label for="description" :class="{ 'text-red-600': unitForm.errors.description }">
                                    Descrição
                                </Label>
                                <Input id="description" v-model="unitForm.description" :class="{ 'border-red-600': unitForm.errors.description }" placeholder="Digite a descrição da unidade" autocomplete="off"/>
                                <div class="text-red-600 text-sm" v-if="unitForm.errors.description">
                                    {{ unitForm.errors.description }}
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-1.5 items-center">
                            <div class="w-full flex flex-col space-y-1.5">
                                <Label for="email" :class="{ 'text-red-600': unitForm.errors.email }">
                                    E-mail
                                </Label>
                                <Input id="email" v-model="unitForm.email" :class="{ 'border-red-600': unitForm.errors.email }" placeholder="Digite o e-mail da unidade" autocomplete="off" />
                                <div class="text-red-600 text-sm" v-if="unitForm.errors.email">
                                    {{ unitForm.errors.email }}
                                </div>
                            </div>
                            <div class="w-full flex flex-col space-y-1.5">
                                <Label for="phone" :class="{ 'text-red-600': unitForm.errors.phone }">
                                    Telefone
                                </Label>
                                <Input id="phone" v-mask="'(##) #####-####'" v-model="unitForm.phone" :class="{ 'border-red-600': unitForm.errors.phone }" placeholder="Digite o telefone da unidade" autocomplete="off" />
                                <div class="text-red-600 text-sm" v-if="unitForm.errors.phone">
                                    {{ unitForm.errors.phone }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-1.5">
                            <Label :class="{ 'text-red-600': unitForm.errors.latitude || unitForm.errors.longitude }">
                                Selecione a localização da unidade
                            </Label>
                            <LMap v-model="markerPosition" />
                            <div class="text-red-600 text-sm" v-if="unitForm.errors.latitude || unitForm.errors.longitude">
                                {{ unitForm.errors.latitude || unitForm.errors.longitude }}
                            </div>
                            <input type="hidden" id="latitude" v-model="unitForm.latitude" />
                            <input type="hidden" id="longitude" v-model="unitForm.longitude" />
                        </div>
                        
                        <div class="flex flex-col space-y-1.5">
                            <Label for="parent_id" :class="{ 'text-red-600': unitForm.errors.parent_id }">
                                Selecione a unidade pai
                            </Label>
                            <Select v-model="unitForm.parent_id" id="parent_id">
                                <SelectTrigger :class="{ 'border-red-600': unitForm.errors.parent_id }">
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
                            <div class="text-red-600 text-sm" v-if="unitForm.errors.parent_id">
                                {{ unitForm.errors.parent_id }}
                            </div>
                        </div>
                    </div>
                </form>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
                <Button variant="destructive" @click="closeUnitModal">
                    Cancelar
                </Button>
                <Button type="submit" :disabled="unitForm.processing" @click="submitUnitForm">
                    Adicionar
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>