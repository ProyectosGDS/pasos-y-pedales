import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './assets/app.css'
import './services/axios'

import App from './App.vue'
import router from './router'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { far } from '@fortawesome/free-regular-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'

import Button from './components/Button.vue'
import DropDown from './components/DropDown.vue'
import ValidateErrors from './components/ValidateErrors.vue'
import Input from './components/Input.vue'
import Modal from './components/Modal.vue'
import Select from './components/Select.vue'
import DataTableServerSide from './components/DataTable/DataTableServerSide.vue'
import DataTable from './components/DataTable/DataTable.vue'
import TextArea from './components/TextArea.vue'
import Toggle from './components/Toggle.vue'
import Badge from './components/Badge.vue'

const app = createApp(App)

library.add(fas)
library.add(far)
library.add(fab)

app.component('Icon',FontAwesomeIcon)
.component('Badge', Badge)
.component('Button', Button)
.component('Input',Input)
.component('Text-Area',TextArea)
.component('Toggle',Toggle)
.component('Select', Select)
.component('Drop-Down',DropDown)
.component('Modal',Modal)
.component('DataTable-ServerSide',DataTableServerSide)
.component('Data-Table',DataTable)
.component('Validate-Errors',ValidateErrors)

app.use(createPinia())
app.use(router)

app.mount('#app')
