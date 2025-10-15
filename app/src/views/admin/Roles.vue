<script setup>
    import { onMounted } from 'vue'
    import { useRolesStore } from '@/stores/admin/roles'
    import { can, hasErrorField } from '@/helpers'
    const store = useRolesStore()

    onMounted(() => {
        store.fetch()
    })
</script>
<template>
    <div v-if="can('store role')" class="flex justify-center">
        <Button @click="store.modal.new = true" text="New role" icon="plus" class="btn-primary" />
    </div>

    <Data-Table v-if="can('view list roles')" :headers="store.headers" :data="store.roles" :loading="store.loading.fetch">
        <template #state="{item}">
            <Icon :icon="item ? 'check' : 'xmark'" :class="item ? 'text-green-500' : 'text-red-500'" />
        </template>
        <template #actions="{item}">
            <Drop-Down 
                icon="ellipsis-vertical" 
                variant="btn-alternative"
                :items="[
                    { label : 'Edit', icon : 'edit', action : () => store.edit(item), can : can('edit role') },
                    { label : 'Delete', icon : 'trash', action : () => store.deleteItem(item), can : can('delete role') },
                ]"
            />
        </template>
    </Data-Table>

    <Modal :open="store.modal.new" header="Create new role" icon="plus">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        <Input v-model="store.role.name" label="Name" required :error="hasErrorField(store.errors,'name')" />           
        <fieldset class="border rounded-lg border-gray-500 p-4">
            <legend class="px-4 font-medium text-2xl">Seleceted permissions</legend>
            <details v-for="(module,index) in store.permissions"
                class=" border rounded-lg border-gray-300 p-4 mt-4">
                <summary class=" uppercase font-medium text-nowrap cursor-pointer">{{'module ' + index }}</summary>
                <br>
                <div class="grid grid-cols-4 gap-4">
                    <label v-for="permission in module" class="flex items-center gap-2 cursor-pointer">
                        <input v-model="store.selectedPermissions" :value="permission.id" type="checkbox" class="w-4 h-4">
                        <span class="text-nowrap text-xs">{{ permission.name }}</span>
                    </label>
                </div>
            </details>
        </fieldset>
        <Validate-Errors :errors="store.errors" v-if="store.errors != 0" />
        <template #footer>
            <Button @click="store.store()" text="Save" icon="save" class="btn-primary" :loading="store.loading.store" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

    <Modal :open="store.modal.edit" header="Edit new role" icon="edit">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        <Input v-model="store.role.name" label="Name" required :error="hasErrorField(store.errors,'name')" />           
        <fieldset class="border rounded-lg border-gray-500 p-4">
            <legend class="px-4 font-medium text-2xl">Seleceted permissions</legend>
            <details v-for="(module,index) in store.permissions"
                class=" border rounded-lg border-gray-300 p-4 mt-4">
                <summary class=" uppercase font-medium text-nowrap cursor-pointer">{{'module ' + index }}</summary>
                <br>
                <div class="grid grid-cols-4 gap-4">
                    <label v-for="permission in module" class="flex items-center gap-2 cursor-pointer">
                        <input v-model="store.selectedPermissions" :value="permission.id" type="checkbox" class="w-4 h-4">
                        <span class="text-nowrap text-xs">{{ permission.name }}</span>
                    </label>
                </div>
            </details>
        </fieldset>
        <Validate-Errors :errors="store.errors" v-if="store.errors != 0" />
        <template #footer>
            <Button @click="store.update()" text="Update" icon="arrows-rotate" class="btn-primary" :loading="store.loading.update" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

    <Modal :open="store.modal.delete" header="Destroy role" icon="trash" size="max-w-md">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>
        <div class="grid justify-items-center">
            <Icon icon="circle-exclamation" class="text-7xl" />
            <p class="text-xl text-center">
                Are you sure you want to delete this role?
            </p>
        </div>
        <template #footer>
            <Button @click="store.destroy()" text="Yes, i'm sure" icon="trash" class="btn-red" :loading="store.loading.destroy" />
            <Button @click="store.resetData" text="No, cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>
</template>