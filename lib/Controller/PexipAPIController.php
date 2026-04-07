<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Pexip\Controller;

use OCA\Pexip\Service\PexipService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\DB\Exception;

use OCP\IRequest;

class PexipAPIController extends Controller {

	public function __construct(
		string $appName,
		IRequest $request,
		private PexipService $pexipService,
		private ?string $userId,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @PublicPage
	 * @NoCSRFRequired
	 *
	 * @param string $local_alias
	 * @return DataResponse
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function checkCall(string $local_alias): DataResponse {
		$response = $this->pexipService->checkCall($local_alias);
		if (isset($response['status']) && $response['status'] === 'fail') {
			return new DataResponse($response, Http::STATUS_NOT_FOUND);
		}
		return new DataResponse($response);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 * @throws Exception
	 */
	public function getUserCalls(): DataResponse {
		$response = $this->pexipService->getUserCalls($this->userId);
		return new DataResponse($response);
	}

	/**
	 * @param string $pexipId
	 * @return DataResponse
	 */
	public function deleteCall(string $pexipId): DataResponse {
		try {
			if ($this->pexipService->deleteCall($this->userId, $pexipId)) {
				return new DataResponse('');
			}
			return new DataResponse('', Http::STATUS_NOT_FOUND);
		} catch (Exception $e) {
			return new DataResponse($e->getMessage(), Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $description
	 * @param string $pin
	 * @param string $guestPin
	 * @param bool $guestsCanPresent
	 * @param bool $allowGuests
	 * @return DataResponse
	 */
	public function createCall(string $description, string $pin = '', string $guestPin = '',
		bool $guestsCanPresent = true, bool $allowGuests = true): DataResponse {
		$response = $this->pexipService->createCall($this->userId, $description, $pin, $guestPin, $guestsCanPresent, $allowGuests);
		if (isset($response['error'])) {
			return new DataResponse($response, Http::STATUS_BAD_REQUEST);
		}
		return new DataResponse($response);
	}
}
