<script setup>
    import { onMounted } from 'vue';
    import { useProfilesStore } from '@/stores/admin/profiles'
    import { can, hasErrorField } from '@/helpers'
    import { useRolesStore } from '@/stores/admin/roles';
    import { useMenusStore } from '@/stores/admin/menus';
    const store = useProfilesStore()
    const roleStore = useRolesStore()
    const menuStore = useMenusStore()

    onMounted(() => {
        store.fetch()
        roleStore.fetch()
        menuStore.fetch()
    })
</script>
<template>
    <div v-if="can('store profile')" class="flex justify-center">
        <Button @click="store.modal.new = true" text="New profile" icon="plus" class="btn-primary" />
    </div>

    <Data-Table v-if="can('view list profiles')" :headers="store.headers" :data="store.profiles" :loading="store.loading.fetch">
        <template #state="{item}">
            <Icon :icon="item ? 'check' : 'xmark'" :class="item ? 'text-green-500' : 'text-red-500'" />
        </template>
        <template #actions="{item}">
            <Drop-Down 
                icon="ellipsis-vertical" 
                variant="btn-alternative"
                :items="[
                    { label : 'Edit', icon : 'edit', action : () => store.edit(item), can : can('edit profile') },
                    { label : 'Delete', icon : 'trash', action : () => store.deleteItem(item), can : can('delete profile') },
                ]"
            />
        </template>
    </Data-Table>

    <Modal :open="store.modal.new" header="Create new profile" icon="plus">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        <Input icon="edit" v-model="store.profile.name" label="Name" required :error="hasErrorField(store.errors,'name')" />           
        <Select v-model="store.profile.role_id"
            label="Roles"
            placeholder="Selected role"
            :options = "roleStore.roles.map(role => ({ value : role.id, label : role.name }))"
            return-type="value"
            :error="hasErrorField(store.errors,'role_id')"
            icon="tag"
            required
        />
        <Select v-model="store.profile.menu_id"
            label="Menus"
            placeholder="Selected menu"
            :options = "menuStore.menus.map(menu => ({ value : menu.id, label : menu.name }))"
            return-type="value"
            :error="hasErrorField(store.errors,'menu_id')"
            icon="layer-group"
            required
        />
        <Text-Area v-model="store.profile.description" label="Description" rows="4" :error="hasErrorField(store.errors,'description')" />
        <Validate-Errors :errors="store.errors" v-if="store.errors != 0" />
        <template #footer>
            <Button @click="store.store()" text="Save" icon="save" class="btn-primary" :loading="store.loading.store" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

    <Modal :open="store.modal.edit" header="Edit new profile" icon="edit">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        <Input icon="edit" v-model="store.profile.name" label="Name" required :error="hasErrorField(store.errors,'name')" />           
        <Select v-model="store.profile.role_id"
            label="Roles"
            placeholder="Selected role"
            :options = "roleStore.roles.map(role => ({ value : role.id, label : role.name }))"
            return-type="value"
            :error="hasErrorField(store.errors,'role_id')"
            icon="tag"
            required
        />
        <Select v-model="store.profile.menu_id"
            label="Menus"
            placeholder="Selected menu"
            :options = "menuStore.menus.map(menu => ({ value : menu.id, label : menu.name }))"
            return-type="value"
            :error="hasErrorField(store.errors,'menu_id')"
            icon="layer-group"
            required
        />
        <Text-Area v-model="store.profile.description" label="Description" rows="4" :error="hasErrorField(store.errors,'description')" />
        <Validate-Errors :errors="store.errors" v-if="store.errors != 0" />
        <template #footer>
            <Button @click="store.update()" text="Update" icon="arrows-rotate" class="btn-primary" :loading="store.loading.update" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

    <Modal :open="store.modal.delete" header="Destroy profile" icon="trash" size="max-w-md">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>
        <div class="grid justify-items-center">
            <Icon icon="circle-exclamation" class="text-7xl" />
            <p class="text-xl text-center">
                Are you sure you want to delete this profile?
            </p>
        </div>
        <template #footer>
            <Button @click="store.destroy()" text="Yes, i'm sure" icon="trash" class="btn-red" :loading="store.loading.destroy" />
            <Button @click="store.resetData" text="No, cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>
</template>