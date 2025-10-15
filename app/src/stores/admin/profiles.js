import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { hasChanged, setToast } from '@/helpers'

export const useProfilesStore = defineStore('profiles', () => {

    const headers = [
        { title : 'id', key : 'id', type : 'numeric' },
        { title : 'name', key : 'name' },
        { title : 'description', key : 'description' },
        { title : 'menu', key : 'menu.name' },
        { title : 'role', key : 'role.name' },
        { title : 'status', key : 'state' },
        { title : '', key : 'actions', width : '100px', class : 'text-end' },
    ]
    const profiles = ref([])
    const profile = ref({})
    const copy_profile = ref({})
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
            const response = await axios.get('/admin/profile')
            profiles.value = response.data.profiles
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

    const store = async() => {
        loading.value.store = true
        try {
            const response = await axios.post('/admin/profile',profile.value)
            setToast(response.data.message,'success')
            profiles.value.unshift(response.data.profile)
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
        profile.value = item
        copy_profile.value = JSON.parse(JSON.stringify(item))
        modal.value.edit = true
    }

    const update = async() => {
        loading.value.update = true
        try {
            if(hasChanged(profile.value, copy_profile.value)) {
                const response = await axios.put('/admin/profile/' + profile.value.id,profile.value)
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
        profile.value = item
        modal.value.delete = true
    }

    const destroy = async() => {
        loading.value.destroy = true
        try {
            
            const response = await axios.delete('/admin/profile/' + profile.value.id)
            
            const index = profiles.value.findIndex(profile => profile.id === response.data.profile.id)

            if (index !== -1) {
                profiles.value.splice(index, 1)
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
        profile.value = {}
        copy_profile.value = {}
        modal.value = {
            new : false,
            edit : false,
            delete : false,
        }
        errors.value = []
    }
    
    return {
        headers,
        profiles,
        profile,
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
