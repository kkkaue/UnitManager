<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { CardDescription, CardTitle } from '@/Components/ui/card';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirme sua senha" />

        <template #card-header>
            <CardTitle>Confirme sua senha</CardTitle>
            <CardDescription>
                Esta é uma área segura do aplicativo. Por favor, confirme sua senha antes de continuar.
            </CardDescription>
        </template>

        <form @submit.prevent="submit">
            <div>
                <Label for="password">
                    Senha
                </Label>
                <Input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" autofocus/>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex justify-end mt-4">
                <Button class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Confirm
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
