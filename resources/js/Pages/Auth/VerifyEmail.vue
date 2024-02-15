<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Button } from '@/Components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import { CardDescription, CardTitle } from '@/Components/ui/card';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Verificação de E-mail" />

        <template #card-header>
            <CardTitle>Verifique seu endereço de e-mail</CardTitle>
            <CardDescription>
                Obrigado por inscrever-se! Antes de começar, você poderia verificar seu endereço de e-mail clicando no link
                acabamos de enviar um e-mail para você? Se você não recebeu o e-mail, teremos prazer em lhe enviar outro.
            </CardDescription>
        </template>

        <Alert class="mb-4 bg-green-100 border-green-500 text-green-700 p-4" v-if="verificationLinkSent">
            <AlertTitle>Link de verificação enviado</AlertTitle>
            <AlertDescription>
                Um novo link de verificação foi enviado para o endereço de e-mail que você forneceu durante o registro.
            </AlertDescription>
        </Alert>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Reenviar email de verificação
                </Button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >Sair</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
