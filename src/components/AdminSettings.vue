<template>
	<div id="pexip_prefs" class="section">
		<h2>
			<PexipIcon />
			{{ t('integration_pexip', 'Pexip integration') }}
		</h2>
		<div id="pexip-content">
			<div class="line">
				<label for="pexip-url">
					<LinkIcon :size="20" class="icon" />
					{{ t('integration_pexip', 'Pexip base URL') }}
				</label>
				<input id="pexip-url"
					v-model="state.pexip_url"
					type="text"
					:placeholder="t('integration_pexip', 'https://...')"
					@input="onInput">
			</div>
			<p v-if="state.pexip_url" class="settings-hint">
				<InformationOutlineIcon :size="20" class="icon" />
				{{ meetingLinkHint }}
			</p>
			<p class="settings-hint">
				<InformationOutlineIcon :size="20" class="icon" />
				{{ configHint }}
			</p>
		</div>
	</div>
</template>

<script>
import InformationOutlineIcon from 'vue-material-design-icons/InformationOutline.vue'
import LinkIcon from 'vue-material-design-icons/Link.vue'

import PexipIcon from './icons/PexipIcon.vue'

import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { delay } from '../utils.js'
import { showSuccess, showError } from '@nextcloud/dialogs'

export default {
	name: 'AdminSettings',

	components: {
		PexipIcon,
		LinkIcon,
		InformationOutlineIcon,
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
	}
	h2 {
		display: flex;
		align-items: center;
		gap: 8px
	}
	h2,
	.line,
	.settings-hint {
		display: flex;
		align-items: center;
		margin-top: 12px;
		.icon {
			margin-right: 4px;
		}
	}

	.line {
		> label {
			width: 300px;
			display: flex;
			align-items: center;
		}
		> input {
			width: 300px;
		}
	}
}
</style>
