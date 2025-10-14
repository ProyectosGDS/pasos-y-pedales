import { useAuthStore } from '@/stores/auth'
import { useGlobalStore } from '@/stores/global'

export const can = (permission) => {
    const authStore = useAuthStore()
    return authStore.userPermissions.includes(permission)
}

export const setToast = (message, type, title = 'Atención') => {
    const global = useGlobalStore()
    global.toasts.unshift({ message : message, type : type, title : title })
}

export const updatePropertyObject = (struct, newData) => {
    Object.assign(struct, newData)
}

export const goHome = () => {
    window.location.href = import.meta.env.VITE_MY_URL
}

export const checkIfCookieExists = (cookieName) => {
    const cookies = document.cookie.split('')
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim()
        if (cookie.startsWith(cookieName + '=')) {
            return true
        }
    }
    return false
}

export const getNestedValue = (obj, key) => {
    const keys = key.split('.')
    return keys.reduce((value, currentKey) => {
        return value && value[currentKey]
    }, obj)
}

export const hasChanged = (target, source) => {
    
    if (target === source) return false
           
        if (target === null || typeof target !== 'object' || source === null || typeof source !== 'object') {
            return target !== source
        }
        
        const keys1 = Object.keys(target)
        const keys2 = Object.keys(source)
          
        if (keys1.length !== keys2.length) return true
           
        for (let key of keys1) {
            if (!keys2.includes(key) || hasChanged(target[key], source[key])) {
                return true
            }
        }
    
        return false
}

export const formatVal = (value, type) => {
     let result

    switch (type) {
        case 'numeric':
            result = new Intl.NumberFormat("es-GT").format(value)
            break

        case 'currency':
            result = new Intl.NumberFormat("es-GT", {
                'style': "currency",
                'currency': "GTQ",
                'minimumFractionDigits': 2,
            }).format(value)
            break

        case 'date':
                const date = new Date(value)
                const d = String(date.getDate()).padStart(2,'0')
                const m = String(date.getMonth() + 1).padStart(2,'0')
                const y = String(date.getFullYear())

                result = value ? `${y}-${m}-${d}` : ''

            break

        case 'datetime':
            
            const fecha = new Date(value)
            const dia = fecha.getDate().toString().padStart(2,'0')
            const mes = fecha.getMonth().toString().padStart(2,'0')
            const anio = fecha.getFullYear().toString()
            const h = fecha.getHours().toString().padStart(2,'0')
            const mi = fecha.getMinutes().toString().padStart(2,'0')
            const s = fecha.getSeconds().toString().padStart(2,'0')

            result = `${anio}-${mes}-${dia} ${h}:${mi}:${s}`

            break

        case 'time':
            
            const f = new Date(value)
            const hours = f.getHours().toString().padStart(2,'0')
            const minutes = f.getMinutes().toString().padStart(2,'0')
            const seconds = f.getSeconds().toString().padStart(2,'0')

            result = `${hours}:${minutes}:${seconds}`

            break

        case 'phone':
            if(value == null) return ''
            
            const phone = value.toString()
            result = `${phone.substring(0,4)} - ${phone.substring(4,8)}`

            break

        default:
            result = value
            break
    }

    return result
}

export const hasErrorField = (bagError, nameField ) => {
    return bagError.hasOwnProperty(nameField)
}

export const handleError = (error) => {

    if (!error.response) {
        console.error('No se recibió respuesta del servidor:', error.request)
        setToast('No se pudo conectar con el servidor','danger','ERROR')
        return
    }

    const { status, data } = error.response

    switch (status) {
        case 422:
            setToast(data.message || 'Error de validación','warning','ERROR DE VALIDACIÓN')
            console.error('Error de validación:', data.errors ?? null)
            break
        case 401:
            setToast(data.message || 'No autorizado','danger','NO AUTORIZADO')
            console.error('No autorizado',data.errors ?? 'No hay errores')
            break
        case 404:
            setToast(data.message || 'Recurso no encontrado','danger','RECURSO NO ENCONTRADO')
            console.error('Recurso no encontrado:', data.message)
            break
        default:
            if (status >= 500) {
                setToast(data.message || 'Error del servidor','danger','ERROR DEL SERVIDOR')
                console.error('Error del servidor:', data.message)
            } else {
                setToast(data.message || 'Error desconocido','danger','ERROR')
                console.error('Error desconocido:', data)
            }
            break
    }
}
