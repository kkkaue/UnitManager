<script setup>
import InputError from '@/Components/InputError.vue';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
        <CardHeader>
            <CardTitle>Informação do Perfil</CardTitle>

            <CardDescription>
                Atualize as informações de perfil e endereço de e-mail da sua conta.
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-6">
                <div>
                    <Label for="name">
                        Nome
                    </Label>
    
                    <Input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name"
                    />
    
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
    
                <div>
                    <Label for="email">
                        Email
                    </Label>
    
                    <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                 
    
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
    
                <div v-if="mustVerifyEmail && user.email_verified_at === null">
                    <p class="text-sm mt-2 text-gray-800">
                        Seu endereço de e-mail não foi verificado.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Clique aqui para reenviar o e-mail de verificação.
                        </Link>
                    </p>
    
                    <div
                        v-show="status === 'verification-link-sent'"
                        class="mt-2 font-medium text-sm text-green-600"
                    >
                        Um novo link de verificação foi enviado para seu endereço de e-mail.
                    </div>
                </div>
    
                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Salvar</Button>
    
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Salvo.</p>
                    </Transition>
                </div>
            </form>
        </CardContent>
</template>
