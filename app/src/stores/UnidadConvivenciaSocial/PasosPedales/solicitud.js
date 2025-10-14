import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { setToast } from '@/helpers'


export const useSolicitudStore = defineStore('solicitud', () => {

    const option = ref(1)
    const solicitud = ref({})
    const documentos = ref({
        carta_solicitud: {
            file: null,
            name: 'file_carta_solicitud',
        },
        dpi: {
            file: null,
            name: 'file_dpi',
        },
        rtu: {
            file: null,
            name: 'file_rtu',
        },
        recibo_servicios: {
            file: null,
            name: 'file_recibo_servicios',
        },
        patente_comercio: {
            file: null,
            name: 'file_patente_comercio',
        },
        acta_notarial: {
            file: null,
            name: 'file_acta_notarial',
        }
    })
    const sedes = ref([])
    const zonas = ref([])
    const tipo_personas = ref([])
    const loading = ref({
        store: false,
        sedes: false,
        zonas: false,
        tipo_persona: false,
    })
    const errors = ref([])

    const getZonas = async () => {
        loading.value.zonas = true
        try {
            const response = await axios.get('zonas')
            zonas.value = response.data.zonas.map(zona => {
                return { label: zona.nombre, value: zona.id }
            })
        } catch (error) {
            console.error(error)
        } finally {
            loading.value.zonas = false
        }
    }

    const getSedes = async () => {
        loading.value.sedes = true
        try {
            const response = await axios.get('pasos-y-pedales/sedes')
            sedes.value = response.data.sedes.map(sede => {
                return { label: sede.nombre, value: sede.id }
            })
        } catch (error) {
            console.error(error)
        } finally {
            loading.value.sedes = false
        }
    }

    const getTipoPersona = async () => {
        loading.value.tipo_persona = true
        try {
            const response = await axios.get('pasos-y-pedales/tipo-personas')
            tipo_personas.value = response.data.tipo_personas.map(tipo => {
                return { label: tipo.nombre, value: tipo.id }
            })
        } catch (error) {
            console.error(error)
        } finally {
            loading.value.tipo_persona = false
        }
    }

    const store = async () => {
        loading.value.store = true

        const formData = new FormData()

        for (const key in solicitud.value) {
            formData.append(key, solicitud.value[key])
        }

        for (const key in documentos.value) {
            if(documentos.value[key].file != null) {
                formData.append(documentos.value[key].name, documentos.value[key].file)
            }
        }

        try {
            const response = await axios.post('pasos-y-pedales/solicitudes', formData)
            setToast(response.data.message, 'success')
            resetData()
        } catch (error) {
            if (error.response.status == 422) {
                errors.value = error.response.data.errors
            }
        } finally {
            loading.value.store = false
        }
    }

    const toggle = (step) => {
        option.value = step
    }

    const siguiente = () => {
        if (option.value < 3 && option.value > 0) {
            option.value++;
        }
    }

    const anterior = () => {
        if (option.value > 1) {
            option.value--;
        }
    }

    const resetData = () => {
        solicitud.value = {}
        errors.value = []
    }

    return {
        option,
        solicitud,
        documentos,
        sedes,
        zonas,
        tipo_personas,
        errors,

        getZonas,
        getSedes,
        getTipoPersona,
        store,
        toggle,
        siguiente,
        anterior,
        resetData
    }
})
