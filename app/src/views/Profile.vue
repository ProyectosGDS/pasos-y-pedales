<script setup>
    import Upload from '@/components/Upload.vue'
    import { formatVal, hasErrorField } from '@/helpers'
    import { useProfileStore } from '@/stores/profile'
    import { onMounted } from 'vue'

    const store = useProfileStore()

    const darkModeSystem = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

    const changeThemeToDark = () => {
        document.documentElement.classList.add('dark');
        localStorage.setItem('color-theme', 'dark');
    }
    const changeThemeToLight = () => {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('color-theme', 'light');
    }
    const changeThemeToSystem = () => {
        if(darkModeSystem === 'dark') {
            changeThemeToDark();
        } else {
            changeThemeToLight();
        }
        localStorage.removeItem('color-theme');
    }

    onMounted(() => {
        store.fetch()
    })


</script>
<template>
    <div v-if="!store.loading.fetch" class="grid grid-cols-1 px-4 pt-6 xl:grid-cols-3 xl:gap-4">
        <div class="col-span-full xl:col-auto">
            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="items-center sm:flex xl:block 2xl:flex sm:space-x-4 xl:space-x-0 2xl:space-x-4">
                    
                    <img v-if="!store.change"
                        class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0 object-cover object-center" 
                        :src="store.information.url_photo ?? `https://ui-avatars.com/api/?name=${store.information.small_name}&color=7F9CF5&background=EBF4FF`" 
                        :alt="store.information.full_name"
                    >
                    <Upload v-if="store.change"
                        accept="image/*" 
                        @sendFile="store.getFile" 
                        class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0" 
                    />
                    <div>
                        <h3 class="mb-1 text-xl font-bold text-gray-700 dark:text-white">Profile picture</h3>
                        <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                            JPG, GIF or PNG. Max size of 800K
                        </div>
                        <div class="flex items-center space-x-4">
                            <Button @click="store.change = !store.change" icon="arrows-rotate" class="btn-alternative" title="Upload image"/>
                            <Button @click="store.uploadPhoto" icon="upload" class="btn-alternative" title="Save picture" :loading="store.loading.upload" />
                            <Button @click="store.deletePicture" icon="trash" class="btn-alternative" title="Delete picture" :loading="store.loading.delete"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flow-root">
                    <h3 class="text-xl font-semibold dark:text-white">Sessions</h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li v-for="session in store.sessions" class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <Icon :icon="['Android','iOS'].includes(session.os) ? 'mobile-screen' : 'desktop'" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-base font-semibold text-gray-700 truncate dark:text-white">
                                        {{ session.ip_address }}
                                    </p>
                                    <p class="text-sm font-normal text-gray-500 truncate dark:text-gray-400">
                                        {{ session.browser+' - '+session.os }}
                                    </p>
                                </div>
                                <div class="inline-flex items-center">
                                    <p class="text-sm font-normal text-gray-500 truncate dark:text-gray-400">
                                        {{ formatVal(session.last_activity,'date') }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
             <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flow-root">
                    <h3 class="text-xl font-semibold dark:text-white">Themes</h3>
                    <div class="flex justify-around items-center gap-4 mt-4 border border-gray-400 p-4 btn">
                        <div @click="changeThemeToLight()" class="cursor-pointer hover:font-bold dark:text-gray-200 text-gray-800">
                            <Icon icon="sun"/>
                            <span>Light</span>
                        </div>
                        <div @click="changeThemeToDark()" class="cursor-pointer hover:font-bold dark:text-gray-200 text-gray-800">
                            <Icon icon="moon"/>
                            <span>Dark</span>
                        </div>
                        <div @click="changeThemeToSystem()" class="cursor-pointer hover:font-bold dark:text-gray-200 text-gray-800">
                            <Icon icon="desktop"/>
                            <span>System</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-2">
            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-semibold dark:text-white">General information</h3>
                <form @submit.prevent="store.update()">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="First name" icon="edit" maxlength="60" v-model="store.information.first_name" required  :error="hasErrorField(store.errors.info,'first_name')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Last name" icon="edit" maxlength="60" v-model="store.information.last_name" required  :error="hasErrorField(store.errors.info,'last_name')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Dpi" icon="address-card" maxlength="13" v-model="store.information.cui" required  :error="hasErrorField(store.errors.info,'cui')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Birthday" icon="cake" v-model="store.information.birthday" type="date" required  :error="hasErrorField(store.errors.info,'birthday')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Email" icon="envelope" v-model="store.information.email" type="email" required  :error="hasErrorField(store.errors.info,'email')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Phone" icon="phone" maxlength="8" minlength="8" v-model="store.information.phone" type="tel" required  :error="hasErrorField(store.errors.info,'phone')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="City" icon="city" maxlength="60" v-model="store.information.city" :error="hasErrorField(store.errors.info,'city')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Address" icon="location-dot" maxlength="255" v-model="store.information.address" :error="hasErrorField(store.errors.info,'address')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Profile" icon="id-card-clip" v-model="store.information.profile_name" disabled/>
                        </div>
                        <div class="col-span-6 sm:col-full">
                            <Button
                                type="submit" 
                                text="Save all" 
                                icon="save" 
                                class="btn-primary"
                                :loading="store.loading.update"
                            />
                            <Validate-Errors v-if="store.errors.info != 0" :errors="store.errors.info" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-semibold dark:text-white">Password information</h3>
                <form @submit.prevent="store.changePassword()">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <Input v-model="store.passwords.current" label="Current password" icon="key" type="password" placeholder="*******" required :error="hasErrorField(store.errors.pass,'current')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input v-model="store.passwords.new" label="New password" icon="lock" type="password" placeholder="*******" required :error="hasErrorField(store.errors.pass,'new')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input v-model="store.passwords.new_confirmation" label="Confirm password" icon="lock" type="password" placeholder="*******" required />
                        </div>
                        <div class="col-span-6 sm:col-full">
                            <Button type="submit" text="Save all" icon="save" class="btn-primary" :loading="store.loading.pass" />
                        </div>
                    </div>
                </form>
                <Validate-Errors v-if="store.errors.pass != 0" :errors="store.errors.pass" />
            </div>
        </div>
    </div>
</template>