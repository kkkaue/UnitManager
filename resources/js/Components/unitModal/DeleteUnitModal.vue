<script setup>
import { defineProps, defineEmits } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/Components/ui/card'

const props = defineProps({
    unit: {
        type: Object,
        required: true,
    },
    isDeleteUnitModalOpen: {
        type: Boolean,
        required: true,
    },
    onClosed: {
        type: Function,
        required: true,
    },
});

const emit = defineEmits(['unit:deleted']);

// Definindo a referência para o estado do formulário
const unitForm = useForm({
    id: props.unit.id,
});

// Função para fechar o modal
const closeUnitModal = () => {
    props.onClosed();
};

// Função para excluir a unidade
const deleteUnit = () => {
    unitForm.delete(route('units.destroy', props.unit.id), {
        onSuccess: () => {
            closeUnitModal();
            emit('unit:deleted');
        },
    });
};
</script>

<template>
    <div v-if="isDeleteUnitModalOpen" class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <Card class="w-1/3">
            <CardHeader>
                <CardTitle>
                    Excluir unidade
                </CardTitle>
                <CardDescription>
                    Tem certeza que deseja excluir a unidade {{ unit.name }}?
                </CardDescription>
            </CardHeader>
            <CardContent>
                <p class="text-sm text-gray-500">
                    Ao excluir a unidade, todos os dados relacionados a ela serão perdidos.
                </p>
                <p class="text-sm text-gray-500">
                    Caso a unidade possua filhos, eles serão movidos para a unidade pai.
                </p>
            </CardContent>
            <CardFooter class="flex justify-between px-6 pb-6">
                <Button type="button" variant="ghost" @click="closeUnitModal">
                    Cancelar
                </Button>
                <Button type="button" variant="destructive" @click="deleteUnit">
                    Excluir
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>