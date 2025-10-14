import { setToast } from '@/helpers'
import axios from 'axios'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAsignacionStore = defineStore('asignacion', () => {
  
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
    const sedes = ref([])
    const urlDoc = ref(null)
    const loading = ref({
        fetch : false,
        sedes : false,
        changeState : false,
    })
    const modal = ref({
        view : false,
    })
    const option = ref(1)

    
    const errors = ref({
        asignar : [],
        rechazar : [],
    })

    const getSedes = async () => {
        loading.value.sedes = true
        try {
            const response = await axios.get('pasos-y-pedales/sedes')
            sedes.value = response.data.sedes.map(sede => {
                return { label : sede.nombre, value : sede.id, icon : 'building' }
            })
        } catch (error) {

        } finally {
            loading.value.sedes = false
        }
    }

    const fetch = async () => {
        loading.value.fetch = true
        try {

            const response = await axios.get('pasos-y-pedales/solicitudes/aceptadas')
            expedientes.value = response.data.expedientes
            
        } catch (error) {

        } finally {
            loading.value.fetch = false
        }
    }

    const verifySpace = async () => {
        loading.value.changeState = true
        try {
            const response = await axios.put('pasos-y-pedales/solicitudes/verificar-espacio/' + expediente.value.id)
            setToast(response.data.message,'success')
        } catch (error) {

        } finally {
            loading.value.changeState = false
        }
    }

    const assignSpace = async () => {
        loading.value.changeState = true
        try {

            const response = await axios.put('pasos-y-pedales/solicitudes/asignar-espacio/' + expediente.value.id, expediente.value)

            const index = expedientes.value.findIndex(expediente => expediente.id === response.data.expediente.id)

            if (index !== -1) {
                expedientes.value.splice(index, 1)
            }

            resetData()

            setToast(response.data.message,'success')
            
        } catch (error) {
            if(error.response.status == 422 ) {
                errors.value.asignar = error.response.data.errors
            }
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
                errors.value.rechazar = error.response.data.errors
            }
        } finally {
            loading.value.changeState = false
        }
    }

    const view = async (item) => {
        expediente.value = item
        if(item.latest_workflow.estado_id != 4) {
            await verifySpace()
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
        expediente.value = {},
        modal.value = {
            view : false
        }
        errors.value = {
            asignar : [],
            rechazar : [],
        }
        option.value = 1
        urlDoc.value = null
    }
    
    return {
        headers,
        expedientes,
        expediente,
        sedes,
        urlDoc,
        option,
        modal,
        loading,
        errors,

        getSedes,
        fetch,
        view,
        toggle,
        previewDoc,
        verifySpace,
        assignSpace,
        rejectRequest,
        resetData, 
    }
})
