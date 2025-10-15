<script setup>
import Avatar from '@/components/Avatar.vue'
import Upload from '@/components/Upload.vue'
import { can, hasErrorField } from '@/helpers'
import { useUsersStore } from '@/stores/admin/users'
import { onMounted } from 'vue'

const store = useUsersStore()

const props = defineProps({
    id : {
        type : String,
        required : true,
    }
})

onMounted(() => { 
    store.show(props.id)
    store.getProfiles()
})

</script>
<template>
    <div v-if="store.user.information" class="grid grid-cols-1 px-4 pt-6 xl:grid-cols-3 xl:gap-4">
        <div class="col-span-full xl:col-auto">
            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="items-center sm:flex xl:block 2xl:flex sm:space-x-4 xl:space-x-0 2xl:space-x-4">                    
                    <Avatar v-if="!store.change" :url="store.user.information.url_photo" :defaultValue="store.user.information.small_name" shape="square" class="size-28" />
                    <Upload v-if="store.change"
                        accept="image/*" 
                        @sendFile="store.getFile" 
                        class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0" 
                    />
                    <div>
                        <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">Profile picture</h3>
                        <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                            JPG, GIF or PNG. Max size of 800K
                        </div>
                        <div class="flex items-center space-x-4">
                            <Button @click="store.change = !store.change" icon="arrows-rotate" class="btn-alternative" title="Upload image"/>
                            <Button @click="store.uploadPicture" icon="upload" class="btn-alternative" title="Save picture" :loading="store.loading.upload" />
                            <Button @click="store.deletePicture" icon="trash" class="btn-alternative" title="Delete picture" :loading="store.loading.delete"/>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-if="can('reset password user')" class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-semibold dark:text-white">Reset password</h3>
                <Button @click="store.resetPassword" text="Yes, reset" icon="key" class="btn-primary" :loading="store.loading.pass" />
            </div>
        </div>
        <div class="col-span-2">
            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-semibold dark:text-white">General information</h3>
                <form @submit.prevent="store.update()">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="First name" icon="edit" maxlength="60" v-model="store.user.information.first_name" required  :error="hasErrorField(store.errors.info,'first_name')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Last name" icon="edit" maxlength="60" v-model="store.user.information.last_name" required  :error="hasErrorField(store.errors.info,'last_name')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Dpi" icon="address-card" maxlength="13" v-model="store.user.information.cui" required  :error="hasErrorField(store.errors.info,'cui')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Birthday" icon="cake" v-model="store.user.information.birthday" type="date" required  :error="hasErrorField(store.errors.info,'birthday')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Email" icon="envelope" v-model="store.user.information.email" type="email" required  :error="hasErrorField(store.errors.info,'email')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Phone" icon="phone" maxlength="8" minlength="8" v-model="store.user.information.phone" type="tel" required  :error="hasErrorField(store.errors.info,'phone')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="City" icon="city" maxlength="60" v-model="store.user.information.city" :error="hasErrorField(store.errors.info,'city')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <Input label="Address" icon="location-dot" maxlength="255" v-model="store.user.information.address" :error="hasErrorField(store.errors.info,'address')" />
                        </div>
                        <div class="col-span-6 sm:col-span-3 flex gap-4 text-2xl">
                            <Icon icon="person" class="text-blue-500" />
                            <Toggle v-model="store.user.information.gender" class="w-14" :values="['M','F']" primaryColor="bg-fuchsia-500" secondaryColor="bg-blue-500" :error="hasErrorField(store.errors.info,'gender')" />
                            <Icon icon="person-dress" class="text-fuchsia-500" />
                        </div>
                        <div class="col-span-6">
                            <Select v-model="store.user.profile_id"
                                label="Profile"
                                icon="id-card-clip"
                                :options="store.profiles"
                                return-type="value"
                            />
                        </div>
                        <div v-if="can('edit user')" class="col-span-6 sm:col-full">
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
            <div v-if="can('delete user')" class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="space-y-2">
                    <h3 class="text-xl font-semibold dark:text-white">Disabled this user</h3>
                    <p>Once you disabled a user, there is no going back. Please be certain.</p>
                    <div class="flex justify-center">
                        <Button @click="store.disabledUser" text="Yes, disabled user" icon="xmark" class="btn-red" :loading="store.loading.pass" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>