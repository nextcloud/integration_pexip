<?php

/**
 * Nextcloud - Pexip
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 * @copyright Julien Veyssier 2023
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
