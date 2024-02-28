<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Checkbox } from '@/Components/ui/checkbox';
import { Button } from '@/Components/ui/button';
import { CardDescription, CardTitle } from '@/Components/ui/card';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />

        <template #card-header>
            <CardTitle>Faça login na sua conta</CardTitle>
            <CardDescription>Faça login em sua conta usando seu e-mail e senha.</CardDescription>
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <Label for="email">
                    Email
                </Label>

                <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <Label for="password">
                    Senha
                </Label>

                <Input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />

                <InputError class="mt-2" :message="form.errors.password" />

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md"
                >
                    Esqueceu sua senha?
                </Link>
            </div>

            <div class="block mt-4">
                <Label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 cursor-pointer">Lembre de mim</span>
                </Label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('register')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md"
                >
                    Não possui uma conta?
                </Link>

                <Button class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Login
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
