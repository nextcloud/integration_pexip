<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

return [
	'routes' => [
		['name' => 'config#setConfig', 'url' => '/config', 'verb' => 'PUT'],
		['name' => 'config#setAdminConfig', 'url' => '/admin-config', 'verb' => 'PUT'],
		['name' => 'pexipAPI#checkCall', 'url' => '/policy/v1/service/configuration', 'verb' => 'GET'],
		['name' => 'pexipAPI#createCall', 'url' => '/calls', 'verb' => 'POST'],
		['name' => 'pexipAPI#deleteCall', 'url' => '/calls/{pexipId}', 'verb' => 'DELETE'],
		['name' => 'pexipAPI#getUserCalls', 'url' => '/calls', 'verb' => 'GET'],
	],
];
