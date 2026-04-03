<template>
	<div id="pexip_prefs" class="section">
		<h2>
			<PexipIcon />
			{{ t('integration_pexip', 'Pexip integration') }}
		</h2>
		<div id="pexip-content">
			<NcTextField
				id="pexip-url"
				v-model="state.pexip_url"
				class="input"
				:label="t('integration_openai', 'Pexip base URL')"
				:placeholder="t('integration_pexip', 'https://...')"
				:show-trailing-button="!!state.pexip_url"
				@update:model-value="onInput"
				@trailing-button-click="state.pexip_url = ''; onInput()">
				<template #icon>
					<EarthIcon :size="20" />
				</template>
			</NcTextField>
			<NcNoteCard v-if="state.pexip_url" type="info">
				{{ meetingLinkHint }}
			</NcNoteCard>
			<NcNoteCard type="info">
				{{ configHint }}
			</NcNoteCard>
		</div>
	</div>
</template>

<script>
import EarthIcon from 'vue-material-design-icons/Earth.vue'

import PexipIcon from './icons/PexipIcon.vue'

import NcNoteCard from '@nextcloud/vue/components/NcNoteCard'
import NcTextField from '@nextcloud/vue/components/NcTextField'

import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { delay } from '../utils.js'
import { showSuccess, showError } from '@nextcloud/dialogs'

export default {
	name: 'AdminSettings',

	components: {
		PexipIcon,
		EarthIcon,
		NcNoteCard,
		NcTextField,
	},

	props: [],

	data() {
		return {
			state: loadState('integration_pexip', 'admin-config'),
			policyUri: window.location.protocol + '//' + window.location.host + generateUrl('/apps/integration_pexip'),
		}
	},

	computed: {
		configHint() {
			return t('integration_pexip', 'On the Pexip side, the "policy server URI" must be set to {policyUri}', { policyUri: this.policyUri })
		},
		meetingLinkHint() {
			const linkExample = this.state.pexip_url + '/webapp3/m/MEETING_ID/express'
			return t('integration_pexip', 'Nextcloud will generate meeting links like {linkExample}', { linkExample })
		},
	},

	watch: {
	},

	mounted() {
	},

	methods: {
		onInput() {
			delay(() => {
				this.saveOptions({
					pexip_url: this.state.pexip_url,
				})
			}, 2000)()
		},
		saveOptions(values) {
			const req = {
				values,
			}
			const url = generateUrl('/apps/integration_pexip/admin-config')
			return axios.put(url, req)
				.then((response) => {
					showSuccess(t('integration_pexip', 'Pexip admin options saved'))
				})
				.catch((error) => {
					showError(
						t('integration_pexip', 'Failed to save Pexip admin options')
						+ ': ' + error.response?.request?.responseText,
					)
				})
		},
	},
}
</script>

<style scoped lang="scss">
#pexip_prefs {
	#pexip-content {
		margin-left: 40px;
		max-width: 800px;
	}
	h2 {
		display: flex;
		align-items: center;
		gap: 8px;
		justify-content: start;
	}
}
</style>
