import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { hasChanged, setToast } from '@/helpers'
import { useAuthStore } from '../auth'

export const useUsersStore = defineStore('users', () => {


    const auth = useAuthStore()
    const headers = [
        { title : 'user', key : 'information.full_name' },
        { title : 'birthday', key : 'information.birthday', icon : 'cake' },
        { title : 'profile', key : 'profile.name', icon : 'tag' },
        { title : 'created at', key : 'created_at', type : 'date' },
        { title : 'state', key : 'deleted_at' },
    ]
    const users = ref([])
    
    const user = ref({
        information : {}
    })
    const copy_user = ref({})
    const profiles = ref([])
    const picture = ref(null)
    const change = ref(false)
    const loading = ref({
        fetch : false,
        store : false,
        update : false,
        reset : false,
        disabled : false,
        upload : false,
        delete : false,
    })
    const modal = ref({
        new : false,
    })
    const errors = ref({
        info : [],
        pass : [],
        upload : [],
        delete : [],
    })

    const getProfiles = async() => {
        loading.value.fetch = true
        try {
            const response = await axios.get('/admin/profile')
            profiles.value = response.data.profiles.map(profile => {
                return {
                    label : profile.name,
                    value : profile.id
                }
            })
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

    const fetch = async() => {
        loading.value.fetch = true
        try {
            const response = await axios.get('/admin/user')
            users.value = response.data.users
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

     const show = async (id) => {
        loading.value.fetch = true
        try {
            const response = await axios.get('/admin/user/' + id)
            user.value = response.data.user
            user.value.profile = response.data.user.profile ?? {}
            copy_user.value = JSON.parse(JSON.stringify(response.data.user))
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

    const store = async() => {
        loading.value.store = true
        try {
            const response = await axios.post('/admin/user',user.value.information)
            users.value.unshift(response.data.user)
            setToast(response.data.message,'success')
            resetData()
        } catch (error) {
            if(error.response.status == 422) {
                errors.value.info = error.response.data.errors
            }
        } finally {
            loading.value.store = false
        }
    }

    const update = async () => {
        loading.value.update = true
        try {
            if(!hasChanged(user.value,copy_user.value,)) return

            const response = await axios.put('admin/user/' + user.value.id , user.value)
            auth.verifyAuth
            setToast(response.data.message,'success')

        } catch (error) {
            if(error.response.status == 422) {
                errors.value.update = error.response.data.errors
            }
            console.error(error)
        }finally {
            loading.value.update = false
        }
    }

    const disabledUser = async() => {
        loading.value.disabled = true
        try { 
            const response = await axios.delete('/admin/user/disabled/' + user.value.id)
            setToast(response.data.message,'success')
            resetData()
        } catch (error) {
            if(error.response.status == 422) {
                errors.value = error.response.data.errors
            }
        } finally {
            loading.value.disabled = false
        }
    }

    const resetPassword = async () => {
        loading.value.reset = true
        try {
            const response = await axios.put('/admin/user/reset-password/' + user.value.id)
            setToast(response.data.message,'success')
            resetData()
        } catch (error) {
            if(error.response.status == 422) {
                errors.value = error.response.data.errors
            }
        } finally {
            loading.value.reset = false
        }
    }

    const getFile = (file) => {
        picture.value = file
    }

    const uploadPicture = async () => {
        loading.value.upload = true
        try {

            const formData = new FormData()
            formData.append('file',picture.value)

            const response = await axios.post('profile/upload-picture/' + auth.user.id ,formData, {
                headers : {
                    'Content-Type' : 'multipart/form-data',
                }
            })
            auth.user.url_photo = response.data.url_photo
            information.value.url_photo = response.data.url_photo
            setToast(response.data.message,'primary')
            resetData()
        } catch (error) {
            if(error.response.status == 422) {
                errors.value.upload = error.response.errors
            }
        } finally {
            loading.value.upload = false
        }
    }

    const deletePicture = async () => {
        loading.value.delete = true
        try {
            const response = await axios.delete('profile/delete-picture/' + auth.user.id )
            setToast(response.data.message,'success')
            auth.user.url_photo = null
            user.value.information.url_photo = null
            resetData()
        } catch (error) {
            if(error.response.status == 422) {
                errors.value.delete = error.response.errors
            }
        } finally {
            loading.value.delete = false
        }
    }

    const resetData = () => {
        user.value = {
            information : {},
        }
        copy_user.value = {}
        modal.value = {
            new : false,
        }
        errors.value = {
            info : [],
            pass : [],
            upload : [],
            delete : [],
        },
        
        picture.value = null
    }
    
    return {
        headers,
        users,
        user,
        profiles,
        picture,
        change,
        loading,
        modal,
        errors,

        fetch,
        getProfiles,
        show,
        store,
        update,
        disabledUser,
        resetPassword,
        getFile,
        uploadPicture,
        deletePicture,
        resetData,
    }
})
