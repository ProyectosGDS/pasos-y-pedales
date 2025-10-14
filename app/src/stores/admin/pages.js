import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { hasChanged, setToast } from '@/helpers'

export const usePagesStore = defineStore('pages', () => {

    const headers = [
        { title : 'id', key : 'id', type : 'numeric' },
        { title : 'label', key : 'label' },
        { title : 'route', key : 'route' },
        { title : 'icon', key : 'icon' },
        { title : 'preview', key : 'preview'},
        { title : 'parent', key : 'parent.label' },
        { title : 'type', key : 'type' },
        { title : 'order', key : 'order'},
        { title : 'status', key : 'state' },
        { title : '', key : 'actions' },
    ]
    const parents = ref([])
    const pages = ref([])
    const page = ref({})
    const copy_page = ref({})
    const loading = ref({
        parents : false,
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

    const getParents = async() => {
        loading.value.parents = true
        try {
            const response = await axios.get('/admin/page/get-parents')
            parents.value = response.data
        } catch (error) {

        } finally {
            loading.value.parents = false
        }
    }

    const fetch = async() => {
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
            const response = await axios.post('/admin/page',page.value)
            pages.value.unshift(response.data.page)
            setToast(response.data.message,'success')
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
        page.value = item
        copy_page.value = JSON.parse(JSON.stringify(item))
        modal.value.edit = true
    }

    const update = async() => {
        loading.value.update = true
        try {
            if(hasChanged(page.value, copy_page.value)) {
                const response = await axios.put('/admin/page/' + page.value.id,page.value)
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
        page.value = item
        modal.value.delete = true
    }

    const destroy = async() => {
        loading.value.destroy = true
        try {
            
            const response = await axios.delete('/admin/page/' + page.value.id)
            
            const index = pages.value.findIndex(page => page.id === response.data.page.id)
            if (index !== -1) {
                pages.value.splice(index, 1)
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
        page.value = {}
        copy_page.value = {}
        modal.value = {
            new : false,
            edit : false,
            delete : false,
        }
        errors.value = []
    }
    
    return {
        headers,
        parents,
        pages,
        page,
        loading,
        modal,
        errors,

        getParents,
        fetch,
        store,
        edit,
        update,
        deleteItem,
        destroy,
        resetData,
    }
})
