<script setup>
import { ref, watchEffect } from "vue"
import { useFloating, flip, shift, offset, autoUpdate } from "@floating-ui/vue"
import { onClickOutside } from "@vueuse/core"

const props = defineProps({
	icon: { type: String, default: "" },
	iconRight: { type: String, default: "" },
	text: { type: String, default: ""},
	img: { type: String, default: null },
	items: { type: Array, default: null },
	closeOnClickOutside: { type: Boolean, default: true },
	position: {
		type: String,
		default: "auto", // auto | bottom | top
		validator: (val) => ["auto", "bottom", "top"].includes(val),
	},
	variant : {
		type : String,
		default : 'btn-primary',
		validator : (val) => {
			return [
				'btn-primary',
				'btn-alternative',
				'btn-light',
				'btn-dark',
				'btn-green',
				'btn-red',
				'btn-yellow',
				'btn-purple'
			].includes(val)
		}
	}
})

const open = ref(false)
const reference = ref(null)
const floating = ref(null)

const { x, y, strategy, update } = useFloating(reference, floating, {
	placement: props.position === "top" ? "top" : "bottom", // 👈 centrado
	middleware: [
		offset(6),
		props.position === "auto" ? flip() : null,
		props.position === "auto" ? shift() : null,
	].filter(Boolean),
	whileElementsMounted: autoUpdate,
})

const toggle = () => {
	open.value = !open.value
	if (open.value) update()
}

onClickOutside(floating, () => {
	if (props.closeOnClickOutside) open.value = false
}, { ignore: [reference] })

watchEffect(() => {
	if (open.value) update()
})
</script>

<template>
	<div class="relative inline-block">
		<!-- Botón -->
		<Button ref="reference" @click="toggle" :text="props.text" :class="props.variant" :img="props.img"
			:iconRight="props.iconRight" :icon="props.icon" />

		<!-- Menú -->
		<Transition name="fade">
			<div v-if="open" ref="floating"
				class="z-10 bg-white dark:bg-gray-700 divide-y divide-gray-100 rounded-lg border-gray-300 border min-w-44" :style="{
					position: strategy,
					top: y != null ? `${y}px` : '',
					left: x != null ? `${x}px` : ''
				}">
				<template v-if="props.items">
					<ul class="p-2 text-sm dark:text-gray-200">
						<template v-for="(item, i) in props.items">
							<li v-if="item.can ?? true" :key="i">
								<a href="#" @click.prevent="item.action?.()"
									class="flex items-center rounded-xl gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
									<Icon v-if="item.icon" :icon="item.icon" />
									{{ item.label }}
								</a>
							</li>
						</template>
					</ul>
				</template>

				<template v-else>
					<ul class="py-2 text-sm dark:text-gray-200 px-4">
						<slot />
					</ul>
				</template>
			</div>
		</Transition>
	</div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
	transition: opacity 0.15s ease, transform 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
	opacity: 0;
	transform: translateY(-4px);
}
</style>
