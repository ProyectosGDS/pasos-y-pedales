import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useTimelineStore = defineStore('timeline', () => {
    
    const headers = ref([
        { title : 'expediente', key : 'id' },
        { title : 'solicitante', key : 'solicitud.primer_nombre' },
        { title : 'cui', key : 'solicitud.cui' },
        { title : 'patente', key : 'solicitud.patente_comercio' },
        { title : 'tipo persona', key : 'solicitud.tipo_persona.nombre' },
        { title : 'área solicitada', key : 'solicitud.sede.nombre' },
        { title : 'estado', key : 'workflows.estado.nombre' }
    ])
    const expediente = ref({})
    const modal = ref({
        timeline : false
    })

    const toggleTimeLine = (item) => {
        expediente.value = item
        modal.value.timeline = true
    }

    const resetData = () => {
        modal.value = {
            timeline : false
        }
        expediente.value = {}
    }

    return {
        headers,
        expediente,
        modal,

        toggleTimeLine,
        resetData
        
    }
})
