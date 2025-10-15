<script setup>
    import { onMounted } from 'vue'
    import { usePagesStore } from '@/stores/admin/pages'
    import { can, hasErrorField } from '@/helpers'
    const store = usePagesStore()

    onMounted(() => {
        store.fetch()
        store.getParents()
    })
</script>
<template>
    <div v-if="can('store page')" class="flex justify-center">
        <Button @click="store.modal.new = true" text="New page" icon="plus" class="btn-primary" />
    </div>
    <Data-Table v-if="can('view list pages')" :headers="store.headers" :data="store.pages" :loading="store.loading.fetch">
        <template #preview="{item}">
            <Icon :icon="item.icon" class="text-xl text-gray-400" />
        </template>
        <template #state="{item}">
            <Icon :icon="item ? 'check' : 'xmark'" :class="item ? 'text-green-500' : 'text-red-500'" />
        </template>
        <template #actions="{item}">
            <Drop-Down 
                icon="ellipsis-vertical" 
                variant="btn-alternative"
                :items="[
                    { label : 'Edit', icon : 'edit', action : () => store.edit(item), can : can('edit page') },
                    { label : 'Delete', icon : 'trash', action : () => store.deleteItem(item), can : can('delete page') },
                ]"
            />
        </template>
    </Data-Table>

    <Modal :open="store.modal.new" header="Create new page" icon="plus">

        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        <div class="grid lg:grid-cols-2 gap-4">
            <Input v-model="store.page.label" label="Label" required :error="hasErrorField(store.errors,'label')" />
            <Input v-model="store.page.route" label="Route" :error="hasErrorField(store.errors,'route')" />
            <Input v-model="store.page.order" label="Order" type="number" min="1" :error="hasErrorField(store.errors,'order')" />
            <div class="flex items-center gap-3">
                <Input v-model="store.page.icon" label="Icon" :error="hasErrorField(store.errors,'icon')" />
                <Icon :icon="store.page.icon" class="flex-1 text-2xl" />
            </div>
            
            <Select 
                v-model="store.page.type" 
                label="Type" 
                :options="[
                    { label : 'header', value : 'header'},
                    { label : 'parent', value : 'parent'},
                    { label : 'page', value : 'page'}
                ]"
                placeholder="Selected page type"
                return-type="value" 
                :error="hasErrorField(store.errors,'type')"
            />
            <Select 
                v-model="store.page.page_id" 
                label="Parents" 
                :options="store.parents"
                placeholder="Select parent"
                return-type="value" 
                :error="hasErrorField(store.errors,'page_id')"
            />
            
        </div>

        <template #footer>
            <Button @click="store.store()" text="Save" icon="save" class="btn-primary" :loading="store.loading.new" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

    <Modal :open="store.modal.edit" header="Edit new page" icon="edit">

        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        <div class="grid lg:grid-cols-2 gap-4">
            <Input v-model="store.page.label" label="Label" required :error="hasErrorField(store.errors,'label')" />
            <Input v-model="store.page.route" label="Route" :error="hasErrorField(store.errors,'route')" />
            <Input v-model="store.page.order" label="Order" type="number" min="1" :error="hasErrorField(store.errors,'order')" />
            <div class="flex items-center gap-3">
                <Input v-model="store.page.icon" label="Icon" :error="hasErrorField(store.errors,'icon')" />
                <Icon :icon="store.page.icon" class="flex-1 text-2xl" />
            </div>
            
            <Select 
                v-model="store.page.type" 
                label="Type" 
                :options="[
                    { label : 'header', value : 'header'},
                    { label : 'parent', value : 'parent'},
                    { label : 'page', value : 'page'}
                ]"
                placeholder="Selected page type"
                return-type="value" 
                :error="hasErrorField(store.errors,'type')"
            />
            <Select 
                v-model="store.page.page_id" 
                label="Parents" 
                :options="store.parents"
                placeholder="Select parent"
                return-type="value" 
                :error="hasErrorField(store.errors,'page_id')"
            />
            
        </div>
        <template #footer>
            <Button @click="store.update()" text="Update" icon="arrows-rotate" class="btn-primary" :loading="store.loading.update" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

    <Modal :open="store.modal.delete" header="Destroy page" icon="trash" size="max-w-md">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>
        <div class="grid justify-items-center">
            <Icon icon="circle-exclamation" class="text-7xl" />
            <p class="text-xl text-center">
                Are you sure you want to delete this page?
            </p>
        </div>
        <template #footer>
            <Button @click="store.destroy()" text="Yes, i'm sure" icon="trash" class="btn-red" :loading="store.loading.destroy" />
            <Button @click="store.resetData" text="No, cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>
</template>