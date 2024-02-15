<script setup>
import { Button } from '@/Components/ui/button';
import InputError from '@/Components/InputError.vue';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import {
  AlertDialog,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import { CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

</script>

<template>
    <CardHeader>
        <CardTitle>Deletar conta</CardTitle>

        <CardDescription>
            Depois que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente. Antes de excluir
            sua conta, baixe quaisquer dados ou informações que você deseja reter.
        </CardDescription>
    </CardHeader>

    <CardContent>
        <AlertDialog>
            <AlertDialogTrigger as-child>
                <Button variant="destructive">Deletar conta</Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle> Tem certeza de que deseja excluir sua conta?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Depois que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente. Por favor
                        digite sua senha para confirmar que deseja excluir permanentemente sua conta.
                    </AlertDialogDescription>
                    <div class="mt-6">
                        <Label for="password" class="sr-only">
                            Senha
                        </Label>

                        <Input id="password" ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-3/4" placeholder="Senha" @keyup.enter="deleteUser"/>

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>
                        Cancelar
                    </AlertDialogCancel>
                    <Button
                        variant="destructive"
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Deletar conta
                    </Button>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </CardContent>
</template>
