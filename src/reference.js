/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
import { registerWidget, registerCustomPickerElement, NcCustomPickerRenderResult } from '@nextcloud/vue/components/NcRichText'
import { linkTo } from '@nextcloud/router'
import { getCSPNonce } from '@nextcloud/auth'

__webpack_nonce__ = getCSPNonce() // eslint-disable-line
__webpack_public_path__ = linkTo('integration_pexip', 'js/') // eslint-disable-line

registerWidget('integration_pexip_call', async (el, { richObjectType, richObject, accessible }) => {
	const { createApp } = await import('vue')
	const { default: PexipReferenceWidget } = await import(/* webpackChunkName: "reference-pexip-lazy" */'./views/PexipReferenceWidget.vue')

	const app = createApp(
		PexipReferenceWidget,
		{
			richObjectType,
			richObject,
			accessible,
		},
	)
	app.mixin({ methods: { t, n } })
	app.mount(el)
}, () => {}, { hasInteractiveView: false })

registerCustomPickerElement('pexip-call', async (el, { providerId, accessible }) => {
	const { createApp } = await import('vue')
	const { default: PexipCustomPickerElement } = await import('./views/PexipCustomPickerElement.vue')

	const app = createApp(
		PexipCustomPickerElement,
		{
			providerId,
			accessible,
		},
	)
	app.mixin({ methods: { t, n } })
	app.mount(el)

	return new NcCustomPickerRenderResult(el, app)
}, (el, renderResult) => {
	renderResult.object.unmount()
}, 'normal')
