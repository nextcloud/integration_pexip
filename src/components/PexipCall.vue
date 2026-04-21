<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<div class="pexip-call">
		<div v-if="deleted" class="call-info">
			{{ t('integration_pexip', 'This Pexip meeting has been deleted') }}
		</div>
		<div v-else-if="call.error" class="call-info">
			{{ t('integration_pexip', 'This Pexip meeting does not exist') }}
		</div>
		<div v-else class="call-info">
			<div class="header">
				<PexipIcon :size="20" class="icon" />
				<span v-if="noLink"
					class="description">
					{{ call.description }}
				</span>
				<a v-else
					:href="callLink"
					target="_blank"
					class="description"
					:title="callLink">
					{{ call.description }}
				</a>
				<div v-if="!noLink" class="spacer" />
				<a v-if="!noLink"
					:href="callLink"
					target="_blank"
					class="joinButton"
					:title="callLink">
					<NcButton variant="primary">
						{{ t('integration_pexip', 'Join meeting') }}
					</NcButton>
				</a>
			</div>
			<div class="content">
				<div v-if="showCreator"
					class="creator">
					{{ createdByLabel }}
				</div>
				<div class="details">
					{{ detailsText }}
				</div>
			</div>
		</div>
		<div v-if="showDeleteButton" class="spacer" />
		<NcButton v-if="showDeleteButton"
			:title="t('integration_pexip', 'Delete meeting')"
			class="delete-button"
			@click.prevent.stop="onDelete">
			<template #icon>
				<NcLoadingIcon v-if="deleting" />
				<DeleteIcon v-else />
			</template>
		</NcButton>
	</div>
</template>

<script>
import DeleteIcon from 'vue-material-design-icons/Delete.vue'

import PexipIcon from './icons/PexipIcon.vue'

import NcButton from '@nextcloud/vue/components/NcButton'
import NcLoadingIcon from '@nextcloud/vue/components/NcLoadingIcon'

import { showError } from '@nextcloud/dialogs'
import { getCurrentUser } from '@nextcloud/auth'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export default {
	name: 'PexipCall',

	components: {
		PexipIcon,
		DeleteIcon,
		NcButton,
		NcLoadingIcon,
	},

	props: {
		call: {
			type: Object,
			required: true,
		},
		noLink: {
			type: Boolean,
			default: false,
		},
		deleteable: {
			type: Boolean,
			default: false,
		},
		showCreator: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			canDelete: getCurrentUser()?.uid === this.call.user_id,
			deleting: false,
			deleted: false,
		}
	},

	computed: {
		callLink() {
			const url = new URL(this.call.link)
			const user = getCurrentUser()
			if (user?.displayName) {
				url.searchParams.append('name', user.displayName)
			} else if (user?.uid) {
				url.searchParams.append('name', user.uid)
			}
			if (this.call.guest_pin) {
				url.searchParams.append('pin', this.call.guest_pin)
			}
			return url.href
		},
		guestAllowedText() {
			return this.call.allow_guests
				? t('integration_pexip', 'Guests allowed')
				: t('integration_pexip', 'No guest access')
		},
		guestCanPresentText() {
			return this.call.guests_can_present
				? t('integration_pexip', 'Guests can present')
				: t('integration_pexip', 'Guests cannot present')
		},
		hostPinText() {
			return this.call.pin
				? t('integration_pexip', 'Host pin')
				: t('integration_pexip', 'No host pin')
		},
		guestPinText() {
			return this.call.guest_pin
				? t('integration_pexip', 'Guests pin')
				: t('integration_pexip', 'No guests pin')
		},
		detailsText() {
			const elements = []
			if (this.call.pin && this.call.allow_guests && this.call.guest_pin) {
				elements.push(t('integration_pexip', 'Host & guest pin set'))
			} else if (!this.call.allow_guests) {
				elements.push(
					this.call.pin
						? t('integration_pexip', 'Host pin set')
						: t('integration_pexip', 'No host pin set'),
				)
			} else {
				// guests are allowed so host pin is necessarily set
				elements.push(t('integration_pexip', 'Host pin set & No guest pin'))
			}
			if (!this.call.allow_guests) {
				elements.push(t('integration_pexip', 'No guest access'))
			} else {
				elements.push(
					this.call.guests_can_present
						? t('integration_pexip', 'Guests can present')
						: t('integration_pexip', 'Guests cannot present'),
				)
			}
			return elements.join(' · ')
		},
		createdByLabel() {
			if (getCurrentUser()?.uid === this.call.user_id) {
				return t('integration_pexip', 'Pexip meeting created by you')
			}

			return t('integration_pexip', 'Pexip meeting created by {displayname}', {
				displayname: this.call.user_name,
			})
		},
		showDeleteButton() {
			return this.deleteable && this.canDelete && !this.deleted
		},
	},

	mounted() {
	},

	methods: {
		onDelete() {
			this.deleting = true
			const url = generateUrl('/apps/integration_pexip/calls/{id}', { id: this.call.pexip_id })
			return axios.delete(url)
				.then((response) => {
					this.deleted = true
					this.$emit('deleted')
				})
				.catch((error) => {
					console.error('Pexip delete call error', error)
					showError(t('integration_pexip', 'Error deleting the Pexip call'))
				})
				.then(() => {
					this.deleting = false
				})
		},
	},
}
</script>

<style scoped lang="scss">
.pexip-call {
	white-space: normal;
	display: flex;
	align-items: center;

	.call-info {
		width: 100%;
		display: flex;
		flex-direction: column;
		gap: 4px;

		.joinButton {
			margin-left: 12px;
		}
	}

	.header {
		display: flex;
		align-items: center;
		.icon {
			margin-right: 8px;
		}
	}
	a.hover {
		color: var(--color-primary);
	}
	.description {
		font-weight: bold;
	}
	.content {
		margin-left: 28px;
	}

	.delete-button {
		margin-left: 8px;
	}

	.spacer {
		flex-grow: 1;
	}
}
</style>
