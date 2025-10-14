<script setup>
    import { formatVal, hasErrorField } from '@/helpers';
    import { useRecepcionStore } from '@/stores/UnidadConvivenciaSocial/PasosPedales/recepcion'
    import { onMounted } from 'vue';
    
    const store = useRecepcionStore()

    onMounted(() => store.fetch())

</script>
<template>
    <Data-Table :headers="store.headers" :data="store.solicitudes" :loading="store.loading.fetch">
        <template #solicitud.nombre_completo="{item}">
            <div class="grid">
                <span class="flex gap-1 items-center text-lg">
                    <Icon icon="user" class="text-sm" />
                    {{ item.solicitud.nombre_completo }}
                </span>
                <span class="flex gap-1 items-center text-sm">
                    <Icon icon="envelope" />
                    {{ item.solicitud.correo }}
                </span>
                <span class="flex gap-1 items-center text-sm">
                    <Icon icon="phone" />
                    {{ formatVal(item.solicitud.telefono,'phone') }}
                </span>
            </div>
        </template>
        <template #actions="{item}">
            <Drop-Down 
                icon="ellipsis-vertical"
                :items="[
                    { label : 'Revisar solicitud', icon : 'file', action : () => store.view(item) }
                ]"
            />
        </template>
    </Data-Table>

    <Modal :open="store.modal.view" header="Solicitud" icon="bell-concierge" size="max-w-3xl" >
        <template #closed>
            <Icon @click="store.resetData" icon="xmark" class="cursor-pointer text-xl p-2 hover:bg-gray-800 rounded-lg" />
        </template>
        <ol class="flex justify-around">
            <li 
                @click="store.toggle(1)" 
                class="flex items-center gap-1 text-xs cursor-pointer hover:bg-gray-800 p-3 rounded-lg"
                :class="{'bg-gray-800' : store.option == 1}">
                <Icon icon="user-circle" class="text-2xl" />
                <p>Datos del solicitante</p>
            </li>
            <li 
                @click="store.toggle(2)" 
                class="flex items-center gap-1 text-xs cursor-pointer hover:bg-gray-800 p-3 rounded-lg"
                :class="{'bg-gray-800' : store.option == 2}">
                <Icon icon="map-location-dot" class="text-2xl" />
                <p>Espacio solicitado</p>
            </li>
            <li 
                @click="store.toggle(3)" 
                class="flex items-center gap-1 text-xs cursor-pointer hover:bg-gray-800 p-3 rounded-lg"
                :class="{'bg-gray-800' : store.option == 3}">
                <Icon icon="file" class="text-2xl" />
                <p>Documentos subidos</p>
            </li>
            <li
                @click="store.toggle(4)" 
                class="flex items-center gap-1 text-xs cursor-pointer hover:bg-gray-800 p-3 rounded-lg"
                :class="{'bg-gray-800' : store.option == 4}">
                <Icon icon="arrows-rotate" class="text-2xl" />
                <p>Cambiar estado</p>
            </li>
        </ol>
        <hr>
        <div>
            <fieldset v-if="store.option == 1" class="border-2 p-4 rounded-lg dark:border-gray-500">
                <legend class="px-3">DATOS DEL SOLICITANTE</legend>
                <div class="grid lg:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <Input v-model="store.solicitud.solicitud.nombre_completo" label="Nombre completo" icon="user" readonly disabled />
                    </div>
                    <Input v-model="store.solicitud.solicitud.cui" label="Cui" icon="address-card" readonly disabled />
                    <Input v-model="store.solicitud.solicitud.nit" label="Nit" icon="address-card" readonly disabled />
                    <Input v-model="store.solicitud.solicitud.correo" label="Correo" icon="envelope" readonly disabled />
                    <Input v-model="store.solicitud.solicitud.telefono" label="Teléfono" icon="phone" readonly disabled />
                    <Input v-model="store.solicitud.solicitud.tipo_persona.nombre" label="Tipo persona" icon="users" readonly disabled />
                    <Input v-model="store.solicitud.solicitud.patente_comercio" label="Patente de comercio" icon="address-card" readonly disabled />
                    <Input v-model="store.solicitud.solicitud.zona_id" label="Zona" icon="signs-post" readonly disabled />
                    <Input v-model="store.solicitud.solicitud.colonia" label="Colonia" icon="landmark" readonly disabled />
                    <div class="col-span-2">
                        <Input v-model="store.solicitud.solicitud.domicilio" label="Domicilio" icon="location-dot" readonly disabled />
                    </div>
                    <div class="col-span-2">
                        <Text-Area v-model="store.solicitud.solicitud.actividad_negocio" label="Actividad de negocio" rows="4" readonly disabled />
                    </div>
                </div>
            </fieldset>

            <fieldset v-if="store.option == 2" class="border-2 p-4 rounded-lg dark:border-gray-500">
                <legend class="px-3">ESPACIO SOLICITADO</legend>
                <div class="grid lg:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <Input v-model="store.solicitud.solicitud.sede.nombre" label="Sede" icon="map-location-dot" readonly disabled />
                    </div>
                    <Input v-model="store.solicitud.solicitud.ancho" label="Ancho (mts)" icon="ruler-vertical" readonly disabled />
                    <Input v-model="store.solicitud.solicitud.largo" label="Largo (mts)" icon="ruler-horizontal" readonly disabled />
                    <div class="col-span-2">
                        <Text-Area v-model="store.solicitud.solicitud.observaciones" label="Observaciones" rows="4" readonly disabled />
                    </div>
                </div>
            </fieldset>

            <fieldset v-if="store.option == 3" class="border-2 py-4 px-2 rounded-lg dark:border-gray-500 flex gap-4">
                <legend class="px-3">DOCUMENTOS</legend>
                <ul>
                    <template v-for="doc in store.solicitud?.solicitud?.documentos">
                        <li @click="store.previewDoc(doc.url)" 
                            class="text-xs cursor-pointer hover:bg-gray-800 px-2 py-2 rounded-lg">
    
                            <Icon icon="file-pdf" class="text-lg" />
                            <span>{{ doc.nombre }}</span>
    
                        </li>
                    </template>
                </ul>
                
                <iframe class="flex-1 h-[37rem] rounded-lg border-2 border-gray-500" :src="store.urlDoc" frameborder="0"></iframe>
            </fieldset>

            <div v-if="store.option == 4">
                <Text-Area v-model="store.solicitud.latest_workflow.observacion" label="Observación" rows="6" :error="hasErrorField(store.errors.rechazar,'observacion')" />
                <br>
                <Validate-Errors :errors="store.errors.rechazar" v-if="store.errors.rechazar != 0" />
                <div v-if="store.solicitud.latest_workflow.estado_id != 7" class="flex justify-evenly">
                    <Button @click="store.rejectRequest" text="Rechazar solicitud" icon="check" class="btn-red" :loading="store.loading.changeState" />
                    <Button @click="store.acceptRequest" text="Aceptar solicitud" icon="check" class="btn-alternative" :loading="store.loading.changeState" />
                </div>
            </div>
        </div>
        <template #footer>
            <Button @click="store.resetData" text="Cancelar" icon="xmark" class="btn-primary" />
            
        </template>
    </Modal>
</template>