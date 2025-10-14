<script setup>
    import { formatVal } from '@/helpers';
    import { useTimelineStore } from '@/stores/UnidadConvivenciaSocial/PasosPedales/timeline'

    const store = useTimelineStore()

</script>
<template>
    <DataTable-ServerSide :headers="store.headers" url="pasos-y-pedales/solicitudes/linea-de-tiempo">
        <template #tbody="{items}">
            <template v-for="item in items">
                <tr @click="store.toggleTimeLine(item)" class="cursor-pointer dark:hover:bg-gray-800 hover:bg-gray-200">
                    <td>{{ item.id }}</td>
                    <td>
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
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <Icon icon="address-card" />
                            <span>{{ item.solicitud.cui }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <Icon icon="address-card" />
                            <span>{{ item.solicitud.patente_comercio }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <Icon icon="users" />
                            <span>{{ item.solicitud.tipo_persona.nombre }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <Icon icon="location-dot" />
                            <span>{{ item.solicitud.sede.nombre }}</span>
                        </div>
                    </td>
                    <td>
                        {{ item.workflows[item.workflows.length - 1].estado.nombre }}
                    </td>
                </tr>
            </template>
        </template>
    </DataTable-ServerSide>

    <Modal :open="store.modal.timeline" header="Time Line" icon="timeline" size="max-w-3xl">
        <template #closed>
            <Icon @click="store.resetData" icon="xmark"
                class="cursor-pointer text-xl p-2 hover:bg-gray-800 rounded-lg" />
        </template>
        <div class="p-4 h-[30rem] overflow-auto">
            <ol class="border-l relative">
                <li v-for="workflow in store.expediente.workflows" class="-ml-4 mt-5">
                    <div class="flex gap-4">
                        <span class="size-8 bg-blue-500 rounded-full flex items-center justify-center absolute">
                            <Icon icon="calendar-days" class="" />
                        </span>
                        <div class="grid pl-12">
                            <span class="text-xl font-semibold">{{ workflow.estado.nombre }}</span>
                            <span>{{ workflow.user?.information?.small_name }}</span>
                            <span class="text-xs">{{ formatVal(workflow.created_at,'date') }}</span>
                            <p class="text-sm">{{ workflow.observacion }}</p>
                        </div>
                    </div>
                </li>
            </ol>
        </div>
        <template #footer>
            <Button @click="store.resetData" text="Cancelar" icon="xmark" class="btn-primary" />
        </template>
    </Modal>

</template>

<style scoped>
    @reference 'tailwindcss';

    td {
        @apply px-6 py-4;
    }
</style>