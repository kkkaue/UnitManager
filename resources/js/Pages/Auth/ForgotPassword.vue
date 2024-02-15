<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { CardDescription, CardTitle } from '@/Components/ui/card';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Esqueceu sua senha" />

        <template #card-header>
            <CardTitle>Esqueceu sua senha?</CardTitle>
            <CardDescription>
                Esqueceu sua senha? Sem problemas. Basta nos informar seu endereço de e-mail e enviaremos uma senha por e-mail
                link de redefinição que permitirá que você escolha um novo.
            </CardDescription>
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <Label for="email">
                    Email
                </Label>

                <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username"/>

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Email Password Reset Link
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
