<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<PexipCall
		:call="richObject.call"
		:show-creator="true"
		class="call-widget" />
</template>

<script>
import PexipCall from '../components/PexipCall.vue'

export default {
	name: 'PexipReferenceWidget',

	components: {
		PexipCall,
	},

	props: {
		richObjectType: {
			type: String,
			default: '',
		},
		richObject: {
			type: Object,
			default: null,
		},
		accessible: {
			type: Boolean,
			default: true,
		},
	},

	data() {
		return {
		}
	},

	computed: {
	},

	mounted() {
	},

	methods: {
	},
}
</script>

<style scoped lang="scss">
.call-widget {
	padding: 12px;
	width: 100%;
}
</style>
