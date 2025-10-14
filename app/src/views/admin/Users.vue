<script setup>
    import { onMounted } from 'vue'
    import { useUsersStore } from '@/stores/admin/users'
    import { formatVal, hasErrorField } from '@/helpers'
    import Avatar from '@/components/Avatar.vue'
    import { useRouter } from 'vue-router'

    const store = useUsersStore()
    const router = useRouter()

    onMounted(() => {
        store.fetch()
    })
</script>
<template>
    <div class="flex justify-center">
        <Button @click="store.modal.new = true" text="New user" icon="plus" class="btn-primary" />
    </div>

    <Data-Table :headers="store.headers" :data="store.users" :loading="store.loading.fetch">
        <template #tbody="{items}">
            <tr v-for="item in items" 
                @click="router.push({ name : 'User edit', params : { id : item.id } })"
                class="dark:hover:bg-gray-800 hover:bg-gray-200 text-gray-600 dark:text-gray-200 cursor-pointer">
                <td>
                    <div class="flex items-center gap-2">
                        <Avatar 
                            :url="item.information.url_photo" 
                            class="size-12 ring-2 ring-gray-400"
                            :default-value="item.information.small_name"
                        />
                        <div class="flex flex-col">
                            <span class="font-bold">{{ item.information.full_name }}</span>
                            <span class="text-xs flex items-center gap-1">
                                <Icon icon="envelope"/>
                                {{ item.information.email }}
                            </span>
                            <span class="text-xs flex items-center gap-1">
                                <Icon icon="phone"/>
                                {{ formatVal(item.information.phone,'phone') }}
                            </span>
                        </div>
                    </div>
                </td>
                <td>
                    <Icon icon="cake" />
                    {{ item.information.birthday }}
                </td>
                <td>
                    <Icon icon="tag" />
                    {{ item.profile?.name ?? null }}
                </td>
                <td>
                    <Icon icon="calendar-days"/>
                    {{ formatVal(item.created_at,'date') }}
                </td>
                <td>
                    {{ item.deleted_at ? 'Inactive' : 'Active' }}
                </td>
            </tr>
        </template>
    </Data-Table>

    <Modal :open="store.modal.new" header="Create new user" icon="plus">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        
        <div class="grid grid-cols-2 gap-6">
            <Input label="First name" icon="edit" maxlength="60" v-model="store.user.information.first_name" required  :error="hasErrorField(store.errors.info,'first_name')" />
            <Input label="Last name" icon="edit" maxlength="60" v-model="store.user.information.last_name" required  :error="hasErrorField(store.errors.info,'last_name')" />
            <Input label="Dpi" icon="address-card" maxlength="13" v-model="store.user.information.cui" required  :error="hasErrorField(store.errors.info,'cui')" />
            <Input label="Birthday" icon="cake" v-model="store.user.information.birthday" type="date" required  :error="hasErrorField(store.errors.info,'birthday')" />
            <Input label="Email" icon="envelope" v-model="store.user.information.email" type="email" required  :error="hasErrorField(store.errors.info,'email')" />
            <Input label="Phone" icon="phone" maxlength="8" minlength="8" v-model="store.user.information.phone" type="tel" required  :error="hasErrorField(store.errors.info,'phone')" />
            <Input label="City" icon="city" maxlength="60" v-model="store.user.information.city" :error="hasErrorField(store.errors.info,'city')" />
            <Input label="Address" icon="location-dot" maxlength="255" v-model="store.user.information.address" :error="hasErrorField(store.errors.info,'address')" />
            <div class="flex gap-4 text-2xl">
                <Icon icon="person" class="text-blue-500" />
                <Toggle v-model="store.user.information.gender" class="w-14" :values="['M','F']" primaryColor="bg-fuchsia-500" secondaryColor="bg-blue-500" :error="hasErrorField(store.errors.info,'gender')" />
                <Icon icon="person-dress" class="text-fuchsia-500" />
            </div>
            <div class="col-span-2">
                <Select v-model="store.user.profile.id"
                    label="Profile"
                    icon="id-card-clip"
                    :options="[
                        { label : 'Sysadmin', value : 1}
                    ]"
                    return-type="value"
                />
            </div>
        </div>
        <Validate-Errors v-if="store.errors.info != 0" :errors="store.errors.info" />
        

        <template #footer>
            <Button @click="store.store()" text="Save" icon="save" class="btn-primary" :loading="store.loading.store" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

</template>

<style scoped>
@reference 'tailwindcss';
    td {
        @apply px-6 py-4;
    }
</style>