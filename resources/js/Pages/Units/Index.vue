<script setup>
// Importando componentes e funções necessárias
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { transformUnits } from '../../unitUtils.js';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import OrgChart from '@/Components/OrgChart.vue';
import AddUnitModal from '@/Components/unitModal/AddUnitModal.vue';
import ViewUnitModal from '@/Components/unitModal/ViewUnitModal.vue';
import EditUnitModal from '@/Components/unitModal/EditUnitModal.vue';
import DeleteUnitModal from '@/Components/unitModal/DeleteUnitModal.vue';

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

// Definindo referências reativas para o estado dos modais
const modals = ref({
    addUnit: false,
    viewUnit: false,
    editUnit: false,
    deleteUnit: false,
});

// Definindo referências reativas para a unidade selecionada e sua unidade pai
const selectedUnit = ref({});
const parentOfSelectedUnit = ref({});

// Tratando os dados das unidades
var transformedUnitsData = transformUnits(unitProps.unitsWithChildren);

// Funções para abrir e fechar os modais
const openModal = (modalName) => {
    modals.value[modalName] = true;
};
const closeModal = (modalName) => {
    modals.value[modalName] = false;
};

// Função para lidar com uma unidade
const handleUnit = (node, modalName) => {
    const unit = unitProps.units.find((unit) => unit.id === node.key);
    selectedUnit.value = unit;
    parentOfSelectedUnit.value = unitProps.units.find((unit) => unit.id === selectedUnit.value.parent_id);
    openModal(modalName);
};

const handleUnitCreated = () => {
    transformedUnitsData = transformUnits(unitProps.unitsWithChildren);
}
</script>

<template>
    <!-- Definindo o título da página -->
    <Head title="Unidades" />

    <!-- Layout autenticado -->
    <AuthenticatedLayout>
        <!-- Cabeçalho da página -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Unidades
            </h2>
        </template>

        <!-- Conteúdo da página -->
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8 items-center justify-start h-screen">
                <!-- Título da página -->
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 leading-tight">Organograma das Unidades</h2>
                </div>
                <!-- Botão para adicionar unidades -->
                <div class="flex justify-end mb-4">
                    <Button @click="() => openModal('addUnit')">Adicionar Unidade</Button>
                </div>
                
                <!-- Organograma das unidades -->
                <OrgChart 
                    :data="transformedUnitsData"
                    @view:unit="node => handleUnit(node, 'viewUnit')"
                    @edit:unit="node => handleUnit(node, 'editUnit')"
                    @delete:unit="node => handleUnit(node, 'deleteUnit')"
                />

                <AddUnitModal 
                    :units="unitProps.units" 
                    :isAddUnitModalOpen="modals.addUnit"
                    :onClosed="() => closeModal('addUnit')"
                    @unit:created="handleUnitCreated"
                />

                <ViewUnitModal
                    :unit="selectedUnit"
                    :parent="parentOfSelectedUnit"
                    :isViewUnitModalOpen="modals.viewUnit"
                    :onClosed="() => closeModal('viewUnit')"
                />

                <EditUnitModal
                    :unit="selectedUnit"
                    :parent="parentOfSelectedUnit"
                    :units="unitProps.units"
                    :isEditUnitModalOpen="modals.editUnit"
                    :onClosed="() => closeModal('editUnit')"
                />

                <DeleteUnitModal
                    :unit="selectedUnit"
                    :isDeleteUnitModalOpen="modals.deleteUnit"
                    :onClosed="() => closeModal('deleteUnit')"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
</style>