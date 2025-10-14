<script setup>
    import { formatVal, hasErrorField } from '@/helpers';
    import { useAutorizacionStore } from '@/stores/UnidadConvivenciaSocial/PasosPedales/autorizacion'
    import { onMounted } from 'vue';
    
    const store = useAutorizacionStore()

    onMounted(() => store.fetch() )

</script>
<template>
    <Data-Table :headers="store.headers" :data="store.expedientes" :loading="store.loading.fetch">
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

    <Modal :open="store.modal.view" header="Expediente" icon="bell-concierge" size="max-w-3xl" >
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
                <Icon icon="user-shield" class="text-2xl" />
                <p>Autorizaciones</p>
            </li>
        </ol>
        <hr>
        <div>
            <fieldset v-if="store.option == 1" class="border-2 p-4 rounded-lg dark:border-gray-500">
                <legend class="px-3">DATOS DEL SOLICITANTE</legend>
                <div class="grid lg:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <Input v-model="store.expediente.solicitud.nombre_completo" label="Nombre completo" icon="user" readonly disabled />
                    </div>
                    <Input v-model="store.expediente.solicitud.cui" label="Cui" icon="address-card" readonly disabled />
                    <Input v-model="store.expediente.solicitud.nit" label="Nit" icon="address-card" readonly disabled />
                    <Input v-model="store.expediente.solicitud.correo" label="Correo" icon="envelope" readonly disabled />
                    <Input v-model="store.expediente.solicitud.telefono" label="Teléfono" icon="phone" readonly disabled />
                    <Input v-model="store.expediente.solicitud.tipo_persona.nombre" label="Tipo persona" icon="users" readonly disabled />
                    <Input v-model="store.expediente.solicitud.patente_comercio" label="Patente de comercio" icon="address-card" readonly disabled />
                    <Input v-model="store.expediente.solicitud.zona_id" label="Zona" icon="signs-post" readonly disabled />
                    <Input v-model="store.expediente.solicitud.colonia" label="Colonia" icon="landmark" readonly disabled />
                    <div class="col-span-2">
                        <Input v-model="store.expediente.solicitud.domicilio" label="Domicilio" icon="location-dot" readonly disabled />
                    </div>
                    <div class="col-span-2">
                        <Text-Area v-model="store.expediente.solicitud.actividad_negocio" label="Actividad de negocio" rows="4" readonly disabled />
                    </div>
                </div>
            </fieldset>

            <div v-if="store.option == 2">
                <fieldset  class="border-2 p-4 rounded-lg dark:border-gray-500">
                    <legend class="px-3">ESPACIO SOLICITADO</legend>
                    <div class="grid lg:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <Input v-model="store.expediente.solicitud.sede.nombre" label="Sede" icon="map-location-dot" readonly disabled />
                        </div>
                        <Input v-model="store.expediente.solicitud.ancho" label="Ancho (mts)" icon="ruler-vertical" readonly disabled />
                        <Input v-model="store.expediente.solicitud.largo" label="Largo (mts)" icon="ruler-horizontal" readonly disabled />
                        <div class="col-span-2">
                            <Text-Area v-model="store.expediente.solicitud.observaciones" label="Observaciones" rows="4" readonly disabled />
                        </div>
                    </div>
                </fieldset>
                <br>
                <fieldset class="border-2 p-4 rounded-lg dark:border-blue-500">
                    <legend class="px-3 text-blue-500">ESPACIO ASIGNADO</legend>
                    <div class="grid lg:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <Input v-model="store.expediente.sede.nombre" label="Sede" icon="map-location-dot" readonly disabled />
                        </div>
                        <Input v-model="store.expediente.ancho" label="Ancho (mts)" type="number" min="1" icon="ruler-vertical" readonly disabled/>
                        <Input v-model="store.expediente.largo" label="Largo (mts)" type="number" min="1" icon="ruler-horizontal" readonly disabled/>
                        <div class="col-span-2">
                            <Text-Area v-model="store.expediente.descripcion" label="Descripción" rows="4" readonly disabled />
                        </div>
                    </div>
                </fieldset>
            </div>

            <fieldset v-if="store.option == 3" class="border-2 py-4 px-2 rounded-lg dark:border-gray-500 flex gap-4">
                <legend class="px-3">DOCUMENTOS</legend>
                <ul>
                    <template v-for="doc in store.expediente?.solicitud?.documentos">
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
                <Text-Area v-model="store.expediente.latest_workflow.observacion" label="Observación" rows="6" :error="hasErrorField(store.errors,'observacion')" />
                <br>
                <Validate-Errors :errors="store.errors" v-if="store.errors != 0" />
                <div v-if="store.expediente.latest_workflow.estado_id != 7" class="flex justify-evenly">
                    <Button @click="store.rejectRequest" text="Rechazar solicitud" icon="xmark" class="btn-red" :loading="store.loading.changeState" />
                    <Button @click="store.authorizedRequest" text="Autorizar solicitud" icon="check" class="btn-green" :loading="store.loading.changeState" />
                    <Button @click="store.rejectAssign" text="Rechazar asignación" icon="arrow-left" class="btn-light" :loading="store.loading.changeState" />
                </div>
            </div>
        </div>
        <template #footer>
            <Button @click="store.resetData" text="Cancelar" icon="xmark" class="btn-primary" />
        </template>
    </Modal>
</template>