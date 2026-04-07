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

namespace OCA\Pexip\Controller;

use OCA\Pexip\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use OCP\PreConditionNotMetException;

class ConfigController extends Controller {

	public function __construct(
		string $appName,
		IRequest $request,
		private IAppConfig $appConfig,
		private ?string $userId,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @NoAdminRequired
	 *
	 * Set config values
	 *
	 * @param array $values key/value pairs to store in config
	 * @return DataResponse
	 * @throws PreConditionNotMetException
	 */
	public function setConfig(array $values): DataResponse {
		foreach ($values as $key => $value) {
			$this->appConfig->setValueString($this->userId, Application::APP_ID, $key, $value);
		}
		return new DataResponse(1);
	}

	/**
	 * Set admin config values
	 *
	 * @param array $values key/value pairs to store in app config
	 * @return DataResponse
	 */
	public function setAdminConfig(array $values): DataResponse {
		foreach ($values as $key => $value) {
			$this->appConfig->setValueString(Application::APP_ID, $key, $value);
		}
		return new DataResponse(1);
	}
}
