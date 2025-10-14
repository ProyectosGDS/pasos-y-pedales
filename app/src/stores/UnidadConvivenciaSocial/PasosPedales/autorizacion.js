import { setToast } from '@/helpers'
import axios from 'axios'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAutorizacionStore = defineStore('autorizacion', () => {
  
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
    
    const expedientes = ref([])
    const expediente = ref({})
    const urlDoc = ref(null)
    const loading = ref({
        fetch : false,
        changeState : false,
    })
    const modal = ref({
        view : false,
    })
    const option = ref(1)

    
    const errors = ref([])


    const fetch = async () => {
        loading.value.fetch = true
        try {

            const response = await axios.get('pasos-y-pedales/solicitudes/asignadas')
            expedientes.value = response.data.expedientes
            
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

    const rejectAssign = async () => {
        loading.value.changeState = true
        try {
            const response = await axios.put('pasos-y-pedales/solicitudes/rechazar-asignacion/' + expediente.value.id,{
                observacion : expediente.value.latest_workflow.observacion
            })
            const index = expedientes.value.findIndex(expediente => expediente.id === response.data.expediente.id)

            if (index !== -1) {
                expedientes.value.splice(index, 1)
            }
            
            setToast(response.data.message,'success')
            resetData()
        } catch (error) {
            if(error.response.status == 422) {
                errors.value = error.response.data.errors
            }
        } finally {
            loading.value.changeState = false
        }
    }

    const authorizedRequest = async () => {
        loading.value.changeState = true
        try {

            const response = await axios.put('pasos-y-pedales/solicitudes/autorizar-solicitud/' + expediente.value.id, expediente.value)

            const index = expedientes.value.findIndex(expediente => expediente.id === response.data.expediente.id)

            if (index !== -1) {
                expedientes.value.splice(index, 1)
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

            const response = await axios.put('pasos-y-pedales/solicitudes/rechazar-solicitud/' + expediente.value.id,{
                observacion : expediente.value.latest_workflow.observacion
            })

            fetch()

            resetData()

            setToast(response.data.message,'success')
            
        } catch (error) {
            if(error.response.status == 422 ) {
                errors.value = error.response.data.errors
            }
        } finally {
            loading.value.changeState = false
        }
    }

    const view = async (item) => {
        expediente.value = item
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
        expediente.value = {},
        modal.value = {
            view : false
        }
        errors.value = []
        option.value = 1
        urlDoc.value = null
    }
    
    return {
        headers,
        expedientes,
        expediente,
        urlDoc,
        option,
        modal,
        loading,
        errors,

        fetch,
        view,
        toggle,
        previewDoc,
        rejectAssign,
        authorizedRequest,
        rejectRequest,
        resetData, 
    }
})
