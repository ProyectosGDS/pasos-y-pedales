<script setup>
    import Upload from '@/components/Upload.vue'
    import { hasErrorField } from '@/helpers'
    import { useSolicitudStore } from '@/stores/UnidadConvivenciaSocial/PasosPedales/solicitud'
    import { onMounted } from 'vue'

    const store = useSolicitudStore()

    onMounted(() => {
        store.getZonas()
        store.getSedes()
        store.getTipoPersona()
    })
</script>
<template>
    <div>
        <ol class="flex items-center w-full p-3 space-x-2 text-sm font-medium text-center  bg-white border border-gray-200 rounded-lg shadow-xs dark:text-gray-400 sm:text-base dark:bg-gray-800 dark:border-gray-700 sm:p-4 sm:space-x-4 rtl:space-x-reverse">
            <li @click="store.toggle(1)" class="flex items-center cursor-pointer" :class="{ 'text-blue-600 dark:text-blue-500' : store.option == 1 }">
                <span
                    class="flex items-center justify-center w-5 h-5 me-2 text-xs border shrink-0 rounded-full" :class="store.option == 1 ? 'border-blue-600 rounded-full dark:border-blue-500' : 'border-gray-500 dark:border-gray-400'">
                    1
                </span>
                Datos del <span class="hidden sm:inline-flex sm:ms-2">Solicitante</span>
                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 12 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                </svg>
            </li>
            <li @click="store.toggle(2)" class="flex items-center cursor-pointer" :class="{ 'text-blue-600 dark:text-blue-500' : store.option == 2 }">
                <span
                    class="flex items-center justify-center w-5 h-5 me-2 text-xs border rounded-full shrink-0" :class="store.option == 2 ? 'border-blue-600 rounded-full dark:border-blue-500' : 'border-gray-500 dark:border-gray-400'">
                    2
                </span>
                Espacio a  <span class="hidden sm:inline-flex sm:ms-2">Solicitar</span>
                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 12 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                </svg>
            </li>
            <li @click="store.toggle(3)" class="flex items-center cursor-pointer" :class="{ 'text-blue-600 dark:text-blue-500' : store.option == 3 }">
                <span
                    class="flex items-center justify-center w-5 h-5 me-2 text-xs border rounded-full shrink-0" :class="store.option == 3 ? 'border-blue-600 rounded-full dark:border-blue-500' : 'border-gray-500 dark:border-gray-400'">
                    3
                </span>
                Documentación
            </li>
        </ol>
        <br>
        <div>
            <fieldset v-if="store.option == 1" class="border-2 p-8 rounded-lg dark:border-gray-500">
                <legend class="px-3">DATOS DEL SOLICITANTE</legend>
                <div class="lg:grid lg:grid-cols-2 gap-y-4 gap-x-12 ">
                    <Input v-model="store.solicitud.primer_nombre" label="Primer nombre" maxlength="50" icon="edit" required :error="hasErrorField(store.errors,'primer_nombre')" />
                    <Input v-model="store.solicitud.segundo_nombre" label="Segundo nombre" maxlength="50" icon="edit" required :error="hasErrorField(store.errors,'segundo_nombre')" />
                    <Input v-model="store.solicitud.primer_apellido" label="Primer apellido" maxlength="50" icon="edit" required :error="hasErrorField(store.errors,'primer_apellido')" />
                    <Input v-model="store.solicitud.segundo_apellido" label="Segundo apellido" maxlength="50" icon="edit" required :error="hasErrorField(store.errors,'segundo_apellido')" />
                    <Input v-model="store.solicitud.cui" label="Cui" maxlength="13" icon="address-card" required :error="hasErrorField(store.errors,'cui')" />
                    <Input v-model="store.solicitud.nit" label="Nit" maxlength="15" icon="address-card" required :error="hasErrorField(store.errors,'nit')" />
                    <Input v-model="store.solicitud.correo" label="Correo" type="email" icon="envelope" maxlength="100" required :error="hasErrorField(store.errors,'correo')" />
                    <Input v-model="store.solicitud.telefono" label="Teléfono" type="tel" maxlength="8" icon="phone" required :error="hasErrorField(store.errors,'telefono')" />
                    <Select v-model="store.solicitud.tipo_persona_id"
                        label="Tipo persona"
                        :options="store.tipo_personas"
                        icon="users"
                        placeholder="Seleccione un tipo persona"
                        return-type="value"
                        required 
                        :error="hasErrorField(store.errors,'tipo_persona_id')"
                    />
                    <Input v-model="store.solicitud.patente_comercio" label="Patente de comercio" maxlength="50" icon="file-signature" required :error="hasErrorField(store.errors,'patente_comercio')" />
                    <Select v-model="store.solicitud.zona_id"
                        label="Zona"
                        :options="store.zonas"
                        icon="map-location-dot"
                        placeholder="Seleccione una zona"
                        return-type="value"
                        required 
                        :error="hasErrorField(store.errors,'zona_id')"
                    />
                    <Input v-model="store.solicitud.colonia" label="Colonia" maxlength="255" icon="landmark" required :error="hasErrorField(store.errors,'colonia')" />
                    <div class="col-span-2">
                        <Input v-model="store.solicitud.domicilio" label="Domicilio" maxlength="500" icon="location-dot" required :error="hasErrorField(store.errors,'domicilio')" />
                    </div>
                    <div class="col-span-2">
                        <Text-Area v-model="store.solicitud.actividad_negocio" label="Actividad de negocio" rows="4" required :error="hasErrorField(store.errors,'actividad_negocio')" />
                    </div>
                </div>
            </fieldset>

            <fieldset v-if="store.option == 2" class="border-2 p-8 rounded-lg dark:border-gray-500">
                <legend class="px-3">ESPACIO A SOLICITAR</legend>
                <div class="grid lg:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="col-span-2">
                        <Select v-model="store.solicitud.sede_id"
                            label="Sedes"
                            :options="store.sedes"
                            placeholder="Selecciona una sede"
                            icon="map-location-dot"
                            return-type="value"
                            required
                            :error="hasErrorField(store.errors,'sede_id')"
                        />
                    </div>
                    <Input v-model="store.solicitud.ancho" label="Ancho (mts)" type="number" min="1" max="50" icon="ruler-vertical" required :error="hasErrorField(store.errors,'ancho')" />
                    <Input v-model="store.solicitud.largo" label="Largo (mts)" type="number" min="1" max="50" icon="ruler-horizontal" required :error="hasErrorField(store.errors,'largo')" />
                    <div class="col-span-2">
                       <Text-Area v-model="store.solicitud.observaciones" label="Observaciones" rows="4" :error="hasErrorField(store.errors,'observaciones')" />
                    </div>
                </div>
            </fieldset>

            <fieldset v-if="store.option == 3" class="border-2 p-8 rounded-lg dark:border-gray-500 flex gap-4 justify-center">
                <legend class="px-3">DOCUMENTACIÓN</legend>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="grid gap-2 justify-items-center">
                        <label>
                            Carta de solicitud
                            <span class="text-red-500">*</span>
                        </label>
                        <Upload v-model="store.documentos.carta_solicitud.file"
                            accept=".pdf" 
                            class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0"
                            description="pdf (max 5 mb)"
                        />
                    </div>
                    <div class="grid gap-2 justify-items-center">
                        <label>
                            Dpi
                            <span class="text-red-500">*</span>
                        </label>
                        <Upload v-model="store.documentos.dpi.file"
                            accept=".pdf" 
                            class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0"
                            description="pdf (max 5 mb)"
                        />
                    </div>
                    <div class="grid gap-2 justify-items-center">
                        <label>
                            Rtu
                            <span class="text-red-500">*</span>
                        </label>
                        <Upload v-model="store.documentos.rtu.file"
                            accept=".pdf" 
                            class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0"
                            description="pdf (max 5 mb)"
                        />
                    </div>
                    <div class="grid gap-2 justify-items-center">
                        <label>
                            Recibo servicios
                            <span class="text-red-500">*</span>
                        </label>
                        <Upload v-model="store.documentos.recibo_servicios.file"
                            accept=".pdf" 
                            class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0"
                            description="pdf (max 5 mb)"
                        />
                    </div>
                    <div class="grid gap-2 justify-items-center">
                        <label>
                            Patente comercio
                            <span class="text-red-500">*</span>
                        </label>
                        <Upload v-model="store.documentos.patente_comercio.file"
                            accept=".pdf" 
                            class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0"
                            description="pdf (max 5 mb)"
                        />
                    </div>
                    <div v-if="store.solicitud?.tipo_persona_id == 2" class="grid gap-2 justify-items-center">
                        <label>
                            Acta notarial
                            <span class="text-red-500">*</span>
                        </label>
                        <Upload v-model="store.documentos.acta_notarial.file"
                            accept=".pdf" 
                            class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0"
                            description="pdf (max 5 mb)"
                        />
                    </div>
                </div>
            </fieldset>
        </div>
        <Validate-Errors :errors="store.errors" v-if="store.errors != 0" />
        <br>
        <div class="flex justify-center gap-4">
            <Button v-if="store.option > 1" @click="store.anterior" text="Anterior" icon="arrow-left" class="btn-light" />
            <Button v-if="store.option < 3" @click="store.siguiente" text="Siguiente" icon="arrow-right" class="btn-light" />
            <Button v-if="store.option == 3" @click="store.store()" text="Enviar solicitud" icon="share" class="btn-primary" />
        </div>
    </div>
</template>