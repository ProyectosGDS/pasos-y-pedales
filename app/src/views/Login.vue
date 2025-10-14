<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { hasErrorField } from '@/helpers'

const router = useRouter()
const auth = useAuthStore()

const handleLogin = async () => {

    const success = await auth.login()

    if (success) {
        router.push({ name: 'Home' })
    }
}

</script>
<template>
    <div class="flex flex-col items-center justify-center px-6 pt-8 mx-auto md:h-screen pt:mt-0 dark:bg-gray-900">
        <a href="" class="flex items-center justify-center mb-8 text-2xl font-semibold lg:mb-10
            dark:text-white">
            <img src="https://flowbite.com/docs/images/logo.svg" class="mr-4 h-11" alt="FlowBite Logo">
            <span>Flowbite</span>
        </a>
        <!-- Card -->
        <div class="w-full max-w-xl p-6 space-y-8 sm:p-8 bg-white rounded-lg shadow dark:bg-gray-800">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Iniciar sesión en la plataforma
            </h2>
            <form @submit.prevent="handleLogin" class="mt-8 space-y-6">
                <Input v-model="auth.credentials.cui" label="Dpi" icon="address-card" type="text" pattern="^\d{13}$" required :error="hasErrorField(auth.errors,'cui')"/>
                <Input v-model="auth.credentials.password" label="Password" icon="key" type="password" required :error="hasErrorField(auth.errors,'password')"/>
                <Validate-Errors v-if="auth.errors != 0" :errors="auth.errors" />
                <Button text="Inicia sesión en tu cuenta" type="submit" class="btn-primary" icon="door-open" :loading="auth.loading" />
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    No estas registrado ? <a class="text-primary-700 hover:underline dark:text-primary-500">Crear cuenta</a>
                </div>
            </form>
        </div>
    </div>

</template>