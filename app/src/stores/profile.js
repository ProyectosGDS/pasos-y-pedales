import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useAuthStore } from './auth'
import axios from 'axios'
import { hasChanged, setToast } from '@/helpers'
import { useRouter } from 'vue-router'

export const useProfileStore = defineStore('profile', () => {
  
    const auth = useAuthStore()
    const router = useRouter()

    const information = ref({})
    const sessions = ref([])
    const copy_information = ref({})
    const picture = ref(null)
    const change = ref(false)
    const passwords = ref({
        current : '',
        new : '',
        new_confirmation : ''
    })

    const modal = ref({
        upload : false
    })

    const loading = ref({
        fetch : false,
        update : false,
        pass : false,
        upload : false,
        delete : false,
    })

    const errors = ref({
        info : [],
        pass : [],
        upload : [],
        delete : [],
    })

    const fetch = async () => {
        loading.value.fetch = true
        try {
            const response = await axios.get('profile/' + auth.user.id)
            information.value = response.data.information
            copy_information.value = JSON.parse(JSON.stringify(response.data.information))
            sessions.value = response.data.sessions
        } catch (error) {
            return false
        }finally {
            loading.value.fetch = false
        }
    }

    const update = async () => {
        loading.value.update = true
        try {
            if(!hasChanged(information.value,copy_information.value,)) return

            const response = await axios.put('profile/' + auth.user.id , information.value)
            auth.verifyAuth()
            Object.assign(information.value,response.data.information)
            Object.assign(copy_information.value,response.data.information)
            setToast(response.data.message,'success')

        } catch (error) {
            if(error.response.status == 422) {
                errors.value.info = error.response.data.errors
            }
        }finally {
            loading.value.update = false
        }
    }

    const changePassword = async () =>{
        loading.value.pass = true
        try {
            const response = await axios.put('profile/change-password/' + auth.user.id ,passwords.value)
            setToast(response.data.message,'primary')
            auth.logout()
            router.push({name : 'Login'})
        } catch (error) {
            if(error.response.status == 422) {
                errors.value.pass = error.response.data.errors
            }
        } finally {
            loading.value.pass = false
        }
    }

    const uploadPhoto = async () => {
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
            information.value.url_photo = null
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
        modal.value = {
            upload : false
        }

        errors.value = {
            info : [],
            pass : [],
            upload : [],
        }

        picture.value = null
        change.value = false
    }
    
    return {
        information,
        copy_information,
        picture,
        sessions,
        change,
        passwords,
        modal,
        loading,
        errors,

        fetch,
        update,
        changePassword,
        uploadPhoto,
        deletePicture,
        resetData,
        
    }
})
