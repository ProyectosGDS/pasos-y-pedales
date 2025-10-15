<script setup>
    import { onMounted } from 'vue'
    import { usePermissionsStore } from '@/stores/admin/permissions'
    import { can, hasErrorField } from '@/helpers'
    const store = usePermissionsStore()

    onMounted(() => {
        store.fetch()
    })
</script>
<template>
    <div v-if="can('store permission')" class="flex justify-center">
        <Button @click="store.modal.new = true" text="New permission" icon="plus" class="btn-primary" />
    </div>

    <Data-Table v-if="can('view list permissions')" :headers="store.headers" :data="store.permissions" :loading="store.loading.fetch">
        <template #actions="{item}">
            <Drop-Down 
                icon="ellipsis-vertical" 
                variant="btn-alternative"
                :items="[
                    { label : 'Edit', icon : 'edit', action : () => store.edit(item), can : can('edit permission') },
                    { label : 'Delete', icon : 'trash', action : () => store.deleteItem(item), can : can('delete permission') },
                ]"
            />
        </template>
    </Data-Table>

    <Modal :open="store.modal.new" header="Create new permission" icon="plus">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        <Input v-model="store.permission.name" label="Name" required :error="hasErrorField(store.errors,'name')" />                    
        <Input v-model="store.permission.module" label="Module" required :error="hasErrorField(store.errors,'module')" />                    
        <Validate-Errors :errors="store.errors" v-if="store.errors != 0" />
        <template #footer>
            <Button @click="store.store()" text="Save" icon="save" class="btn-primary" :loading="store.loading.store" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

    <Modal :open="store.modal.edit" header="Edit new permission" icon="edit">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        <Input v-model="store.permission.name" label="Name" required :error="hasErrorField(store.errors,'name')" />           
        <Input v-model="store.permission.module" label="Module" required :error="hasErrorField(store.errors,'module')" />           
        <Validate-Errors :errors="store.errors" v-if="store.errors != 0" />
        <template #footer>
            <Button @click="store.update()" text="Update" icon="arrows-rotate" class="btn-primary" :loading="store.loading.update" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

    <Modal :open="store.modal.delete" header="Destroy permission" icon="trash" size="max-w-md">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>
        <div class="grid justify-items-center">
            <Icon icon="circle-exclamation" class="text-7xl" />
            <p class="text-xl text-center">
                Are you sure you want to delete this permission?
            </p>
        </div>
        <template #footer>
            <Button @click="store.destroy()" text="Yes, i'm sure" icon="trash" class="btn-red" :loading="store.loading.destroy" />
            <Button @click="store.resetData" text="No, cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>
</template>