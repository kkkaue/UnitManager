<script setup>
import { defineProps, computed } from 'vue';
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/Components/ui/card'

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
    isViewUnitModalOpen: {
        type: Boolean,
        required: true,
    },
    onClosed: {
        type: Function,
        required: true,
    },
});

// Propriedade computada para o nome da unidade pai
const parentName = computed(() => {
    return props.parent ? props.parent.name : 'Unidade raiz (não tem pai)';
});

// Função para fechar o modal
const closeUnitModal = () => {
    props.onClosed();
};
</script>

<template>
    <div v-if="isViewUnitModalOpen" class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <Card class="w-1/3">
            <CardHeader>
                <CardTitle>
                    Visualizar Unidade
                </CardTitle>
                <CardDescription>
                    Visualize os detalhes da unidade
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="grid items-center w-full gap-4">
                    <div class="flex space-x-1.5 items-center">
                        <div class="w-full flex flex-col space-y-1.5">
                            <Label for="name">
                                Nome da Unidade
                            </Label>
                            <Input id="name" v-model="unit.name" disabled/>
                        </div>
                        <div class="w-full flex flex-col space-y-1.5">
                            <Label for="description">
                                Descrição
                            </Label>
                            <Input id="description" v-model="unit.description" disabled/>
                        </div>
                    </div>
                    <div class="flex space-x-1.5 items-center">
                        <div class="w-full flex flex-col space-y-1.5">
                            <Label for="email">
                                E-mail
                            </Label>
                            <Input id="email" v-model="unit.email" disabled />
                        </div>
                        <div class="w-full flex flex-col space-y-1.5">
                            <Label for="phone">
                                Telefone
                            </Label>
                            <Input id="phone" v-mask="'(##) #####-####'" v-model="unit.phone" disabled />
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1.5">
                        <Label>
                            Localização da unidade
                        </Label>
                        <LMap :modelValue="unit.location" :disabled="true" />
                    </div>
                    <div class="flex flex-col space-y-1.5">
                        <Label for="parent">
                            Unidade pai
                        </Label>
                        <Input id="parent" v-model="parentName" disabled />
                    </div>
                </div>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
                <Button variant="destructive" @click="closeUnitModal">
                    Fechar
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>