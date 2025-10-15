<script setup>
    import { onMounted } from 'vue';
    import { useMenusStore } from '@/stores/admin/menus'
    import { can } from '@/helpers'
    const store = useMenusStore()

    onMounted(() => {
        store.fetch()
        store.getPages()
    })
</script>
<template>
    <div v-if="can('store menu')" class="flex justify-center">
        <Button @click="store.modal.new = true" text="New menu" icon="plus" class="btn-primary" />
    </div>

    <Data-Table v-if="can('view list menus')" :headers="store.headers" :data="store.menus" :loading="store.loading.fetch">
        <template #state="{item}">
            <Icon :icon="item ? 'check' : 'xmark'" :class="item ? 'text-green-500' : 'text-red-500'" />
        </template>
        <template #actions="{item}">
            <Drop-Down 
                icon="ellipsis-vertical" 
                variant="btn-alternative"
                :items="[
                    { label : 'Edit', icon : 'edit', action : () => store.edit(item), can : can('edit menu') },
                    { label : 'Delete', icon : 'trash', action : () => store.deleteItem(item), can : can('delete menu') },
                ]"
            />
        </template>
    </Data-Table>

    <Modal :open="store.modal.new" header="Create new menu" icon="plus">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        <Input v-model="store.menu.name" label="Name" required />           
        <fieldset class="border rounded-lg border-gray-500 p-4">
            <legend class="px-4 font-medium text-2xl">Seleceted pages</legend>
            <div class="grid grid-cols-2 justify-items-center xl:grid-cols-4 gap-4">
                <label v-for="page in store.pages" class="flex items-center gap-2 cursor-pointer">
                    <input v-model="store.selectedPages" :value="page.id" type="checkbox" class="w-4 h-4">
                    <div class="grid">
                        <span>{{ page.label }}</span>
                        <span class="text-[9px]">({{ page.type }})</span>
                    </div>
                </label>
            </div>
        </fieldset>

        <template #footer>
            <Button @click="store.store()" text="Save" icon="save" class="btn-primary" :loading="store.loading.store" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

    <Modal :open="store.modal.edit" header="Edit new menu" icon="edit">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>

        <Input v-model="store.menu.name" label="Name" required />           
        <fieldset class="border rounded-lg border-gray-500 p-4">
            <legend class="px-4 font-medium text-2xl">Seleceted pages</legend>
            <div class="grid grid-cols-2 justify-items-center xl:grid-cols-4 gap-4">
                <label v-for="page in store.pages" class="flex items-center gap-2 cursor-pointer">
                    <input v-model="store.selectedPages" :value="page.id" type="checkbox" class="w-4 h-4">
                    <div class="grid">
                        <span>{{ page.label }}</span>
                        <span class="text-[9px]">({{ page.type }})</span>
                    </div>
                </label>
            </div>
        </fieldset>
        <template #footer>
            <Button @click="store.update()" text="Update" icon="arrows-rotate" class="btn-primary" :loading="store.loading.update" />
            <Button @click="store.resetData" text="Cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>

    <Modal :open="store.modal.delete" header="Destroy menu" icon="trash" size="max-w-md">
        <template #closed>
            <Button @click="store.resetData" icon="xmark" clas="btn-light" />
        </template>
        <div class="grid justify-items-center">
            <Icon icon="circle-exclamation" class="text-7xl" />
            <p class="text-xl text-center">
                Are you sure you want to delete this menu?
            </p>
        </div>
        <template #footer>
            <Button @click="store.destroy()" text="Yes, i'm sure" icon="trash" class="btn-red" :loading="store.loading.destroy" />
            <Button @click="store.resetData" text="No, cancel" icon="xmark" class="btn-alternative" />
        </template>
    </Modal>
</template>