import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { hasChanged, setToast } from '@/helpers'

export const useRolesStore = defineStore('roles', () => {

    const headers = [
        { title : 'id', key : 'id', type : 'numeric' },
        { title : 'name', key : 'name' },
        { title : 'status', key : 'state' },
        { title : '', key : 'actions', width : '100px', class : 'text-end' },
    ]
    const roles = ref([])
    const permissions = ref([])
    const selectedPermissions = ref([])
    const copy_selectedPermissions = ref([])
    const role = ref({})
    const copy_role = ref({})
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
            const response = await axios.get('/admin/role')
            roles.value = response.data.roles
            permissions.value = response.data.permissions
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

    const store = async() => {
        loading.value.store = true
        try {
            const response = await axios.post('/admin/role',{
                name : role.value.name,
                permissions : selectedpermissions.value
            })
            setToast(response.data.message,'success')
            roles.value.unshift(response.data.role)
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
        role.value = item
        copy_role.value = JSON.parse(JSON.stringify(item))
        selectedPermissions.value = item.permissions.map(permission => permission.id)
        copy_selectedPermissions.value = JSON.parse(JSON.stringify(selectedPermissions.value))
        modal.value.edit = true
    }

    const update = async() => {
        loading.value.update = true
        try {
            if(hasChanged(role.value, copy_role.value) || hasChanged(selectedPermissions.value, copy_selectedPermissions.value)) {
                const response = await axios.put('/admin/role/' + role.value.id,{
                    name : role.value.name,
                    permissions : selectedPermissions.value
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
        role.value = item
        modal.value.delete = true
    }

    const destroy = async() => {
        loading.value.destroy = true
        try {
            
            const response = await axios.delete('/admin/role/' + role.value.id)
            
            const index = roles.value.findIndex(role => role.id === response.data.role.id)

            if (index !== -1) {
                roles.value.splice(index, 1)
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
        role.value = {}
        copy_role.value = {}
        modal.value = {
            new : false,
            edit : false,
            delete : false,
        }
        selectedPermissions.value = []
        copy_selectedPermissions.value = []
        errors.value = []
    }
    
    return {
        headers,
        roles,
        permissions,
        selectedPermissions,
        copy_selectedPermissions,
        role,
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
