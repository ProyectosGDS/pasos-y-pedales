import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { hasChanged, setToast } from '@/helpers'

export const useMenusStore = defineStore('menus', () => {

    const headers = [
        { title : 'id', key : 'id', type : 'numeric' },
        { title : 'name', key : 'name' },
        { title : 'status', key : 'state' },
        { title : '', key : 'actions' },
    ]
    const menus = ref([])
    const pages = ref([])
    const selectedPages = ref([])
    const copy_selectedPages = ref([])
    const menu = ref({})
    const copy_menu = ref({})
    const loading = ref({
        fetch : false,
        store : false,
        update : false,
        destroy : false,
    })
    const modal = ref({
        new : false,
        edit: false,
        delete : false,
    })
    const errors = ref([])

    const fetch = async() => {
        loading.value.fetch = true
        try {
            const response = await axios.get('/admin/menu')
            menus.value = response.data.menus
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

    const getPages = async() => {
        loading.value.fetch = true
        try {
            const response = await axios.get('/admin/page')
            pages.value = response.data.pages
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

    const store = async() => {
        loading.value.store = true
        try {
            const response = await axios.post('/admin/menu',{
                name : menu.value.name,
                pages : selectedPages.value
            })
            setToast(response.data.message,'success')
            menus.value.unshift(response.data.menu)
            resetData()
        } catch (error) {
            if(error.response.status == 422) {
                errors.value = error.response.data.errors
            }
        } finally {
            loading.value.store = false
        }
    }

    const edit = (item) => {
        menu.value = item
        copy_menu.value = JSON.parse(JSON.stringify(item))
        selectedPages.value = menu.value.pages.map(page => page.id)
        copy_selectedPages.value = JSON.parse(JSON.stringify(selectedPages.value))
        modal.value.edit = true
    }

    const update = async() => {
        loading.value.update = true
        try {
            if(hasChanged(menu.value, copy_menu.value) || hasChanged(selectedPages.value, copy_selectedPages.value)) {
                const response = await axios.put('/admin/menu/' + menu.value.id,{
                    name : menu.value.name,
                    pages : selectedPages.value
                })
                setToast(response.data.message,'success')
            }
            resetData()
        } catch (error) {
            if(error.response.status == 422) {
                errors.value = error.response.data.errors
            }
        } finally {
            loading.value.update = false
        }
    }

    const deleteItem = (item) => {
        menu.value = item
        modal.value.delete = true
    }

    const destroy = async() => {
        loading.value.destroy = true
        try {
            
            const response = await axios.delete('/admin/menu/' + menu.value.id)
            
            const index = menus.value.findIndex(menu => menu.id === response.data.menu.id)

            if (index !== -1) {
                menus.value.splice(index, 1)
            }

            setToast(response.data.message,'success')
            resetData()
        } catch (error) {
            if(error.response.status == 422) {
                errors.value = error.response.data.errors
            }
        } finally {
            loading.value.destroy = false
        }
    }


    const resetData = () => {
        menu.value = {}
        copy_menu.value = {}
        modal.value = {
            new : false,
            edit : false,
            delete : false,
        }
        selectedPages.value = []
        copy_selectedPages.value = []
        errors.value = []
    }
    
    return {
        headers,
        menus,
        pages,
        selectedPages,
        copy_selectedPages,
        menu,
        loading,
        modal,
        errors,

        fetch,
        getPages,
        store,
        edit,
        update,
        deleteItem,
        destroy,
        resetData,
    }
})
