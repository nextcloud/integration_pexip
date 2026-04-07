<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<span :aria-hidden="!title"
		:aria-label="title"
		class="material-design-icon pexip-icon"
		role="img"
		v-bind="$attrs"
		@click="$emit('click', $event)">
		<svg
			:fill="fillColor"
			:width="size"
			:height="size"
			enable-background="new 0 0 300 300"
			version="1.1"
			viewBox="0 0 300 300"
			xml:space="preserve"
			xmlns="http://www.w3.org/2000/svg">
			<path d="m51.5 276.38c-14.093-3.7955-24.193-14.012-27.892-28.215-1.9059-7.3171-1.9059-188.95 0-196.27 3.7468-14.385 13.872-24.51 28.257-28.257 7.3171-1.9059 188.95-1.9059 196.27 0 14.385 3.7468 24.51 13.872 28.257 28.257 1.9059 7.3171 1.9059 188.95 0 196.27-3.7468 14.385-13.872 24.51-28.257 28.257-7.0798 1.8441-189.78 1.8046-196.63-0.0425zm68.712-128.48v-65.959h-34.043v16.17h17.873v99.576h-17.873v16.17h34.043zm93.619 57.873v-8.0852h-17.873v-99.576h17.873v-16.17h-34.043v131.92h34.043z" />
		</svg>
	</span>
</template>

<script>
export default {
	name: 'PexipIcon',
	props: {
		title: {
			type: String,
			default: '',
		},
		fillColor: {
			type: String,
			default: 'currentColor',
		},
		size: {
			type: Number,
			default: 24,
		},
	},
}
</script>
