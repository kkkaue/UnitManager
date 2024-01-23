<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UnitTree from '@/Components/UnitTree.vue';
import Unit from '@/Components/Unit.vue';
import NodeUnit from '@/Components/NodeUnit.vue';

const props = defineProps({
    units: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Unidades" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unidades</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="min-w-20 flex flex-col items-center justify-start text-center flex-grow flex-shrink-0 basis-0">
                    <span class="bg-white p-3 rounded-lg text-xl text-gray-800 font-medium">
                        teste 
                    </span>
                    <div class="flex gap-8">
                        <div class="min-w-20 flex flex-col items-center justify-start text-center flex-grow flex-shrink-0 basis-0">
                            <span class="bg-white p-3 rounded-lg text-xl text-gray-800 font-medium">
                                teste 
                            </span>
                            <div class="flex gap-8">
                                <div class="min-w-20 flex flex-col items-center justify-start text-center flex-grow flex-shrink-0 basis-0">
                                    <span class="bg-white p-3 rounded-lg text-xl text-gray-800 font-medium">
                                        teste 
                                    </span>
                                </div>
                                <div class="min-w-20 flex flex-col items-center justify-start text-center flex-grow flex-shrink-0 basis-0">
                                    <span class="bg-white p-3 rounded-lg text-xl text-gray-800 font-medium">
                                        teste 
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="min-w-20 flex flex-col items-center justify-start text-center flex-grow flex-shrink-0 basis-0">
                            <span class="bg-white p-3 rounded-lg text-xl text-gray-800 font-medium">
                                teste 
                            </span>
                            <div class="flex gap-8">
                                <div class="min-w-20 flex flex-col items-center justify-start text-center flex-grow flex-shrink-0 basis-0">
                                    <span class="bg-white p-3 rounded-lg text-xl text-gray-800 font-medium">
                                        teste 
                                    </span>
                                </div>
                                <div class="min-w-20 flex flex-col items-center justify-start text-center flex-grow flex-shrink-0 basis-0">
                                    <span class="bg-white p-3 rounded-lg text-xl text-gray-800 font-medium">
                                        teste 
                                    </span>
                                    <div class="flex gap-8">
                                        <div class="min-w-20 flex flex-col items-center justify-start text-center flex-grow flex-shrink-0 basis-0">
                                            <span class="bg-white p-3 rounded-lg text-xl text-gray-800 font-medium">
                                                teste 
                                            </span>
                                        </div>
                                        <div class="min-w-20 flex flex-col items-center justify-start text-center flex-grow flex-shrink-0 basis-0">
                                            <span class="bg-white p-3 rounded-lg text-xl text-gray-800 font-medium">
                                                teste unidade doida ae
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 items-center justify-start">
                <UnitTree :units="units" />
            </div> -->
        </div>
    </AuthenticatedLayout>
</template>
