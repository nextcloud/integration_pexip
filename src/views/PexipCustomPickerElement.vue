<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<div class="pexip-picker-content-wrapper">
		<div class="pexip-picker-content">
			<h2>
				{{ t('integration_pexip', 'Pexip meetings') }}
			</h2>
			<div v-if="!showCreation"
				class="call-list-wrapper">
				<NcLoadingIcon v-if="loadingCalls" :size="20" />
				<div v-else-if="calls.length > 0" class="call-list">
					<PexipCall v-for="call in calls"
						:key="call.pexip_id"
						:call="call"
						:no-link="true"
						:deleteable="true"
						class="call"
						tabindex="0"
						@deleted="onCallDeleted(call.id)"
						@keydown.native.enter.prevent.stop="onSubmit(call.link)"
						@click.native="onSubmit(call.link)" />
				</div>
				<NcEmptyContent v-else
					:description="t('integration_pexip', 'No meetings found')">
					<template #icon>
						<PexipIcon />
					</template>
				</NcEmptyContent>
			</div>
			<div v-show="!showCreation" class="creation-toggle">
				<NcButton class="toggle-button"
					variant="tertiary"
					@click="showCreation = true">
					<template #icon>
						<PlusIcon />
					</template>
					{{ t('integration_pexip', 'Create a meeting') }}
				</NcButton>
			</div>
			<div v-show="showCreation" class="creation-form">
				<div class="line">
					<label for="desc">
						{{ t('integration_pexip', 'Name') }}
					</label>
					<NcRichContenteditable
						id="desc"
						v-model="description"
						:maxlength="3000"
						:placeholder="t('integration_pexip', 'Meeting name (max 3000 characters)')"
						:link-autocomplete="false" />
				</div>
				<div class="line">
					<NcPasswordField
						id="pin"
						v-model="pin"
						class="pinInput"
						:maxlength="20"
						:error="!isHostPinValid"
						:label="hostPinLabel"
						:label-visible="true"
						:helper-text="pinHelper" />
				</div>
				<div class="line">
					<NcCheckboxRadioSwitch
						v-model="allow_guests">
						{{ t('integration_pexip', 'Allow guests') }}
					</NcCheckboxRadioSwitch>
				</div>
				<div v-if="allow_guests" class="line">
					<NcPasswordField
						id="pin"
						v-model="guest_pin"
						class="pinInput"
						:maxlength="20"
						:disabled="!allow_guests"
						:error="!isGuestPinValid"
						:label="t('integration_pexip', 'Guest pin')"
						:label-visible="true"
						:helper-text="pinHelper" />
				</div>
				<div v-if="allow_guests" class="line">
					<NcCheckboxRadioSwitch
						v-model="guests_can_present">
						{{ t('integration_pexip', 'Guests can present') }}
					</NcCheckboxRadioSwitch>
				</div>
				<div class="creation-footer">
					<NcButton class="cancel-button"
						variant="secondary"
						:disabled="creating"
						@click="showCreation = false">
						{{ t('integration_pexip', 'Cancel') }}
					</NcButton>
					<NcButton class="create-button"
						variant="primary"
						:disabled="!canCreate"
						@click="onCreate">
						<template #icon>
							<NcLoadingIcon v-if="creating" />
							<ArrowRightIcon v-else />
						</template>
						{{ t('integration_pexip', 'Create') }}
					</NcButton>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import ArrowRightIcon from 'vue-material-design-icons/ArrowRight.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'

import PexipIcon from '../components/icons/PexipIcon.vue'

import NcButton from '@nextcloud/vue/components/NcButton'
import NcLoadingIcon from '@nextcloud/vue/components/NcLoadingIcon'
import NcCheckboxRadioSwitch from '@nextcloud/vue/components/NcCheckboxRadioSwitch'
import NcRichContenteditable from '@nextcloud/vue/components/NcRichContenteditable'
import NcEmptyContent from '@nextcloud/vue/components/NcEmptyContent'
import NcPasswordField from '@nextcloud/vue/components/NcPasswordField'

import PexipCall from '../components/PexipCall.vue'

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

export default {
	name: 'PexipCustomPickerElement',

	components: {
		PexipIcon,
		PexipCall,
		NcButton,
		NcLoadingIcon,
		NcRichContenteditable,
		NcCheckboxRadioSwitch,
		NcEmptyContent,
		NcPasswordField,
		PlusIcon,
		ArrowRightIcon,
	},

	props: {
		providerId: {
			type: String,
			required: true,
		},
		accessible: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			creating: false,
			loadingCalls: false,
			calls: [],
			showCreation: false,
			description: '',
			pin: '',
			allow_guests: false,
			guest_pin: '',
			guests_can_present: false,
			pinHelper: t('integration_pexip', 'Between 4 and 20 digits optionally endind with one or more #'),
		}
	},

	computed: {
		canCreate() {
			return !!this.description
				&& (!this.allow_guests || !!this.pin)
				&& this.isHostPinValid
				&& this.isGuestPinValid
		},
		isHostPinValid() {
			return this.isPinValid(this.pin)
		},
		isGuestPinValid() {
			return this.isPinValid(this.guest_pin)
		},
		hostPinLabel() {
			return (this.allow_guests && this.pin === '')
				? t('integration_pexip', 'Host pin (mandatory if you allow guests)')
				: t('integration_pexip', 'Host pin')
		},
	},

	watch: {
	},

	mounted() {
		this.getCalls()
	},

	methods: {
		isPinValid(value) {
			return value.length === 0
				|| (value.length <= 20
					&& value.length >= 4
					&& value.match(/[^0-9#]/) === null
					// # are only allowed at the end
					&& value.match(/^[^#]*#*$/) !== null)
		},
		getCalls() {
			this.loadingCalls = true
			const url = generateUrl('/apps/integration_pexip/calls')
			return axios.get(url)
				.then((response) => {
					this.calls = response.data
				})
				.catch((error) => {
					console.error('Pexip get calls error', error)
					showError(t('integration_pexip', 'Error getting the Pexip calls'))
				})
				.then(() => {
					this.loadingCalls = false
				})
		},
		onSubmit(url) {
			this.$emit('submit', url)
			this.$el.dispatchEvent(new CustomEvent('submit', { detail: url, bubbles: true }))
		},
		onCreate() {
			this.creating = true
			const params = {
				description: this.description,
				pin: this.pin,
				allowGuests: this.allow_guests,
				guestPin: this.guest_pin,
				guestsCanPresent: this.guests_can_present,
			}
			const url = generateUrl('/apps/integration_pexip/calls')
			return axios.post(url, params)
				.then((response) => {
					const link = response.data?.link
					this.onSubmit(link)
				})
				.catch((error) => {
					console.error('Pexip call creation error', error)
					showError(t('integration_pexip', 'Error creating the Pexip call'))
				})
				.then(() => {
					this.creating = false
				})
		},
		onCallDeleted(id) {
			const i = this.calls.findIndex(call => call.id === id)
			if (i !== -1) {
				this.calls.splice(i, 1)
			}
		},
	},
}
</script>

<style scoped lang="scss">
:deep(.rich-contenteditable__input--empty::before) {
	position: relative !important;
}

.pexip-picker-content-wrapper {
	width: 100%;
}

.pexip-picker-content {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	padding: 12px 16px 16px 16px;

	h2 {
		margin-top: 0;
		display: flex;
		align-items: center;
	}

	.spacer {
		flex-grow: 1;
	}

	.call-list {
		width: 100%;
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 8px;
		.call {
			width: 95%;
			border: 2px solid var(--color-border);
			border-radius: var(--border-radius-large);
			padding: 8px;
			cursor: pointer;
			:deep(*) {
				cursor: pointer;
			}
			&:focus,
			&:focus-visible,
			&:hover {
				//background-color: var(--color-background-hover);
				border: 2px solid var(--color-primary);
				outline: none;
				box-shadow: none;
			}
		}
	}

	.creation-toggle {
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: start;
		margin-top: 12px;
		> * {
			margin-left: 4px;
		}
	}

	.call-list-wrapper {
		width: 100%;
	}

	.creation-footer {
		width: 100%;
		margin-top: 8px;
		display: flex;
		align-items: center;
		justify-content: end;
		gap: 8px;
	}

	.creation-form {
		width: 100%;
		padding-top: 12px;
		display: flex;
		flex-direction: column;
		align-items: start;
		gap: 8px;

		.line {
			width: 100%;
			display: flex;
			flex-direction: column;

			input,
			select {
				width: 200px;
			}
		}

		input[type=number] {
			width: 80px;
			appearance: initial !important;
			-moz-appearance: initial !important;
			-webkit-appearance: initial !important;
		}

		#desc {
			width: 100%;
			min-height: 70px;
		}
		.pinInput {
			width: 300px;
		}
	}
}
</style>
