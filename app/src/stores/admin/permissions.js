import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { hasChanged, setToast } from '@/helpers'

export const usePermissionsStore = defineStore('permissions', () => {

    const headers = [
        { title : 'id', key : 'id', type : 'numeric' },
        { title : 'name', key : 'name' },
        { title : 'module', key : 'module' },
        { title : 'guard', key : 'guard_name' },
        { title : '', key : 'actions', width : '100px', class : 'text-end' },
    ]
    const permissions = ref([])
    const permission = ref({})
    const copy_permission = ref({})
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
            const response = await axios.get('/admin/permission')
            permissions.value = response.data.permissions
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

    const store = async() => {
        loading.value.store = true
        try {
            const response = await axios.post('/admin/permission',permission.value)
            setToast(response.data.message,'success')
            permissions.value.unshift(response.data.permission)
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
        permission.value = item
        copy_permission.value = JSON.parse(JSON.stringify(item))
        modal.value.edit = true
    }

    const update = async() => {
        loading.value.update = true
        try {
            if(hasChanged(permission.value, copy_permission.value)) {
                const response = await axios.put('/admin/permission/' + permission.value.id, permission.value)
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
        permission.value = item
        modal.value.delete = true
    }

    const destroy = async() => {
        loading.value.destroy = true
        try {
            
            const response = await axios.delete('/admin/permission/' + permission.value.id)
            
            const index = permissions.value.findIndex(permission => permission.id === response.data.permission.id)

            if (index !== -1) {
                permissions.value.splice(index, 1)
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
        permission.value = {}
        copy_permission.value = {}
        modal.value = {
            new : false,
            edit : false,
            delete : false,
        }
        errors.value = []
    }
    
    return {
        headers,
        permissions,
        permissions,
        permission,
        loading,
        modal,
        errors,

        fetch,
        store,
        edit,
        update,
        deleteItem,
        destroy,
        resetData,
    }
})
