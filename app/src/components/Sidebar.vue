<script setup>
    import Logo from '@/components/Logo.vue'
    import { useRouter, useRoute } from 'vue-router'
    import { useAuthStore } from '@/stores/auth'
    import { ref, watch } from 'vue'
    import { onClickOutside } from '@vueuse/core'

    const auth = useAuthStore()
    const router = useRouter()
    const route = useRoute()

    const openParentId = ref(null)
    const openSidebar = ref(false)
    const sidebarRef = ref(null)
    const toggleParent = (parentId) => {
        openParentId.value = openParentId.value === parentId ? null : parentId
    }

    onClickOutside(sidebarRef, () => {
        if (openSidebar.value) openSidebar.value = false
    })

    const handleLogout = () => {
        auth.logout()
        router.push({ name : 'Login'})
    }

    watch(() => route.name, (newRouteName) => {
        const parent = auth.userMenu.find(menu => 
            menu.childrens?.some(child => child.route === newRouteName)
        )

        if (parent && openParentId.value !== parent.id) {
            openParentId.value = parent.id
        } else if (!parent && openParentId.value !== null) {
            openParentId.value = null 
        }
    }, { immediate: true })

</script>

<template>
    <Button
        icon="bars"
        class="btn-primary ml-4 mt-4 sm:hidden"
        @click="openSidebar = !openSidebar"
    />

    <aside ref="sidebarRef" 
        class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
        :class="{
            '-translate-x-full': !openSidebar,
            'translate-x-0': openSidebar
        }">
        <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">
            <RouterLink :to="{name : 'Home'}">
                <Logo class="h-[3.4rem] w-auto fill-gray-800 dark:fill-gray-200" />
            </RouterLink>
            <br>
            <ul class="space-y-2 font-medium">
                <template v-for="page in auth.userMenu" :key="page.id">
                    <li v-if="page.type == 'header'" class="p-3 text-gray-400 uppercase">{{ page.label }}</li>
                    
                    <li v-else-if="page.type == 'parent' && page.childrens?.length > 0">
                        <button 
                            @click="toggleParent(page.id)" 
                            class="flex items-center w-full p-2 text-base cursor-pointer text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-00 dark:text-white dark:hover:bg-gray-700"
                            :class="{'bg-gray-400 dark:bg-gray-700': openParentId === page.id}"> 
                            <Icon :icon="page.icon" />
                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">
                                {{ page.label }}
                            </span>
                            <Icon icon="chevron-down" :class="openParentId === page.id ? '' : 'rotate-180'" />
                        </button>

                        <ul v-if="openParentId === page.id" class="py-2 space-y-2 pl-4">
                            <li v-for="child in page.childrens" :key="child.id">
                                <RouterLink 
                                    :to="{ name : child.route}" 
                                    class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-400 dark:text-white dark:hover:bg-gray-700"
                                    :class="{'bg-gray-400 dark:bg-gray-700': route.name === child.route}"> 
                                    <Icon :icon="child.icon" />
                                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">
                                        {{ child.label }}
                                    </span>
                                </RouterLink>
                            </li>
                        </ul>
                    </li>
                    
                    <li v-else>
                        <RouterLink 
                            :to="{ name : page.route}" 
                            class="flex cursor-pointer items-center p-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 group" 
                            :class="{'bg-gray-400 dark:bg-gray-700': route.name === page.route}">
                            <Icon :icon="page.icon" />
                            <span class="ms-3">{{ page.label }}</span>
                        </RouterLink>
                    </li>
                </template>
            </ul>
        </div>
        <div class="absolute bottom-0 left-0 justify-center w-full p-4 space-x-4 bg-white lg:flex dark:bg-gray-800" sidebar-bottom-menu="">
            <Drop-Down
                :text="auth.user?.small_name ?? 'Sign in'"
                :img="auth.user?.url_photo ?? `https://ui-avatars.com/api/?name=${auth.user?.small_name}&color=7F9CF5&background=EBF4FF`" 
                icon-right="chevron-up" 
                :items="[
                    { label: 'Profile', icon: 'user', action: () => router.push({ name : 'Profile'}) },
                    { label: 'Logout', icon: 'right-to-bracket', action: () => handleLogout() },
                ]"
                variant="btn-light"
            />
        </div>
    </aside>
</template>

<style scoped>
    @reference 'tailwindcss';

    :deep(svg) {
        @apply transition duration-75 ;
    }
</style>