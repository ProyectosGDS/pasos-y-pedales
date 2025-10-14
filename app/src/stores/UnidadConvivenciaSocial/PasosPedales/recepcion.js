import { setToast } from '@/helpers'
import axios from 'axios'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useRecepcionStore = defineStore('recepcion', () => {
  
    const headers = ref([
        { title : 'expediente', key : 'id', type : 'numeric' },
        { title : 'solicitante', key : 'solicitud.nombre_completo' },
        { title : 'cui', key : 'solicitud.cui', icon : 'address-card' },
        { title : 'patente', key : 'solicitud.patente_comercio', icon : 'building-user' },
        { title : 'tipo persona', key : 'solicitud.tipo_persona.nombre', icon : 'users' },
        { title : 'área solicitada', key : 'solicitud.sede.nombre', icon : 'location-dot' },
        { title : 'status', key : 'latest_workflow.estado.nombre' },
        { title : '', key : 'actions' },
    ])
    
    const solicitudes = ref([])
    const solicitud = ref({})
    const urlDoc = ref(null)
    const loading = ref({
        fetch : false,
        changeState : false,
    })
    const modal = ref({
        view : false,
    })
    const option = ref(1)

    
    const errors = ref({
        rechazar : [],
    })

    const fetch = async () => {
        loading.value.fetch = true
        try {

            const response = await axios.get('pasos-y-pedales/solicitudes/ingresadas')
            solicitudes.value = response.data.solicitudes
            
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

    const acceptRequest = async () => {
        loading.value.changeState = true
        try {

            const response = await axios.put('pasos-y-pedales/solicitudes/aceptar-solicitud/' + solicitud.value.id,{
                observacion : solicitud.value.latest_workflow.observacion
            })

            const index = solicitudes.value.findIndex(solicitud => solicitud.id === response.data.expediente.id)

            if (index !== -1) {
                solicitudes.value.splice(index, 1)
            }
            resetData()

            setToast(response.data.message,'success')
            
        } catch (error) {

        } finally {
            loading.value.changeState = false
        }
    }

    const rejectRequest = async () => {
        loading.value.changeState = true
        try {

            const response = await axios.put('pasos-y-pedales/solicitudes/rechazar-solicitud/' + solicitud.value.id,{
                observacion : solicitud.value.latest_workflow.observacion
            })

            fetch()

            resetData()

            setToast(response.data.message,'success')
            
        } catch (error) {
            if(error.response.status == 422 ) {
                errors.value.rechazar = error.response.data.errors
            }
        } finally {
            loading.value.changeState = false
        }
    }

    const reviewingRequest = async () => {
        loading.value.changeState = true
        try {
            const response = await axios.put('pasos-y-pedales/solicitudes/revisar-solicitud/' + solicitud.value.id)
            setToast(response.data.message,'success')
        } catch (error) {

        } finally {
            loading.value.changeState = false
        }
    }

    const view = async (item) => {
        solicitud.value = item
        if(![2,7].includes(item.latest_workflow.estado_id)) {
            await reviewingRequest()
        }
        modal.value.view = true
    }

    const previewDoc = (url) => {
        loading.value.pdf = true
        urlDoc.value = url
    }

    function toggle(number) {
        option.value = number
    }

    const resetData = () => {
        solicitud.value = {},
        modal.value = {
            view : false
        }
        errors.value = {
            rechazar : []
        }
        option.value = 1
        urlDoc.value = null
    }
    
    return {
        headers,
        solicitudes,
        solicitud,
        urlDoc,
        option,
        modal,
        loading,
        errors,

        fetch,
        view,
        toggle,
        previewDoc,
        reviewingRequest,
        acceptRequest,
        rejectRequest,
        resetData, 
    }
})
